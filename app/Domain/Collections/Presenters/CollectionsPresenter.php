<?php

namespace App\Domain\Collections\Presenters;

use App\App\Contracts\PresenterInterface;
use GetCollection;
use App\Domain\Collections\Aggregate\Actions\SearchCollection;
use App\Domain\Collections\Aggregate\DataObjects\CollectionData;
use App\Domain\Collections\Aggregate\Queries\CollectionCardsSummary;
use App\Domain\Collections\Aggregate\DataObjects\CollectionCardSearchData;
use App\Domain\Cards\DataObjects\CardSearchData;

class CollectionsPresenter implements PresenterInterface
{
    private array $request;

    private string $uuid;
    
    private CollectionData $collection;
    
    private CollectionCardsSummary $collectionCards;

    public function __construct(array $request, string $uuid)
    {
        $this->request = $request;
        $this->uuid = $uuid;
        $this->collectionCards = new CollectionCardsSummary($uuid);
        $this->collection = (new GetCollection)($uuid);
    }

    public function present(): array
    {
        $collectionCards = $this->collectionCards->list();

        $searchData = new CollectionCardSearchData([
            'data'      => $collectionCards,
            'uuid'      => $this->uuid,
            'search'    => new CardSearchData($this->request),
        ]);

        $searchCollection = new SearchCollection;
        $searchResults = $searchCollection($searchData)->collection ?: null;

        if ($searchResults && $searchData->search->paginator) {
            $page = $searchData->search->paginator;
            $list = $searchResults->paginate(
                $page['per_page'],
                $page['total'],
                $page['current_page'],
            );
        } elseif ($searchResults) {
            $list = $searchResults->paginate(25);
        } else {
            $list = [];
        }
        return [
            'collection'    => $this->collection,
            'list'          => $list,
            'search'        => $searchData->search,
        ];
    }
}