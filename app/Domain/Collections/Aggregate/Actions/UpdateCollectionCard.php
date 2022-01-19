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

class UpdateCollectionCard
{
    protected array $change;

    protected string $uuid;

    public function __invoke(array $data)
    {
        $lock = Cache::lock('saving-collection-card', 20);

        try {
            $lock->block(15);

            try {
                $uuid         = $data['uuid'];
                $this->uuid   = $uuid;
                $this->change = $data['change'];
                $updated = $this->updateQuantity();
            } catch (Exception $e) {
                Cache::restoreLock('saving-collection-card', $lock)->release();
                throw $e;
            }

            $eventData    = [
                'uuid'          => $uuid,
                'change'        => $data['change'],
                'updated'       => $updated['collected'],
                'quantity_diff' => $updated['quantity_diff'],
                'from'          => $data['change']['from'] ?? [],
                'lock'          => $lock->owner(),
            ];

            CollectionAggregateRoot::retrieve($uuid)
                ->updateCollectionCard($eventData)
                ->persist();
        } catch (LockTimeoutException $e) {
            throw $e;
        }

        return $updated['card'];
    }

    protected function updateQuantity() : array
    {
        $requestedChange = $this->change['change'];
        $collectionUuid  = $this->uuid;

        $existingCard = CollectionCardSummary::where('collection_uuid', '=', $collectionUuid)
            ->where('card_uuid', '=', $this->change['id'])
            ->where('finish', '=', $this->change['finish'])
            ->first();

        $quantity         = optional($existingCard)->quantity ?: 0;
        $proposedQuantity = $quantity + $requestedChange;
        $actualChange     = $requestedChange;
        $finalQuantity    = $proposedQuantity;

        $searchData = new CollectionCardSearchParameterData([
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
        $formattedCards = (new FormatCards)($cardBuilder, $searchData);
        $formattedCard  = $formattedCards->first();
        if (!$formattedCard) {
            throw new Exception("no card found with that uuid");
        }

        if (!in_array($this->change['finish'], $formattedCard['finishes'])) {
            throw new Exception("invalid finish");
        }

        if (!$formattedCard['quantities'] && $this->change['change'] < 0) {
            throw new Exception('invalid quantity');
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
