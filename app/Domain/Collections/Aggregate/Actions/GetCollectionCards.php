<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Collections\Aggregate\DataObjects\CollectionCardData;
use App\Domain\Collections\Models\CollectionCardSummary;
use App\Support\Collection as SupportCollection;
use Illuminate\Support\Collection;

class GetCollectionCards
{
    public function __invoke(string $uuid)
    {
        $collectionCards = CollectionCardSummary::with('cardSearchDataObject')
            ->where('collection_uuid', '=', $uuid)
            ->where('quantity', '>', 0)
            ->get();

        return $this->format($collectionCards);
    }

    public function format(Collection $collectionCards) : SupportCollection
    {
        $collectionCards->transform(function ($card) {
            $cardData = $card->cardSearchDataObject;

            return (new CollectionCardData([
                'id'                => $cardData->id,
                'uuid'              => $cardData->card_uuid,
                'name'              => $cardData->card_name,
                'name_normalized'   => $cardData->card_name_normalized,
                'set'               => $cardData->set_code,
                'set_name'          => $cardData->set_name,
                'features'          => $cardData->features,
                'price'             => $card->current_price,
                'acquired_date'     => $card->date_added ?? null,
                'acquired_price'    => $card->price_when_added ?? null,
                'quantity'          => $card->quantity ?? null,
                'finish'            => $card->finish ?? null,
                'image'             => $cardData->image,
                'set_image'         => $cardData->set_image,
                'collector_number'  => $cardData->collector_number,
            ]))->toArray();
        });

        return new SupportCollection($collectionCards->toArray());
    }
}
