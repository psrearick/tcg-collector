<?php

use App\Actions\NormalizeString;
use App\Domain\Cards\Models\Card;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateNameNormalizedFieldOnCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->dropColumn('name_normalized');
        });

        Schema::table('cards', function (Blueprint $table) {
            $table->string('name_normalized')->after('name')->index();
        });

        DB::table('cards')->orderBy('id')->each(function ($card) {
            Card::find($card->id)->update(['name_normalized' => (new NormalizeString)($card->name)]);
        });
    }
}
