<?php

namespace App\Http\Controllers;

use App\Domain\Folders\Aggregate\Actions\CreateFolder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FoldersController extends Controller
{
    public function create(Request $request) : Response
    {
        return Inertia::render('Folders/Create', [
            'folder' => $request->get('folder') ? (int) $request->get('folder') : null,
        ]);
    }

    public function destroy()
    {}

    public function show(string $uuid, Request $request)
    {
        dd($uuid);
    }

    public function store(Request $request, CreateFolder $createFolder)
    {
        $uuid = $createFolder($request->all());

        return redirect()->route('folders.show', $uuid);
    }

    public function update()
    {}
}