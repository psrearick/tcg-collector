<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Cards\Actions\BuildCard;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardData;
use App\Domain\Collections\Models\CollectionCardSummary;
use App\Support\Collection as SupportCollection;
use Illuminate\Support\Collection;

class GetCollectionCards
{
    public function __invoke(string $uuid)
    {
        $collectionCards = CollectionCardSummary::with('card', 'card.frameEffects', 'card.set')
            ->where('collection_uuid', '=', $uuid)
            ->where('quantity', '>', 0)
            ->get();

        return $this->format($collectionCards);
    }

    public function format(Collection $collectionCards) : SupportCollection
    {
        $collectionCards->transform(function ($card) {
            $cardBuilder = new BuildCard($card->card);
            $build = $cardBuilder
                    ->add('feature')
                    ->add('image_url')
                    ->add('set_image_url')
                    ->get();

            return (new CollectionCardData([
                'id'                => $build->id,
                'uuid'              => $build->uuid,
                'name'              => $build->name,
                'name_normalized'   => $build->name_normalized,
                'set'               => optional($build->set)->code,
                'set_name'          => optional($build->set)->name,
                'features'          => $build->feature,
                'price'             => $card->current_price,
                'acquired_date'     => $card->date_added ?? null,
                'acquired_price'    => $card->price_when_added ?? null,
                'quantity'          => $card->quantity ?? null,
                'finish'            => $card->finish ?? null,
                'image'             => $build->image_url,
                'set_image'         => $build->set_image_url,
                'collector_number'  => $build->collectorNumber,
            ]))->toArray();
        });

        return new SupportCollection($collectionCards->toArray());
    }
}
