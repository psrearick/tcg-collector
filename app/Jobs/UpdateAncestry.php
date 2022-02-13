<?php

namespace App\Jobs;

use App\Domain\Base\Collection;
use App\Domain\Collections\Models\CollectionGeneral;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\LazyCollection;
use Throwable;

class UpdateAncestry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @throws Throwable
     */
    public function handle() : void
    {
        $collections    = CollectionGeneral::whereNull('deleted_at');
        $count          = $collections->count();

        $batch = Bus::batch([])
            ->allowFailures()
            ->finally(function (Batch $batch) use ($count) {
                if ($count !== $batch->totalJobs) {
                    return;
                }

                UpdateFolderAncestry::dispatch();
            })
            ->name('Update Ancestry')
            ->dispatch();

        $collections
            ->cursor()
            ->map(fn (Collection $collection) => $this->createUpdateJob($collection))
            ->chunk(500)
            ->each(function (LazyCollection $jobs) use ($batch) {
                $batch->add($jobs);
            });
    }

    private function createUpdateJob(Collection $collection) : ShouldQueue
    {
        return new UpdateCollectionTotals($collection);
    }
}
