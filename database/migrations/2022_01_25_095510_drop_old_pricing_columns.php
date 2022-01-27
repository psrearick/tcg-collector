<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropOldPricingColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('prices', 'price_bak')) {
            Schema::table('prices', function (Blueprint $table) {
                $table->dropColumn('price_bak');
            });
        }

        if (Schema::hasColumn('collection_card_summaries', 'price_when_added_bak')) {
            Schema::table('collection_card_summaries', function (Blueprint $table) {
                $table->dropColumn('price_when_added_bak');
            });
        }

        if (Schema::hasColumn('collection_card_summaries', 'price_when_updated_bak')) {
            Schema::table('collection_card_summaries', function (Blueprint $table) {
                $table->dropColumn('price_when_updated_bak');
            });
        }

        if (Schema::hasColumn('collection_card_summaries', 'current_price_bak')) {
            Schema::table('collection_card_summaries', function (Blueprint $table) {
                $table->dropColumn('current_price_bak');
            });
        }

        if (Schema::hasColumn('card_collections', 'price_when_added_bak')) {
            Schema::table('card_collections', function (Blueprint $table) {
                $table->dropColumn('price_when_added_bak');
            });
        }

        if (Schema::hasColumn('card_search_data_objects', 'price_bak')) {
            Schema::table('card_search_data_objects', function (Blueprint $table) {
                $table->dropColumn('prices_bak');
            });
        }

        if (Schema::hasColumn('summaries', 'current_value_bak')) {
            Schema::table('summaries', function (Blueprint $table) {
                $table->dropColumn('current_value_bak');
            });
        }

        if (Schema::hasColumn('summaries', 'acquired_value_bak')) {
            Schema::table('summaries', function (Blueprint $table) {
                $table->dropColumn('acquired_value_bak');
            });
        }

        if (Schema::hasColumn('summaries', 'gain_loss_bak')) {
            Schema::table('summaries', function (Blueprint $table) {
                $table->dropColumn('gain_loss_bak');
            });
        }
    }
}
