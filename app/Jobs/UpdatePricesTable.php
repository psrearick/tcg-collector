<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Brick\Money\Money;
use Illuminate\Support\Facades\DB;

class UpdatePricesTable implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private object $price;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(object $price)
    {
        $this->price = $price;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $price = $this->price;
        $priceValue = Money::of($price->price ?: 0, 'USD');
        $priceValueInt = $priceValue->getMinorAmount()->toInt();

        DB::table('prices')
            ->where('id', '=', $price->id)
            ->update([
                'price_int' => $priceValueInt,
            ]);
    }
}
