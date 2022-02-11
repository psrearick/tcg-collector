<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Cards\Actions\FormatCards;
use App\Domain\Cards\DataObjects\CardSearchData;
use App\Domain\Cards\Models\Card;
use App\Domain\Collections\Aggregate\CollectionAggregateRoot;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardData;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchParameterData;
use App\Domain\Collections\Models\CollectionCardSummary;
use App\Domain\Collections\Services\CollectionCardSettingsService;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

class UpdateCollectionCard
{
    private array $change;

    private string $uuid;

    /**
     * @throws LockTimeoutException
     * @throws Exception
     */
    public function __invoke(array $data) : array
    {
        $uuid         = $data['uuid'];
        $this->uuid   = $uuid;
        $this->change = $data['change'];
        $updated      = $this->updateQuantity();
        $eventData    = [
            'uuid'          => $uuid,
            'change'        => $data['change'],
            'updated'       => $updated['collected'],
            'quantity_diff' => $updated['quantity_diff'],
            'from'          => $data['change']['from'] ?? [],
        ];

        $lock = Cache::lock('saving-collection-card', 20);

        $lock->block(15, function () use ($uuid, $eventData, $lock) {
            $eventData['lock'] = $lock->owner();
            CollectionAggregateRoot::retrieve($uuid)
                ->updateCollectionCard($eventData)
                ->persist();
        });

        return $updated['card'];
    }

    /**
     * @throws Exception
     */
    private function updateQuantity() : array
    {
        $requestedChange = $this->change['change'];
        $collectionUuid  = $this->uuid;

        $existingCard = CollectionCardSummary::where('collection_uuid', '=', $collectionUuid)
            ->where('card_uuid', '=', $this->change['id'])
            ->where('finish', '=', $this->change['finish'])
            ->first();

        $quantity           = optional($existingCard)->quantity ?: 0;
        $proposedQuantity   = $quantity + $requestedChange;
        $actualChange       = $requestedChange;
        $finalQuantity      = $proposedQuantity;
        $searchData         = new CollectionCardSearchParameterData([
            'uuid'      => $this->uuid,
            'single'    => true,
            'search'    => new CardSearchData(
                [
                    'uuid'   => $this->change['id'],
                    'finish' => $this->change['finish'],
                    'data'   => $existingCard ?? null,
                ]
            ),
        ]);

        $cardBuilder    = Card::where('uuid', '=', $this->change['id']);
        $formattedCard  = (new FormatCards())($cardBuilder, $searchData)->first();
        if (!$formattedCard) {
            throw new RuntimeException('no card found with that uuid');
        }

        if (!in_array($this->change['finish'], $formattedCard['finishes'], true)) {
            throw new RuntimeException('invalid finish');
        }

        if (!$formattedCard['quantities'] && $this->change['change'] < 0) {
            throw new RuntimeException('invalid quantity');
        }

        $price                  = $formattedCard['prices'][$this->change['finish']] ?? 0;
        $currentPrice           = $this->change['from']['acquired_price'] ?? $price;
        $changeAcquiredPrice    = $this->change['acquired_price'] ?? $currentPrice;
        $acquiredPrice          = CollectionCardSettingsService::tracksPrice()
            ? $changeAcquiredPrice
            : $currentPrice;

        $changeCondition = $this->change['condition'] ?? '';
        $condition       = CollectionCardSettingsService::tracksCondition()
            ? $changeCondition : '';

        $collectionCard                   = $formattedCard;
        $collectionCard['set']            = $formattedCard['set_code'];
        $collectionCard['finish']         = $this->change['finish'];
        $collectionCard['price']          = $price;
        $collectionCard['acquired_price'] = $acquiredPrice;
        $collectionCard['condition']      = $condition;
        $collectionCard['acquired_date']  = Carbon::today();
        $collectionCard['quantity']       = $finalQuantity;
        $collected                        = (new CollectionCardData($collectionCard))->toArray();

        $formattedCard['quantities'][$this->change['finish']] = $finalQuantity;

        return ['card' => $formattedCard, 'collected' => $collected, 'quantity_diff' => $actualChange];
    }
}
