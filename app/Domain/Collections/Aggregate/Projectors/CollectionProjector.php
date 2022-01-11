<?php

namespace App\Domain\Collections\Aggregate\Projectors;

use App\Domain\Collections\Aggregate\Events\CollectionCardsDeleted;
use App\Domain\Collections\Aggregate\Events\CollectionCardsMoved;
use App\Domain\Collections\Aggregate\Events\CollectionCardUpdated;
use App\Domain\Collections\Aggregate\Events\CollectionCreated;
use App\Domain\Collections\Aggregate\Events\CollectionDeleted;
use App\Domain\Collections\Aggregate\Events\CollectionMoved;
use App\Domain\Collections\Aggregate\Events\CollectionUpdated;
use App\Domain\Collections\Models\Collection;
use App\Domain\Collections\Models\CollectionCardSummary;
use App\Domain\Collections\Services\CollectionCardSettingsService;
use App\Domain\Folders\Models\Folder;
use App\Domain\Prices\Aggregate\Actions\GetCollectionTotals;
use App\Domain\Prices\Aggregate\Actions\UpdateCollectionAncestryTotals;
use App\Domain\Prices\Aggregate\Actions\UpdateFolderAncestryTotals;
use Brick\Money\Money;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

class CollectionProjector extends Projector
{
    public function onCollectionCardsDeleted(CollectionCardsDeleted $event) : void
    {
        $cards          = $event->cards;
        $collectionUuid = $event->uuid;
        $collection     = Collection::uuid($collectionUuid);

        foreach ($cards as $card) {
            $collection->cards()
                ->where('uuid', '=', $card['uuid'])
                ->wherePivot('finish', '=', $card['finish'])
                ->detach();

            CollectionCardSummary::where('collection_uuid', '=', $collectionUuid)
                ->where('card_uuid', '=', $card['uuid'])
                ->where('finish', '=', $card['finish'])
                ->delete();
        }

        (new UpdateCollectionAncestryTotals)($collection);
    }

    public function onCollectionCardsMoved(CollectionCardsMoved $event) : void
    {
        $cards           = $event->cards;
        $originUuid      = $event->uuid;
        $origin          = Collection::uuid($originUuid);
        $destinationUuid = $event->destination;
        $destination     = Collection::uuid($destinationUuid);

        foreach ($cards as $card) {

            // Update pivot values
            $collectionCards = $origin->cards()
                ->where('uuid', '=', $card['uuid'])
                ->wherePivot('finish', '=', $card['finish'])
                ->get();

            foreach ($collectionCards as $collectionCard) {
                $collectionCard->collections()
                    ->updateExistingPivot($origin, ['collection_uuid' => $destinationUuid], true);
            }

            // move card summary data
            $originCard = CollectionCardSummary::where('collection_uuid', '=', $originUuid)
                ->where('card_uuid', '=', $card['uuid'])
                ->where('finish', '=', $card['finish'])
                ->first();

            $destinationCard = CollectionCardSummary::where('collection_uuid', '=', $destinationUuid)
                ->where('card_uuid', '=', $card['uuid'])
                ->where('finish', '=', $card['finish'])
                ->first();

            if ($destinationCard) {
                $destinationCard->update([
                    'quantity' => $destinationCard->quantity + $card['quantity'],
                ]);

                $originCard->delete();
            } else {
                $originCard->update([
                    'collection_uuid' => $destinationUuid,
                ]);
            }
        }

        $destinationTotals = (new GetCollectionTotals)($destination);
        $destination->summary->update($destinationTotals);
        $destinationFolder = $destination->folder;
        if ($destinationFolder) {
            (new UpdateFolderAncestryTotals)($destinationFolder);
        }

        $originTotals = (new GetCollectionTotals)($origin);
        $origin->summary->update($originTotals);
        $originFolder = $origin->folder;
        if ($originFolder) {
            (new UpdateFolderAncestryTotals)($originFolder);
        }
    }

    public function onCollectionCardUpdated(CollectionCardUpdated $event) : void
    {
        $attributes = $event->collectionCardAttributes;
        $this->updateCollectionCard($attributes);
        $this->updateCollectionCardSummary($attributes);
        Cache::restoreLock('saving-collection-card', $attributes['lock'])->release();
    }

    public function onCollectionCreated(CollectionCreated $event) : void
    {
        $attributes = $event->collectionAttributes;
        $collection = Collection::create([
            'uuid'          => $attributes['uuid'],
            'name'          => $attributes['name'],
            'description'   => $attributes['description'],
            'is_public'     => $attributes['is_public'],
            'user_id'       => $attributes['user_id'],
            'folder_uuid'   => $attributes['folder_uuid'],
        ]);

        if ($attributes['groups']) {
            $collection->groups()->sync($attributes['groups']);
        }
    }

    public function onCollectionDeleted(CollectionDeleted $event) : void
    {
        $collection = Collection::uuid($event->aggregateRootUuid());
        $folderUuid = $collection->folder_uuid;
        $collection->delete();

        if ($folderUuid) {
            $folder = Folder::uuid($folderUuid);
            if ($folder) {
                (new UpdateFolderAncestryTotals)($folder);
            }
        }
    }

    public function onCollectionMoved(CollectionMoved $event) : void
    {
        $collection         = Collection::uuid($event->uuid);
        $destinationUuid    = $event->destination;
        $originalParentUuid = $collection->folder_uuid;

        $collection->update([
            'folder_uuid' => $destinationUuid,
        ]);

        if ($destinationUuid) {
            (new UpdateFolderAncestryTotals)(Folder::uuid($destinationUuid));
        }

        if ($originalParentUuid) {
            (new UpdateFolderAncestryTotals)(Folder::uuid($originalParentUuid));
        }
    }

    public function onCollectionUpdated(CollectionUpdated $event) : void
    {
        $attributes = $event->collectionAttributes;
        $collection = Collection::uuid($attributes['uuid']);
        $collection->update([
            'name'          => $attributes['name'],
            'description'   => $attributes['description'],
            'is_public'     => $attributes['is_public'],
        ]);

        if ($attributes['groups']) {
            $collection->groups()->sync($attributes['groups']);
        }
    }

    private function updateCollectionCard(array $attributes) : void
    {
        Collection::uuid($attributes['uuid'])->cards()
            ->attach($attributes['updated']['id'], [
                'collection_uuid'  => $attributes['uuid'],
                'card_uuid'        => $attributes['updated']['uuid'],
                'price_when_added' => $attributes['updated']['acquired_price'],
                'quantity'         => $attributes['quantity_diff'],
                'finish'           => $attributes['updated']['finish'],
                'condition'        => $attributes['updated']['condition'],
                'date_added'       => Carbon::now(),
            ]);
    }

    private function updateCollectionCardSummary(array $attributes) : void
    {
        $cardUuid   = $attributes['updated']['uuid'];
        $finish     = $attributes['updated']['finish'];
        $change     = $attributes['quantity_diff'];

        $acquired   = $attributes['updated']['acquired_price'];
        $price      = $attributes['updated']['price'] ?? $acquired;
        $fromPrice  = isset($attributes['from']) ? ($attributes['from']['acquired_price'] ?? $acquired) : $acquired;
        // $acquired   = Money::of($acquired, 'USD')->getMinorAmount()->toInt();
        // $price      = Money::of($price, 'USD')->getMinorAmount()->toInt();
        // $fromPrice  = Money::of($fromPrice, 'USD')->getMinorAmount()->toInt();

        $condition  = $attributes['updated']['condition'];
        $fromCond   = isset($attributes['from']) ? ($attributes['from']['condition'] ?? $condition) : $condition;
        $fromCond   = $fromCond ?: 'NM';

        $existingCard = CollectionCardSummary::where('collection_uuid', '=', $attributes['uuid'])
            ->where('card_uuid', '=', $cardUuid)
            ->where('finish', '=', $finish);

        if (CollectionCardSettingsService::tracksCondition()) {
            $existingCard->where('condition', $fromCond);
        }

        if (CollectionCardSettingsService::tracksPrice()) {
            $existingCard->where('price_when_added', $fromPrice);
        }

        $existingCard = $existingCard->get()->first();

        if (!$existingCard) {
            if (CollectionCardSettingsService::tracksPrice() && $fromCond == 'NM') {
                $existingCard = CollectionCardSummary::where('collection_uuid', '=', $attributes['uuid'])
                    ->where('card_uuid', '=', $cardUuid)
                    ->where('finish', '=', $finish)
                    ->whereNull('condition');

                if (CollectionCardSettingsService::tracksPrice()) {
                    $existingCard->where('price_when_added', $fromPrice);
                }

                $existingCard = $existingCard->get()->first();
            }
        }

        $targetCard   = CollectionCardSummary::where('collection_uuid', '=', $attributes['uuid'])
            ->where('card_uuid', '=', $cardUuid)
            ->where('finish', '=', $finish);

        if (CollectionCardSettingsService::tracksCondition()) {
            $targetCard->where('condition', $condition);
        }

        if (CollectionCardSettingsService::tracksPrice()) {
            $targetCard->where('price_when_added', $acquired);
        }

        $targetCard = $targetCard->get()->first();

        if (!$targetCard) {
            if (CollectionCardSettingsService::tracksPrice() && $condition == 'NM') {
                $targetCard   = CollectionCardSummary::where('collection_uuid', '=', $attributes['uuid'])
                    ->where('card_uuid', '=', $cardUuid)
                    ->where('finish', '=', $finish)
                    ->whereNull('condition');

                if (CollectionCardSettingsService::tracksPrice()) {
                    $targetCard->where('price_when_added', $acquired);
                }

                $targetCard = $targetCard->get()->first();
            }
        }

        if (!$existingCard) {
            CollectionCardSummary::create([
                'collection_uuid'       => $attributes['uuid'],
                'card_uuid'             => $cardUuid,
                'price_when_added'      => $acquired,
                'price_when_updated'    => $price,
                'current_price'         => $price,
                'quantity'              => $change,
                'finish'                => $finish,
                'condition'             => $condition,
                'date_added'            => Carbon::now(),
            ]);

            return;
        }

        if ($existingCard && $targetCard && $existingCard->id != $targetCard->id) {
            $change = $change + $targetCard->quantity;
            $targetCard->delete();
        }

        $existingCard->update([
            'price_when_added'      => $acquired,
            'price_when_updated'    => $price,
            'current_price'         => $price,
            'condition'             => $condition,
            'quantity'              => $existingCard->quantity + $change,
        ]);
    }
}
