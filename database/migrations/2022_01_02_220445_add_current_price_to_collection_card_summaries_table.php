<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrentPriceToCollectionCardSummariesTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('collection_card_summaries', function (Blueprint $table) {
            $table->dropColumn('current_price');
        });
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('collection_card_summaries', function (Blueprint $table) {
            $table->float('current_price')->nullable()->after('price_when_updated');
        });
    }
}
