<?php

namespace App\Http\Controllers;

use App\Domain\Collections\Presenters\CollectionsPresenter;
use App\Domain\Groups\Presenters\GroupsShowPresenter;
use App\Domain\Prices\Aggregate\Actions\GetSummaryData;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GroupsController
{
    public function index(Request $request) : Response
    {
        $request->merge(['inGroup' => true]);
        $presenter = (new GroupsShowPresenter($request->paginate, $request->userId))->present();

        return Inertia::render('Groups/Index', $presenter);
    }

    public function show(string $uuid, Request $request, GetSummaryData $getSummaryData) : Response
    {
        $request->merge(['inGroup' => true]);
        $collections     = (new CollectionsPresenter($request->all(), $uuid, true))->present();
        $summary         = $getSummaryData(collect([$collections['collection']]), null, false);

        return Inertia::render('Groups/Show', [
            'totals'        => $summary,
            'collection'    => $collections['collection'],
            'list'          => $collections['list'],
            'search'        => $collections['search'],
        ]);
    }
}
