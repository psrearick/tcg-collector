<?php

namespace App\Jobs;

use App\Domain\Cards\Models\Card;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class ChunkCardObjectCreate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

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
        Card::chunkById(150,
            function ($cards) {
                $cards->each(function ($card) {
                    CreateCardSearchDataObjects::dispatch($card->uuid);
                });
            }
        );
    }
}
