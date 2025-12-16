<?php

use Illuminate\Database\Seeder;

class RegionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('regions')->truncate();
        DB::table('regions')->insert([
            [
                'name' => '北海道地方',
                'small_shipping_price' => 800,
                'large_shipping_price' => 2500,
                'extra_large_shipping_price' => 5500,
                'extra_large_shipping_price2' => 4000,
                'extra_large_shipping_price3' => 3700,
            ],
            [
                'name' => '北東北地方',
                'small_shipping_price' => 750,
                'large_shipping_price' => 2300,
                'extra_large_shipping_price' => 5300,
                'extra_large_shipping_price2' => 3800,
                'extra_large_shipping_price3' => 3500,
            ],
            [
                'name' => '南東北地方',
                'small_shipping_price' => 700,
                'large_shipping_price' => 2100,
                'extra_large_shipping_price' => 5100,
                'extra_large_shipping_price2' => 3600,
                'extra_large_shipping_price3' => 3300,
            ],
            [
                'name' => '関東地方',
                'small_shipping_price' => 650,
                'large_shipping_price' => 1900,
                'extra_large_shipping_price' => 4900,
                'extra_large_shipping_price2' => 3400,
                'extra_large_shipping_price3' => 3100,
            ],
            [
                'name' => '北陸地方',
                'small_shipping_price' => 600,
                'large_shipping_price' => 1700,
                'extra_large_shipping_price' => 4700,
                'extra_large_shipping_price2' => 3200,
                'extra_large_shipping_price3' => 2900,
            ],
            [
                'name' => '中部地方',
                'small_shipping_price' => 500,
                'large_shipping_price' => 1500,
                'extra_large_shipping_price' => 4500,
                'extra_large_shipping_price2' => 3000,
                'extra_large_shipping_price3' => 2700,
            ],
            [
                'name' => '近畿地方',
                'small_shipping_price' => 600,
                'large_shipping_price' => 1700,
                'extra_large_shipping_price' => 4700,
                'extra_large_shipping_price2' => 3200,
                'extra_large_shipping_price3' => 2900,
            ],
            [
                'name' => '中国地方',
                'small_shipping_price' => 650,
                'large_shipping_price' => 1900,
                'extra_large_shipping_price' => 4900,
                'extra_large_shipping_price2' => 3400,
                'extra_large_shipping_price3' => 3100,
            ],
            [
                'name' => '四国地方',
                'small_shipping_price' => 750,
                'large_shipping_price' => 2300,
                'extra_large_shipping_price' => 5300,
                'extra_large_shipping_price2' => 3800,
                'extra_large_shipping_price3' => 3500,
            ],
            [
                'name' => '北九州地方',
                'small_shipping_price' => 750,
                'large_shipping_price' => 2300,
                'extra_large_shipping_price' => 5300,
                'extra_large_shipping_price2' => 3800,
                'extra_large_shipping_price3' => 3500,
            ],
            [
                'name' => '南九州地方',
                'small_shipping_price' => 800,
                'large_shipping_price' => 2500,
                'extra_large_shipping_price' => 5500,
                'extra_large_shipping_price2' => 4000,
                'extra_large_shipping_price3' => 3700,
            ],
            [
                'name' => '沖縄地方',
                'small_shipping_price' => 1200,
                'large_shipping_price' => 7750,
                'extra_large_shipping_price' => 9400,
                'extra_large_shipping_price2' => 9250,
                'extra_large_shipping_price3' => 8950,
            ]
        ]);
    }
}
