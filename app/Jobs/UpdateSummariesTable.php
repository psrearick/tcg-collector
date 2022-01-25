<?php

namespace App\Jobs;

use Brick\Money\Money;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateSummariesTable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private object $summary;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(object $summary)
    {
        $this->summary = $summary;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $summary          = $this->summary;
        $currentValue     = Money::of($summary->current_value ?: 0, 'USD');
        $currentValueInt  = $currentValue->getMinorAmount()->toInt();
        $acquiredValue    = Money::of($summary->acquired_value ?: 0, 'USD');
        $acquiredValueInt = $acquiredValue->getMinorAmount()->toInt();
        $gainLoss         = Money::of($summary->gain_loss ?: 0, 'USD');
        $gainLossInt      = $gainLoss->getMinorAmount()->toInt();

        DB::table('summaries')
            ->where('id', $summary->id)
            ->update([
                'current_value_int'  => $currentValueInt,
                'acquired_value_int' => $acquiredValueInt,
                'gain_loss_int'      => $gainLossInt,
            ]);
    }
}
