<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSummariesTable extends Migration
{
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('summary');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('summaries', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->index();
            $table->string('type')->default('collection');
            $table->integer('total_cards')->default(0);
            $table->float('current_value')->default(0.0);
            $table->float('acquired_value')->default(0.0);
            $table->float('gain_loss')->default(0.0);
            $table->float('gain_loss_percent')->default(0.0);
            $table->timestamps();
        });
    }
}
