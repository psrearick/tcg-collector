<?php

namespace App\Http\Controllers;

use App\Domain\Collections\Models\Collection;

class CollectionsListController
{
    public function index()
    {
        return response()->json(Collection::all()->values());
    }
}