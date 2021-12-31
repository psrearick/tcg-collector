<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Cards\Actions\FormatCards;
use App\Domain\Cards\DataObjects\CardSearchData;
use App\Domain\Collections\Aggregate\CollectionAggregateRoot;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardData;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchData;
use App\Domain\Collections\Aggregate\Queries\CollectionCardsSummary;
use App\Domain\Collections\Models\Collection;
use Carbon\Carbon;

class UpdateCollectionCard
{
    protected array $change;

    protected string $uuid;

    public function __invoke(array $data)
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
        ];

        CollectionAggregateRoot::retrieve($uuid)
            ->updateCollectionCard($eventData)
            ->persist();

        return $updated['card'];
    }

    protected function updateQuantity() : array
    {
        $requestedChange = $this->change['change'];

        $collectionCardSummary = new CollectionCardsSummary($this->uuid);
        $match                 = $collectionCardSummary->cards()[$this->change['id']] ?? null;

        if ($match) {
            $match = $match[$this->change['finish']] ?? null;
        }

        $quantity         = $match ?: 0;
        $proposedQuantity = $quantity + $requestedChange;
        $actualChange     = $requestedChange;
        $finalQuantity    = $proposedQuantity;
        if ($proposedQuantity < 0) {
            $actualChange  = $quantity;
            $finalQuantity = 0;
        }

        $searchData = new CollectionCardSearchData([
            'uuid'      => $this->uuid,
            'search'    => new CardSearchData(['uuid' => $this->change['id']]),
        ]);

        $searchCollectionCards = new SearchCollectionCards;
        $builder               = $searchCollectionCards($searchData)->builder ?: [];
        $formatCards           = new FormatCards;
        $formattedCards        = $formatCards($builder, $this->uuid);
        if (!$formatCards) {
            return [];
        }

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
