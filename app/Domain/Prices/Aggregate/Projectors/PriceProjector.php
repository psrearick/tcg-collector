<?php

namespace App\Domain\Prices\Aggregate\Projectors;

use App\Domain\Prices\Aggregate\Actions\GetCollectionTotals;
use App\Domain\Prices\Aggregate\Actions\GetFolderTotals;
use App\Domain\Prices\Aggregate\Events\PriceCreated;
use App\Domain\Prices\Models\Price;
use Illuminate\Support\Facades\Log;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class PriceProjector extends Projector
{
    public function onPriceProviderCreated(PriceCreated $priceCreated) : void
    {
        $attributes = $priceCreated->priceAttributes;

        $price = Price::create([
            'card_uuid'     => $attributes['card_uuid'],
            'provider_uuid' => $attributes['provider_uuid'],
            'price'         => $attributes['price'],
            'foil'          => $attributes['foil'] ?? false,
            'type'          => $attributes['type'],
        ]);

        $collections = $price->card->collections->unique('uuid');

        $collections->each(function ($collection) {
            $getCollectionTotals = new GetCollectionTotals;
            $collectionTotals = $getCollectionTotals($collection);
            $collectionTotals['type'] = 'collection';
            $collection->summary()->updateOrCreate([
                'uuid' => $collection->uuid,
            ], $collectionTotals);

            $folder = $collection->folder;
            if ($folder) {
                $getFolderTotals = new GetFolderTotals;
                $folderTotals = $getFolderTotals($folder, true);
                $folderTotals['type'] = 'folder';
                $folder->summary()->updateOrCreate([
                    'uuid' => $folder->uuid,
                ], $folderTotals);

                $ancestors = $folder->ancestors;
                if ($ancestors) {
                    $ancestors->each(function ($ancestor) {
                        $getAncestorTotals = new GetFolderTotals;
                        $ancestorTotals = $getAncestorTotals($ancestor, true);
                        $ancestor['type'] = 'folder';
                        $ancestor->summary()->updateOrCreate([
                            'uuid' => $ancestor->uuid,
                        ], $ancestorTotals);
                    });
                }
            }
        });
    }
}
