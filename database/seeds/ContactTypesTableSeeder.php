<?php

use Illuminate\Database\Seeder;

class ContactTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('contact_types')->truncate();
        DB::table('contact_types')->insert([
            [
              'id' => 1,
              'wpid' => 2191,
              'name' => '個人サンプル請求',
            ],
            [
              'id' => 2,
              'wpid' => 2186,
              'name' => '個人図面見積もり',
            ],
            [
              'id' => 3,
              'wpid' => 2190,
              'name' => '個人訪問見積もり',
            ],
            [
              'id' => 4,
              'wpid' => 2210,
              'name' => '個人その他問い合わせ',
            ],
            [
              'id' => 5,
              'wpid' => 2192,
              'name' => '法人サンプル請求',
            ],
            [
              'id' => 6,
              'wpid' => 2194,
              'name' => '法人図面見積もり',
            ],
            [
              'id' => 7,
              'wpid' => 2193,
              'name' => '法人訪問見積もり',
            ],
            [
              'id' => 8,
              'wpid' => 2213,
              'name' => '法人その他問い合わせ',
            ],
      ]);
    }
}
