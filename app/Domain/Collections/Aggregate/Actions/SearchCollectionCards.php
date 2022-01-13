<?php

namespace App\Domain\Collections\Aggregate\Actions;

use App\Domain\Cards\Actions\SearchCards;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchParameterData;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchResultsData;
use Illuminate\Database\Eloquent\Builder;

class SearchCollectionCards
{
    protected ?Builder $cards;

    protected CollectionCardSearchParameterData $collectionCardSearchParameterData;

    public function __invoke(CollectionCardSearchParameterData $collectionCardSearchParameterData) : CollectionCardSearchResultsData
    {
        $this->collectionCardSearchParameterData = $collectionCardSearchParameterData;
        $searchCards                    = new SearchCards;
        $cardSearchResults              = $searchCards($collectionCardSearchParameterData->search);
        $this->cards                    = $cardSearchResults->builder;

        if (!$this->cards) {
            return new CollectionCardSearchResultsData([]);
        }

        $this->addRelations();

        return new CollectionCardSearchResultsData(['builder' => $this->cards]);
    }

    private function addRelations() : void
    {
        $uuid = $this->collectionCardSearchParameterData->uuid;

        $this->cards->with([
            'finishes',
            'frameEffects',
            'set',
        ]);

        if ($uuid) {
            $this->cards->with([
                'collections' => function ($query) use ($uuid) {
                    $query->where('collections.uuid', '=', $uuid);
                },
            ]);
        } else {
            $this->cards->with(['collections']);
        }
    }
}
