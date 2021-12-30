<?php

namespace App\Http\Controllers;

use App\Domain\Folders\Aggregate\Actions\MoveFolder;
use Illuminate\Http\Request;

class FoldersMoveController extends Controller
{
    public function update(Request $request, MoveFolder $moveFolder)
    {
        $moveFolder($request->get('uuid'), $request->get('destination') ?? '', auth()->id());

        return back();
    }
}
