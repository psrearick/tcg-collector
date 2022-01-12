<?php

use App\Actions\Migrations\PopulateIntegerMoneyFields as MigrationsPopulateIntegerMoneyFields;
use Illuminate\Database\Migrations\Migration;

class PopulateIntegerMoneyFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        (new MigrationsPopulateIntegerMoneyFields)();
    }
}
