<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Domain\Cards\Models\Card;
use App\Jobs\CreateCardSearchDataObjects;

class ChunkCardObjectCreate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Card::with(['frameEffects', 'set', 'finishes', 'prices'])->chunk(15,
            function ($cards) {
                $cards->each(function ($card) {
                    CreateCardSearchDataObjects::dispatch($card);
                });
            }
        );
    }
}
