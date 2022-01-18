<?php

namespace Tests\Feature\Domain\Traits;

use App\Domain\Cards\Models\Card;
use App\Domain\Collections\Aggregate\Actions\CreateCollection;
use App\Domain\Collections\Aggregate\Actions\DeleteCollectionCards;
use App\Domain\Collections\Aggregate\Actions\UpdateCollectionCard;
use App\Domain\Collections\Models\Collection;
use App\Domain\Folders\Aggregate\Actions\CreateFolder;
use App\Domain\Folders\Models\Folder;

trait WithCollectionCards
{
    public function createCollection(string $folderUuid = '', bool $isPublic = false) : string
    {
        $params = [
            'description'   => $this->faker->sentence(),
            'name'          => $this->faker->words(2, true),
            'is_public'     => $isPublic,
        ];

        if ($folderUuid) {
            $params['folder_uuid'] = $folderUuid;
        }

        return (new CreateCollection)($params);
    }

    public function createCollectionCard(
        string $uuid = '',
        int $index = 0,
        string $finish = '',
        int $quantity = 1,
        string $condition = 'NM',
        ?int $acquired_price = null,
    ) : string {
        $card = Card::all()->get($index);
        $data = [
            'uuid'      => $uuid ?: $this->createCollection(),
            'change'    => [
                'id'                => $card->uuid,
                'finish'            => $finish ?: $card->finishes->first()->name,
                'change'            => $quantity,
                'condition'         => $condition,
                'acquired_price'    => $acquired_price,
            ],
        ];

        return (new UpdateCollectionCard)($data)['uuid'];
    }

    public function createCollectionInFolder(string $name = 'folder 01') : array
    {
        $folderUuid         = $this->createFolder($name);
        $collectionUuid     = $this->createCollection($folderUuid);

        return [
            'folder_uuid'       => $folderUuid,
            'collection_uuid'   => $collectionUuid,
        ];
    }

    public function createFolder(string $name = 'folder 01', ?string $parent = null) : string
    {
        $params = [
            'name' => $name,
        ];

        if ($parent) {
            $params['parent_uuid'] = $parent;
        }

        return (new CreateFolder)($params);
    }

    /**
     * @param string $collection the uuid of the collection to delete from
     * @param array $cards an array of collection cardSummaries to delete
     */
    public function deleteCards(string $collection, array $cards) : void
    {
        (new DeleteCollectionCards)($collection, $cards);
    }

    public function getCollectionSummary(Collection $collection) : array
    {
        $collection->refresh();
        $summary = $collection->summary;

        if ($summary) {
            return [
                'total_cards'       => $summary->total_cards,
                'current_value'     => $summary->current_value,
                'acquired_value'    => $summary->acquired_value,
                'gain_loss'         => $summary->gain_loss,
                'gain_loss_percent' => $summary->gain_loss_percent,
            ];
        }

        return [];
    }

    public function getFolderSummary(Folder $folder) : array
    {
        $folder->refresh();
        $summary = $folder->summary;

        if ($summary) {
            return [
                'total_cards'       => $summary->total_cards,
                'current_value'     => $summary->current_value,
                'acquired_value'    => $summary->acquired_value,
                'gain_loss'         => $summary->gain_loss,
                'gain_loss_percent' => $summary->gain_loss_percent,
            ];
        }

        return [];
    }

    public function getState(?Card $card = null, ?Collection $collection = null, ?Folder $folder = null) : array
    {
        $collection->refresh();
        $cards = $collection->cards()->get()->map(fn ($card) => $card->pivot);

        $response           = [];
        $collectionCards    = [];
        $pivot              = [];
        if ($card) {
            $collectionCards    = $collection->cards->where('uuid', '=', $card->uuid);
            $pivot              = $collectionCards->last()->pivot;
            $card->refresh();
        }

        $response['identity'] = [
            'card_uuid'       => $card->uuid ?? null,
            'collection_uuid' => $collection->uuid,
            'folder_uuid'     => $folder->uuid ?? null,
        ];

        $response['total_collection_cards'] = [
            'quantity'  => 0,
            'price'     => 0,
            'cards'     => 0,
        ];

        if ($cards) {
            $totals = $cards->reduce(function ($carry, $card) {
                return [
                    'quantity'  => $carry['quantity'] + $card->quantity,
                    'price'     => $carry['price'] +
                        ($card->quantity * $card->price_when_added),
                    'cards'     => $carry['cards'] + 1,
                ];
            }, $response['total_collection_cards']);

            $response['total_collection_cards'] = $totals;
        }

        if ($collectionCards) {
            $response['collection_cards'] = [
                'total_cards' => $collectionCards->count(),
            ];
        }

        if ($pivot) {
            $response['pivot'] = [
                'card_uuid'         => $pivot->card_uuid,
                'collection_uuid'   => $pivot->collection_uuid,
                'acquired_price'    => $pivot->price_when_added,
                'condition'         => $pivot->condition,
                'quantity'          => $pivot->quantity,
                'finish'            => $pivot->finish,
                'description'       => $pivot->description,
            ];
        }

        $response['collection'] = $this->getCollectionSummary($collection);

        $cardSummaries = $collection->cardSummaries
            ->where('quantity', '>', 0);
        $response['collection_card_summaries'] = [
            'total_cards'       => $cardSummaries->count(),
        ];

        $collectionCardSummary = $cardSummaries->last();
        if ($pivot && $pivot->quantity > 0) {
            $collectionCardSummary = $cardSummaries
                ->where('price_when_added', '=', $pivot->price_when_added)
                ->where('finish', '=', $pivot->finish)
                ->where('condition', '=', $pivot->condition ?: 'NM')
                ->first();
        }

        $response['collection_card_summary'] = [
            'acquired_price'    => $collectionCardSummary->price_when_added ?? null,
            // 'last_update'       => $collectionCardSummary->price_when_updated,
            'condition'         => $collectionCardSummary->condition ?? null,
            'quantity'          => $collectionCardSummary->quantity ?? null,
            'finish'            => $collectionCardSummary->finish ?? null,
        ];

        if ($folder) {
            $response['folder'] = $this->getFolderSummary($folder);
        }

        return $response;
    }

    public function updateCard(array $pivot, array $change = []) : array
    {
        $quantityChange     = $change['change'] ?? 0;
        $oldPrice           = $change['oldPrice'] ?? $pivot['acquired_price'];
        $newPrice           = $change['newPrice'] ?? round($oldPrice * 0.8);
        $newCondition       = $change['newCondition'] ?? 'LP';
        $oldCondition       = $change['oldCondition'] ?? $pivot['condition'];

        $data = [
            'acquired_price'    => $newPrice,
            'change'            => $quantityChange,
            'condition'         => $newCondition,
            'finish'            => $pivot['finish'],
            'id'                => $pivot['card_uuid'],
            'price'             => $newPrice,
            'quantity'          => $pivot['quantity'] + $quantityChange,
            'from'              => [
                'condition'         => $oldCondition,
                'finish'            => $pivot['finish'],
                'acquired_price'    => $oldPrice,
            ],
        ];

        (new UpdateCollectionCard)([
            'uuid'      => $pivot['collection_uuid'],
            'change'    => $data,
        ]);

        return $data;
    }
}
