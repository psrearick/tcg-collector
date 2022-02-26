<?php

namespace App\Http\Controllers;

use App\Domain\Folders\Aggregate\Actions\CreateFolder;
use App\Domain\Folders\Aggregate\Actions\DeleteFolder;
use App\Domain\Folders\Aggregate\Actions\GetChildren;
use App\Domain\Folders\Aggregate\Actions\GetFolder;
use App\Domain\Folders\Aggregate\Actions\UpdateFolder;
use App\Domain\Prices\Aggregate\Actions\GetSummaryData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FoldersController extends Controller
{
    public function create(Request $request) : Response
    {
        return Inertia::render('Folders/Create', [
            'folder' => $request->get('folder') ?? null,
        ]);
    }

    public function destroy(string $folder, DeleteFolder $deleteFolder) : RedirectResponse
    {
        $deleteFolder($folder);

        return redirect()->back();
    }

    public function show(string $uuid, GetFolder $getFolder, GetChildren $getChildren, GetSummaryData $getSummaryData) : Response
    {
        $folder         = $getFolder($uuid);
        $folderChildren = $getChildren($uuid);
        $collections    = $folderChildren['collections'];
        $folders        = $folderChildren['folders'];
        $summary        = $getSummaryData($collections, $folders, false);

        return Inertia::render('Folders/Show', [
            'folder'        => $folder->toArray(),
            'collections'   => $collections,
            'folders'       => $folders,
            'totals'        => $summary,
        ]);
    }

    public function store(Request $request, CreateFolder $createFolder) : RedirectResponse
    {
        $uuid = $createFolder($request->all());

        return redirect()->route('folders.show', $uuid);
    }

    public function update(Request $request, UpdateFolder $updateFolder) : RedirectResponse
    {
        $updateFolder($request->all());

        return redirect()->back();
    }
}
