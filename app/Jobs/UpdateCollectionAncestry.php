<?php

namespace App\Jobs;

use App\Domain\Base\Collection;
use App\Domain\Collections\Models\CollectionGeneral;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\LazyCollection;
use Throwable;

class UpdateCollectionAncestry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @throws Throwable
     */
    public function handle() : void
    {
        CollectionGeneral::whereNull('deleted_at')
            ->cursor()
            ->map(fn (Collection $collection) => new UpdateCollectionTotals($collection))
            ->chunk(500)
            ->each(function (LazyCollection $jobs) {
                Bus::chain($jobs->toArray())->dispatch();
            });
    }
}
