<?php

namespace App\Domain\Collections\Aggregate\Projectors\Traits;

use App\Domain\Collections\Models\Collection;
use App\Domain\Collections\Models\CollectionCardSummary;
use App\Domain\Collections\Services\CollectionCardSettingsService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait UpdatesCollectionCards
{
    protected function createCollectionCardSummary(array $attributes) : void
    {
        $collection = $attributes['uuid'];
        $card       = $attributes['change']['id'];
        $finish     = $attributes['change']['finish'];
        $quantity   = $attributes['quantity_diff'];
        $acquired   = $attributes['change']['acquired_price']
            ?: $attributes['updated']['acquired_price'];
        $condition  = $attributes['change']['condition'];
        $price      = $attributes['updated']['price'];

        if ($quantity < 1) {
            return;
        }

        if (!CollectionCardSettingsService::tracksPrice()) {
            $acquired = $price;
        }

        if (!CollectionCardSettingsService::tracksCondition()) {
            $condition = 'NM';
        }

        CollectionCardSummary::create([
            'collection_uuid'       => $collection,
            'card_uuid'             => $card,
            'price_when_added'      => $acquired,
            'price_when_updated'    => $price,
            'current_price'         => $price,
            'quantity'              => $quantity,
            'finish'                => $finish,
            'condition'             => $condition,
            'date_added'            => Carbon::now(),
        ]);
    }

    protected function findCollectionCardSummary(array $attributes) : Builder
    {
        $search = $this->getSearchAttributes($attributes);
        $from   = $this->getFromAttributes($attributes);

        $toMatchOn = $search['search'];

        foreach ($from as $attribute => $value) {
            if ($value) {
                $toMatchOn[$attribute] = $value;
            }
        }

        $summary = CollectionCardSummary::where('card_uuid', '=', $search['card'])
            ->where('collection_uuid', '=', $search['collection'])
            ->where('finish', '=', $toMatchOn['finish'])
            ->where('price_when_added', '=', $toMatchOn['acquired']);

        if ($toMatchOn['condition'] == 'NM') {
            $summary->where(function ($query) {
                $query->where('condition', '=', '')
                    ->orWhere('condition', '=', 'NM')
                    ->orWhereNull('condition');
            });
        } else {
            $summary->where('condition', '=', $toMatchOn['condition']);
        }

        if (isset($toMatchOn['quantity'])) {
            $summary->where('quantity', '=', $toMatchOn['quantity']);
        }

        return $summary;
    }

    protected function findTargetCollectionCardSummary(array $attributes) : Builder
    {
        $search    = $this->getSearchAttributes($attributes);
        $toMatchOn = $search['search'];

        $summary = CollectionCardSummary::where('card_uuid', '=', $search['card'])
            ->where('collection_uuid', '=', $search['collection'])
            ->where('finish', '=', $toMatchOn['finish'])
            ->where('price_when_added', '=', $toMatchOn['acquired']);

        if ($toMatchOn['condition'] == 'NM') {
            $summary->where(function ($query) {
                $query->where('condition', '=', '')
                    ->orWhere('condition', '=', 'NM')
                    ->orWhereNull('condition');
            });
        } else {
            $summary->where('condition', '=', $toMatchOn['condition']);
        }

        return $summary;
    }

    protected function getFromAttributes(array $attributes)
    {
        if (!$attributes['from']) {
            return [];
        }

        $values = [
            'finish'     => $attributes['from']['finish'],
            'acquired'   => $attributes['from']['acquired_price'],
            'condition'  => $attributes['from']['condition'],
        ];

        if (!isset($attributes['from']['quantity']))
        {
            return $values;
        }
    }

    protected function getSearchAttributes(array $attributes)
    {
        $collection = $attributes['uuid'];
        $card       = $attributes['change']['id'];

        $search = [
            'finish'     => $attributes['change']['finish'],
            'acquired'   => $attributes['change']['acquired_price']
                ?: $attributes['updated']['acquired_price'],
            'condition'  => $attributes['change']['condition'] ?: 'NM',
        ];

        return [
            'card'          => $card,
            'collection'    => $collection,
            'search'        => $search,
        ];
    }

    protected function hasChange(array $attributes) : bool
    {
        return true;
    }

    protected function isValidUpdate(array $attributes) : bool
    {
        $collection = Collection::uuid($attributes['uuid']);
        $summary    = $this->findCollectionCardSummary($attributes)->get();
        if ($summary->first()) {
            return ($summary->sum('quantity') + $attributes['quantity_diff']) > -1;
        }

        return $attributes['quantity_diff'] > -1;
    }

    protected function shouldUpdateCollectionCardSummary(array $attributes) : bool
    {
        $summary = $this->findCollectionCardSummary($attributes);

        return $summary->count() > 0;
    }

    protected function updateCollectionCardSummary(array $attributes) : void
    {
        $summary = $this->findCollectionCardSummary($attributes)->first();
        if (!$summary) {
            return;
        }
        $change = $attributes['quantity_diff'];
        $target = $this->findTargetCollectionCardSummary($attributes)->first();

        if ($target && $target->id != $summary->id) {
            $change += $target->quantity;
            $target->delete();
        }

        $summary->update([
            'price_when_added'      => $attributes['change']['acquired_price']
                ?: $attributes['updated']['acquired_price'],
            'price_when_updated'    => $attributes['updated']['price'],
            'current_price'         => $attributes['updated']['price'],
            'condition'             => $attributes['change']['condition'] ?: 'NM',
            'quantity'              => $summary->quantity + $change,
        ]);
    }
}
