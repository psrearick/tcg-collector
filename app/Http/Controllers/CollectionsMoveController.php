<?php

namespace App\Http\Controllers;

use App\Domain\Collections\Aggregate\Actions\MoveCollection;
use Illuminate\Http\Request;

class CollectionsMoveController extends Controller
{
    public function update(Request $request, MoveCollection $moveCollection)
    {
        $moveCollection($request->get('uuid'), $request->get('destination') ?? '', auth()->id());

        return back();
    }
}
