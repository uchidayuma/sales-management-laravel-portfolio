<?php

use Illuminate\Database\Seeder;

class RanksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ranks')->insert([
        [
            'id' => 1,
            'name' => 'A',
            'condition' => 1,
        ],
        [
            'id' => 2,
            'name' => 'B',
            'condition' => 1,
        ],
        [
            'id' => 3,
            'name' => 'C',
            'condition' => 1,
        ],
        [
            'id' => 4,
            'name' => 'D',
            'condition' => 1,
        ],
      ]);
    }
}
