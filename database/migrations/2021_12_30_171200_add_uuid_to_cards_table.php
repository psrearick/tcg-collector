<?php

use App\Domain\Cards\Models\Card;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AddUuidToCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->string('uuid')->nullable()->after('id')->index();
        });

        DB::table('cards')->update(['uuid' => DB::raw("`cardId`")]);

        Card::whereNull('uuid')->orWhere('uuid', '=', DB::raw("''"))
            ->update(['uuid' => Str::uuid()]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
}
