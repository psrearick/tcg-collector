<?php

namespace App\Domain\Sets\Presenters;

use App\App\Contracts\PresenterInterface;
use App\Domain\Sets\Models\Set;

class SetPresenter implements PresenterInterface
{
    public function present(string $search = '') : array
    {
        $sets = Set::select(['id', 'code', 'name']);

        if ($search) {
            $searchTerm  = '%' . $search . '%';
            $sets->where('sets.name', 'like', $searchTerm)
                ->orWhere('sets.code', 'like', $searchTerm);
        }

        $sets->orderBy('releaseDate', 'desc');

        return $sets->get()->toArray();
    }
}
