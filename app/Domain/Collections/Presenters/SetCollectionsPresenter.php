<?php

namespace App\Domain\Collections\Presenters;

use App\App\Contracts\PresenterInterface;
use App\Domain\Collections\Aggregate\DataObjects\CollectionData;
use App\Domain\Collections\Models\Collection;
use App\Domain\Sets\Models\Set;

class SetCollectionsPresenter implements PresenterInterface
{
    protected string $uuid;

    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    public function present() : array
    {
        $collectionData = new CollectionData(Collection::uuid($this->uuid)->toArray());
        $sets           = $this->getSets();

        return [
            'collection'    => $collectionData,
            'sets'          => $sets,
        ];
    }

    private function getSets() : array
    {
        return Set::select(['id', 'code', 'name'])->orderBy('releaseDate', 'desc')->get()->toArray();
    }
}
