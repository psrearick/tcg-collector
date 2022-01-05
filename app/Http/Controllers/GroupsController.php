<?php

namespace App\Http\Controllers;

use App\Domain\Groups\Presenters\GroupsShowPresenter;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Domain\Prices\Aggregate\Actions\GetSummaryData;
use App\Domain\Collections\Presenters\CollectionsPresenter;

class GroupsController
{
    public function index(Request $request) : Response
    {
        $presenter = (new GroupsShowPresenter($request->paginate, $request->userId))->present();
        return Inertia::render('Groups/Index', $presenter);
    }

    public function show(string $uuid, Request $request, GetSummaryData $getSummaryData) : Response
    {
        $collections     = (new CollectionsPresenter($request->all(), $uuid))->present();
        $summary         = $getSummaryData(collect([$collections['collection']]), null, false);

        return Inertia::render('Groups/Show', [
            'totals'        => $summary,
            'collection'    => $collections['collection'],
            'list'          => $collections['list'],
            'search'        => $collections['search'],
        ]);
    }
}