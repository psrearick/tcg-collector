<?php

namespace App\Domain\Collections\Aggregate\Projectors\Services;

use App\Domain\Collections\Aggregate\Actions\UpdateCollectionCard;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardEventData;
use App\Domain\Collections\Aggregate\Projectors\Actions\CreateCollectionCard;
use App\Domain\Collections\Models\CollectionCardSummary;
use App\Domain\Collections\Services\CollectionCardSettingsService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class UpdateCollectionCardsService
{
    private ?CollectionCardEventData $data = null;

    private array $fromData = [];

    private ?CollectionCardEventData $summaryData = null;

    public function createCollectionCard() : self
    {
        (new CreateCollectionCard)($this->data->toCollectionCard());

        return $this;
    }

    public function createCollectionCardSummary() : self
    {
        $summary = $this->summaryData;

        if ($summary->quantity < 1) {
            return $this;
        }

        if (!CollectionCardSettingsService::tracksPrice()) {
            $summary->acquired = $summary->price;
        }

        if (!CollectionCardSettingsService::tracksCondition()) {
            $summary->condition = 'NM';
        }

        CollectionCardSummary::create($summary->toCollectionCardSummary());

        return $this;
    }

    public function createCollectionCardWithEvent() : self
    {
        (new UpdateCollectionCard)($this->data->toCollectionCardEvent());

        return $this;
    }

    public function findCollectionCardSummary() : Builder
    {
        $search = $this->getSearchAttributes();
        $from   = $this->getFromAttributes();

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

    public function findTargetCollectionCardSummary() : Builder
    {
        $search    = $this->getSearchAttributes();
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

    public function getAttribute(string $attribute) : mixed
    {
        return $this->data->{$attribute};
    }

    public function getData() : CollectionCardEventData
    {
        return $this->data;
    }

    public function getFromAttributes() : array
    {
        if (!count($this->fromData)) {
            return [];
        }

        $values = [
            'finish'     => $this->fromData['finish'],
            'acquired'   => $this->fromData['acquired'],
            'condition'  => $this->fromData['condition'],
        ];

        if (!isset($this->fromData['quantity'])) {
            return $values;
        }

        $values['quantity'] = $this->fromData['quantity'];

        return $values;
    }

    public function isValidUpdate() : bool
    {
        $summary    = $this->findCollectionCardSummary()->get();
        if ($summary->first()) {
            return ($summary->sum('quantity') + $this->data->quantity) > -1;
        }

        return $this->data->quantity > -1;
    }

    public function removeCollectionCard() : self
    {
        $remove             = $this->data;
        $remove->quantity   = $remove->quantity * -1;
        (new CreateCollectionCard)($remove->toCollectionCard());

        return $this;
    }

    public function removeCollectionCardWithEvent() : self
    {
        $data           = $this->data;
        $data->quantity = $data->quantity * -1;

        (new UpdateCollectionCard)($data->toCollectionCardEvent());

        return $this;
    }

    public function setAttribute(string $attribute, mixed $value) : self
    {
        $this->data->{$attribute} = $value;

        return $this;
    }

    public function setCollectionCardFromEventAttributes(array $attributes, bool $now = true) : self
    {
        $from = $attributes['from'] ?? [];

        $this->data = new CollectionCardEventData([
            'acquired'          => $attributes['updated']['acquired_price'],
            'card'              => $attributes['updated']['uuid'],
            'collection'        => $attributes['uuid'],
            'condition'         => $attributes['updated']['condition'] ?: 'NM',
            'date_added'        => $now ? Carbon::now() : $attributes['updated']['date_added'],
            'finish'            => $attributes['updated']['finish'],
            'price'             => $attributes['updated']['acquired_price'],
            'quantity'          => $attributes['quantity_diff'],
            'from_condition'    => $from['condition'] ?? '',
            'from_finish'       => $from['finish'] ?? '',
            'from_acquired'     => $from['acquired_price'] ?? null,
            'from_quantity'     => $from['quantity'] ?? null,
        ]);

        $this->fromData = !!count($from) ? [
            'condition'     => $this->data->from_condition,
            'finish'        => $this->data->from_finish,
            'acquired'      => $this->data->from_acquired,
            'quantity'      => $this->data->from_quantity,
        ] : $from;

        return $this;
    }

    public function setCollectionCardFromPivot(array $pivot, bool $now = true) : self
    {
        $this->data = new CollectionCardEventData([
            'acquired'          => $pivot['price_when_added'],
            'card'              => $pivot['card_uuid'],
            'collection'        => $pivot['collection_uuid'],
            'condition'         => $pivot['condition'],
            'date_added'        => $now ? Carbon::now() : $pivot['date_added'],
            'finish'            => $pivot['finish'],
            'price'             => $pivot['price_when_added'],
            'quantity'          => $pivot['quantity'],
        ]);

        return $this;
    }

    public function setCollectionCardSummaryFromEventAttributes(array $attributes, bool $now = true) : self
    {
        $this->summaryData = new CollectionCardEventData([
            'acquired'   => $attributes['change']['acquired_price']
                ?? $attributes['updated']['acquired_price'],
            'card'          => $attributes['change']['id'],
            'collection'    => $attributes['uuid'],
            'condition'     => $attributes['change']['condition'] ?? 'NM',
            'date_added'    => $now ? Carbon::now() : $attributes['updated']['date_added'],
            'finish'        => $attributes['change']['finish'],
            'price'         => $attributes['updated']['price'],
            'quantity'      => $attributes['quantity_diff'],
            'updated_price' => $attributes['updated']['price'],
        ]);

        return $this;
    }

    public function shouldUpdateCollectionCardSummary() : bool
    {
        $summary = $this->findCollectionCardSummary();

        return $summary->count() > 0;
    }

    public function updateCollectionCardSummary() : self
    {
        $summary = $this->findCollectionCardSummary()->first();
        if (!$summary) {
            return $this;
        }

        $change = $this->data->quantity;
        $target = $this->findTargetCollectionCardSummary()->first();

        if ($target && $target->id != $summary->id) {
            $change += $target->quantity;
            $target->delete();
        }

        $updatedSummary           = $this->summaryData;
        $updatedSummary->quantity = $summary->quantity + $change;

        $summary->update($updatedSummary->toCollectionCardSummary());

        return $this;
    }

    private function getSearchAttributes() : array
    {
        $collection = $this->data->collection;
        $card       = $this->data->card;

        $search = [
            'finish'     => $this->data->finish,
            'acquired'   => $this->data->acquired,
            'condition'  => $this->data->condition ?: 'NM',
        ];

        return [
            'card'          => $card,
            'collection'    => $collection,
            'search'        => $search,
        ];
    }
}
