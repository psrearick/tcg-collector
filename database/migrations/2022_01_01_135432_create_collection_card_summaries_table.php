<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollectionCardSummariesTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('collection_card_summary');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collection_card_summaries', function (Blueprint $table) {
            $table->id();
            $table->string('card_uuid')->index();
            $table->string('collection_uuid')->index();
            $table->float('price_when_added')->nullable();
            $table->float('price_when_updated')->nullable();
            $table->text('description')->nullable();
            $table->string('condition')->nullable();
            $table->integer('quantity')->default(0);
            $table->string('finish')->default('nonfoil')->nullable();
            $table->string('import_uuid')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('date_added')->nullable();
            $table->timestamps();
        });
    }
}
