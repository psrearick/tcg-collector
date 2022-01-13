<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FrameEffectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('frame_effects')->insert([
            'name' => 'legendary',
            'description' => 'The cards have a legendary crown',
        ]);

        DB::table('frame_effects')->insert([
            'name' => 'devoid',
            'description' => 'The Devoid frame effect',
        ]);
        
        DB::table('frame_effects')->insert([
            'name' => 'showcase',
            'description' => 'A custom Showcase frame',
        ]);
        
        DB::table('frame_effects')->insert([
            'name' => 'extendedart',
            'description' => 'An extended art frame',
        ]);
        
        DB::table('frame_effects')->insert([
            'name' => 'etched',
            'description' => 'The cards have an etched foil treatment',
        ]);
    }
}
