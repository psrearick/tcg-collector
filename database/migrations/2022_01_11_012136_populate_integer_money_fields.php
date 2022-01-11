<?php

use Brick\Money\Money;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class PopulateIntegerMoneyFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('summaries')
            ->lazyById()->each(function ($summary) {
                $currentValue = Money::of($summary->current_value ?: 0, 'USD');
                $currentValueInt = $currentValue->getMinorAmount()->toInt();
                $acquiredValue = Money::of($summary->acquired_value ?: 0, 'USD');
                $acquiredValueInt = $acquiredValue->getMinorAmount()->toInt();
                $gainLoss = Money::of($summary->gain_loss ?: 0, 'USD');
                $gainLossInt = $gainLoss->getMinorAmount()->toInt();

                DB::table('summaries')
                    ->where('id', $summary->id)
                    ->update([
                        'current_value_int'  => $currentValueInt,
                        'acquired_value_int' => $acquiredValueInt,
                        'gain_loss_int'      => $gainLossInt,
                    ]);
            });

        DB::table('card_search_data_objects')
            ->lazyById()->each(function ($dataObject) {
                $prices = unserialize($dataObject->prices);
                $priceInts = [];
                foreach ($prices as $finish => $price) {
                    $priceValue = Money::of($price ?: 0, 'USD');
                    $priceValueInt = $priceValue->getMinorAmount()->toInt();
                    $priceInts[$finish] = $priceValueInt;
                }

                DB::table('card_search_data_objects')
                    ->where('id', '=', $dataObject->id)
                    ->update([
                        'prices_int' => serialize($priceInts),
                    ]);
            });

        DB::table('prices')
            ->lazyById()->each(function ($price) {
                $priceValue = Money::of($price->price ?: 0, 'USD');
                $priceValueInt = $priceValue->getMinorAmount()->toInt();

                DB::table('prices')
                    ->where('id', '=', $price->id)
                    ->update([
                        'price_int' => $priceValueInt,
                    ]);
            });

        DB::table('collection_card_summaries')
            ->lazyById()->each(function ($summary) {
                $priceWhenAddedValue = Money::of($summary->price_when_added ?: 0, 'USD');
                $priceWhenAddedValueInt = $priceWhenAddedValue->getMinorAmount()->toInt();
                $priceWhenUpdatedValue = Money::of($summary->price_when_updated ?: 0, 'USD');
                $priceWhenUpdatedValueInt = $priceWhenUpdatedValue->getMinorAmount()->toInt();
                $currentPriceValue = Money::of($summary->current_price ?: 0, 'USD');
                $currentPriceValueInt = $currentPriceValue->getMinorAmount()->toInt();

                DB::table('collection_card_summaries')
                    ->where('id', '=', $summary->id)
                    ->update([
                        'price_when_added_int'   => $priceWhenAddedValueInt,
                        'price_when_updated_int' => $priceWhenUpdatedValueInt,
                        'current_price_int'      => $currentPriceValueInt,
                    ]);
            });

        DB::table('card_collections')
            ->lazyById()->each(function ($cardCollection) {
                $priceWhenAddedValue = Money::of($cardCollection->price_when_added ?: 0, 'USD');
                $priceWhenAddedValueInt = $priceWhenAddedValue->getMinorAmount()->toInt();

                DB::table('card_collections')
                    ->where('id', '=', $cardCollection->id)
                    ->update([
                        'price_when_added_int' => $priceWhenAddedValueInt,
                    ]);
            });
    }
}
