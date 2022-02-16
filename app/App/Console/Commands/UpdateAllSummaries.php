<?php

namespace App\App\Console\Commands;

use App\Domain\Base\Collection;
use App\Domain\Collections\Models\CollectionGeneral;
use App\Jobs\UpdateAncestry;
use App\Jobs\UpdateCollectionTotals;
use App\Jobs\UpdateFolderAncestry;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\LazyCollection;
use Symfony\Component\Console\Command\Command as Response;
use Throwable;

class UpdateAllSummaries extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update all collection ancestry totals';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'summaries:update';

    /**
     * @throws Throwable
     */
    public function handle() : int
    {
        $collections    = CollectionGeneral::whereNull('deleted_at');
//        $count          = $collections->count();

        $batch = $collections
            ->get()
            ->map(fn (Collection $collection) => new UpdateCollectionTotals($collection));

        Bus::batch([$batch->toArray()])
            ->allowFailures()
            ->finally(function () {
                UpdateFolderAncestry::dispatch();
            })
            ->name('Update Ancestry')
            ->dispatch();

//        $batch = Bus::batch([])
//            ->allowFailures()
//            ->finally(function (Batch $batch) use ($count) {
//                if ($count !== $batch->totalJobs) {
//                    return;
//                }
//
//                UpdateFolderAncestry::dispatch();
//            })
//            ->name('Update Ancestry')
//            ->dispatch();

//        $collections
//            ->cursor()
//            ->map(fn (Collection $collection) => $this->createUpdateJob($collection))
//            ->chunk(500)
//            ->each(function (LazyCollection $jobs) use ($batch) {
//                $batch->add($jobs);
//            });

//        UpdateAncestry::dispatch()->onQueue('long-running-queue');

        return Response::SUCCESS;
    }

    private function createUpdateJob(Collection $collection) : ShouldQueue
    {
        return new UpdateCollectionTotals($collection);
    }
}
