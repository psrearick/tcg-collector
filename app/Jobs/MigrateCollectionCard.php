<?php

namespace App\Jobs;

use App\Domain\Collections\Aggregate\Actions\UpdateCollectionCard;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MigrateCollectionCard implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $collectionUuid;

    private string $finish;

    private int $quantity;

    private string $uuid;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($collectionUuid, $uuid, $finish, $quantity)
    {
        $this->finish              = $finish;
        $this->collectionUuid      = $collectionUuid;
        $this->uuid                = $uuid;
        $this->quantity            = $quantity;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $updateCollectionCard = new UpdateCollectionCard;
        $updateCollectionCard([
            'uuid'   => $this->collectionUuid, // collection uuid
            'change' => [
                'change' => $this->quantity, // quantity
                'id'     => $this->uuid,  // card uuid
                'finish' => $this->finish, // finish - foil, nonfoil, etched
            ],
        ]);
    }
}
