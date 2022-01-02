<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Cards\Actions\FormatCards;
use App\Domain\Cards\DataObjects\CardSearchData;
use App\Domain\Cards\Models\Card;
use App\Domain\Collections\Aggregate\CollectionAggregateRoot;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardData;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchData;
use App\Domain\Collections\Models\CollectionCardSummary;
use Carbon\Carbon;
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

            $uuid         = $data['uuid'];
            $this->uuid   = $uuid;
            $this->change = $data['change'];
            $updated      = $this->updateQuantity();
            $eventData    = [
                'uuid'          => $uuid,
                'change'        => $data['change'],
                'updated'       => $updated['collected'],
                'quantity_diff' => $updated['quantity_diff'],
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
        if ($proposedQuantity < 0) {
            $actualChange  = $quantity;
            $finalQuantity = 0;
        }

        $searchData = new CollectionCardSearchData([
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

        $formattedCard                    = $formattedCards->first();
        $collectionCard                   = $formattedCard;
        $collectionCard['set']            = $formattedCard['set_code'];
        $collectionCard['finish']         = $this->change['finish'];
        $collectionCard['price']          = $formattedCard['prices'][$this->change['finish']];
        $collectionCard['acquired_price'] = $formattedCard['prices'][$this->change['finish']];
        $collectionCard['acquired_date']  = Carbon::today();
        $collectionCard['quantity']       = $finalQuantity;
        $collected                        = (new CollectionCardData($collectionCard))->toArray();

        $formattedCard['quantities'][$this->change['finish']] = $finalQuantity;

        return ['card' => $formattedCard, 'collected' => $collected, 'quantity_diff' => $actualChange];
    }
}
