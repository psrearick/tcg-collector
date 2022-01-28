<?php

namespace App\Jobs;

use App\Domain\Base\Collection;
use App\Domain\Prices\Aggregate\Actions\GetCollectionTotals;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateCollectionTotals implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Collection $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    public function handle() : void
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        $collection         = $this->collection;
        $collectionTotals   = (new GetCollectionTotals)($collection);
        $collection->summary()->updateOrCreate([
            'uuid'  => $collection->uuid,
            'type'  => 'collection',
        ], $collectionTotals);
    }
}
