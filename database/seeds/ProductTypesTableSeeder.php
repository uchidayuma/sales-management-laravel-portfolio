<?php

use Illuminate\Database\Seeder;

class ProductTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('product_types')->truncate();
        DB::table('product_types')->insert([
            [
              'id' => 1,
              'name' => '人工芝',
            ],
            [
              'id' => 2,
              'name' => '副資材',
            ],
            [
              'id' => 3,
              'name' => '販促物',
            ],
            [
              'id' => 4,
              'name' => 'カット',
            ],
            [
              'id' => 5,
              'name' => '反物カット',
            ],
        ]);
    }
}
