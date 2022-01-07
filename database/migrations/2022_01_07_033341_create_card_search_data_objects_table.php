<?php

use App\Actions\CreateCardObjects;
use App\Domain\Cards\Actions\BuildCard;
use App\Domain\Cards\Models\Card;
use App\Domain\Prices\Aggregate\Actions\GetLatestPrices;
use App\Domain\Prices\Aggregate\Actions\MatchType;
use App\Models\CardSearchDataObject;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardSearchDataObjectsTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('card_search_data');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('card_search_data_objects', function (Blueprint $table) {
            $table->id();
            $table->string('card_uuid')->index();
            $table->string('card_name');
            $table->string('card_name_normalized')->index();
            $table->string('set_id')->index();
            $table->string('set_name')->index();
            $table->string('set_code')->index();
            $table->string('features')->nullable();
            $table->string('prices')->nullable();
            $table->string('collector_number')->nullable();
            $table->string('finishes')->nullable();
            $table->string('image')->nullable();
            $table->string('set_image')->nullable();
            $table->timestamps();
        });

        (new CreateCardObjects)();
    }
}
