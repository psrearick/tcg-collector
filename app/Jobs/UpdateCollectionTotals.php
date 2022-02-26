<?php

namespace App\Jobs;

use App\Domain\Collections\Models\Collection;
use App\Domain\Prices\Aggregate\Actions\Summaries\CalculateCollectionTotals;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateCollectionTotals implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private CalculateCollectionTotals $calculate;

    private Collection $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
        $this->calculate  = app(CalculateCollectionTotals::class);
    }

    public function handle() : void
    {
        $collection         = $this->collection;
        $collectionTotals   = $this->calculate->execute($collection);
        $collection->summary()->updateOrCreate([
            'uuid'  => $collection->uuid,
            'type'  => 'collection',
        ], $collectionTotals);
    }
}
