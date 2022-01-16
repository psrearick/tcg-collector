<?php

namespace Tests\Feature\Domain\Traits;

use App\Domain\Cards\Models\Card;
use App\Domain\Collections\Aggregate\Actions\CreateCollection;
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

    public function createCollectionInFolder() : array
    {
        $folderUuid         = $this->createFolder();
        $collectionUuid     = $this->createCollection($folderUuid);

        return [
            'folder_uuid'       => $folderUuid,
            'collection_uuid'   => $collectionUuid,
        ];
    }

    public function createFolder() : string
    {
        $params = [
            'name' => 'folder 01',
        ];

        return (new CreateFolder)($params);
    }

    public function getState(Card $card, Collection $collection, ?Folder $folder = null) : array
    {
        $collection->refresh();
        $card->refresh();
        $collectionCards = $collection->cards->where('uuid', '=', $card->uuid);
        $pivot           = $collectionCards->last()->pivot;

        $response = [];

        $response['identity'] = [
            'card_uuid'       => $card->uuid,
            'collection_uuid' => $collection->uuid,
            'folder_uuid'     => $folder->uuid ?? null,
        ];

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

        $summary                = $collection->summary;
        $response['collection'] = [
            'total_cards'       => $summary->total_cards,
            'current_value'     => $summary->current_value,
            'acquired_value'    => $summary->acquired_value,
            'gain_loss'         => $summary->gain_loss,
            'gain_loss_percent' => $summary->gain_loss_percent,
        ];

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
            $folder->refresh();
            $summary            = $folder->summary;

            if ($summary) {
                $response['folder'] = [
                    'total_cards'       => $summary->total_cards,
                    'current_value'     => $summary->current_value,
                    'acquired_value'    => $summary->acquired_value,
                    'gain_loss'         => $summary->gain_loss,
                    'gain_loss_percent' => $summary->gain_loss_percent,
                ];
            }
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
