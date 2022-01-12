<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMoneyFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // Schema::table('prices', function (Blueprint $table) {
        //     $table->renameColumn('price', 'price_bak');
        //     $table->renameColumn('price_int', 'price');
        // });

        // Schema::table('collection_card_summaries', function (Blueprint $table) {
        //     $table->renameColumn('price_when_added', 'price_when_added_bak');
        //     $table->renameColumn('price_when_updated', 'price_when_updated_bak');
        //     $table->renameColumn('current_price', 'current_price_bak');
        //     $table->renameColumn('price_when_added_int', 'price_when_added');
        //     $table->renameColumn('price_when_updated_int', 'price_when_updated');
        //     $table->renameColumn('current_price_int', 'current_price');
        // });

        // Schema::table('card_collections', function (Blueprint $table) {
        //     $table->renameColumn('price_when_added', 'price_when_added_bak');
        //     $table->renameColumn('price_when_added_int', 'price_when_added');
        // });

        // Schema::table('card_search_data_objects', function (Blueprint $table) {
        //     $table->renameColumn('prices', 'prices_bak');
        //     $table->renameColumn('prices_int', 'prices');
        // });
    }
}
