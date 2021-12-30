<?php

namespace App\Http\Controllers;

use App\Domain\Folders\Aggregate\Actions\CreateFolder;
use App\Domain\Folders\Aggregate\Actions\UpdateFolder;
use App\Domain\Folders\Aggregate\Queries\FolderChildren;
use GetFolder;
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

    public function destroy()
    {
    }

    public function show(string $uuid, GetFolder $getFolder) : Response
    {
        $folder         = $getFolder($uuid);
        $folderChildren = new FolderChildren($uuid, auth()->id());

        return Inertia::render('Folders/Show', [
            'folder'      => $folder,
            'folders'     => $folderChildren->folders(),
            'collections' => $folderChildren->collections(),
        ]);
    }

    public function store(Request $request, CreateFolder $createFolder)
    {
        $uuid = $createFolder($request->all());

        return redirect()->route('folders.show', $uuid);
    }

    public function update(Request $request, UpdateFolder $updateFolder)
    {
        dd($request->all());
        $updateFolder($request->all());

        return back();
    }
}
