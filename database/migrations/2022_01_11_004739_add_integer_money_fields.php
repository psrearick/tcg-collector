<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIntegerMoneyFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('summaries', function (Blueprint $table) {
            $table->integer('current_value_int')->after('current_value')->nullable();
            $table->integer('acquired_value_int')->after('acquired_value')->nullable();
            $table->integer('gain_loss_int')->after('gain_loss')->nullable();
        });

        Schema::table('prices', function (Blueprint $table) {
            $table->integer('price_int')->after('price')->nullable();
        });

        Schema::table('collection_card_summaries', function (Blueprint $table) {
            $table->integer('price_when_added_int')->after('price_when_added')->nullable();
            $table->integer('price_when_updated_int')->after('price_when_updated')->nullable();
            $table->integer('current_price_int')->after('current_price')->nullable();
        });

        Schema::table('card_collections', function (Blueprint $table) {
            $table->integer('price_when_added_int')->after('price_when_added')->nullable();
        });

        Schema::table('card_search_data_objects', function (Blueprint $table) {
            $table->string('prices_int')->after('prices')->nullable();
        });
    }
}
