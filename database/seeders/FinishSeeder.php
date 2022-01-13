<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinishSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('finishes')->insert([
            'name' => 'nonfoil',
        ]);

        DB::table('finishes')->insert([
            'name' => 'foil',
        ]);

        DB::table('finishes')->insert([
            'name' => 'etched',
        ]);

        DB::table('finishes')->insert([
            'name' => 'glossy',
        ]);
    }
}
