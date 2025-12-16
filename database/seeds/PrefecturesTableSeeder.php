<?php

use Illuminate\Database\Seeder;

class PrefecturesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('prefectures')->truncate();
        DB::table('prefectures')->insert([
            [
                'region_id' => 1,
                'name' => '北海道',
                'charter_shipping_price' => null,
            ],
            [
                'region_id' => 2,
                'name' => '青森県',
                'charter_shipping_price' => 150000,
            ],
            [
                'region_id' => 2,
                'name' => '岩手県',
                'charter_shipping_price' => 150000,
            ],
            [
                'region_id' => 3,
                'name' => '宮城県',
                'charter_shipping_price' => 100000,
            ],
            [
                'region_id' => 2,
                'name' => '秋田県',
                'charter_shipping_price' => 150000,
            ],
            [
                'region_id' => 3,
                'name' => '山形県',
                'charter_shipping_price' => 100000,
            ],
            [
                'region_id' => 3,
                'name' => '福島県',
                'charter_shipping_price' => 80000,
            ],
            [
                'region_id' => 4,
                'name' => '茨城県',
                'charter_shipping_price' => 60000,
            ],
            [
                'region_id' => 4,
                'name' => '栃木県',
                'charter_shipping_price' => 60000,
            ],
            [
                'region_id' => 4,
                'name' => '群馬県',
                'charter_shipping_price' => 50000,
            ],
            [
                'region_id' => 4,
                'name' => '埼玉県',
                'charter_shipping_price' => 70000,
            ],
            [
                'region_id' => 4,
                'name' => '千葉県',
                'charter_shipping_price' => 50000,
            ],
            [
                'region_id' => 4,
                'name' => '東京都',
                'charter_shipping_price' => 50000,
            ],
            [
                'region_id' => 4,
                'name' => '神奈川県',
                'charter_shipping_price' => 50000,
            ],
            [
                'region_id' => 4,
                'name' => '新潟県',
                'charter_shipping_price' => 70000,
            ],
            [
                'region_id' => 5,
                'name' => '富山県',
                'charter_shipping_price' => 55000,
            ],
            [
                'region_id' => 5,
                'name' => '石川県',
                'charter_shipping_price' => 55000,
            ],
            [
                'region_id' => 5,
                'name' => '福井県',
                'charter_shipping_price' => 55000,
            ],
            [
                'region_id' => 4,
                'name' => '山梨県',
                'charter_shipping_price' => 50000,
            ],
            [
                'region_id' => 5,
                'name' => '長野県',
                'charter_shipping_price' => 55000,
            ],
            [
                'region_id' => 6,
                'name' => '岐阜県',
                'charter_shipping_price' => 45000,
            ],
            [
                'region_id' => 6,
                'name' => '静岡県',
                'charter_shipping_price' => 45000,
            ],
            [
                'region_id' => 6,
                'name' => '愛知県',
                'charter_shipping_price' => 45000,
            ],
            [
                'region_id' => 6,
                'name' => '三重県',
                'charter_shipping_price' => 45000,
            ],
            [
                'region_id' => 7,
                'name' => '滋賀県',
                'charter_shipping_price' => 50000,
            ],
            [
                'region_id' => 7,
                'name' => '京都府',
                'charter_shipping_price' => 50000,
            ],
            [
                'region_id' => 7,
                'name' => '大阪府',
                'charter_shipping_price' => 50000,
            ],
            [
                'region_id' => 7,
                'name' => '兵庫県',
                'charter_shipping_price' => 50000,
            ],
            [
                'region_id' => 7,
                'name' => '奈良県',
                'charter_shipping_price' => 50000,
            ],
            [
                'region_id' => 7,
                'name' => '和歌山県',
                'charter_shipping_price' => 50000,
            ],
            [
                'region_id' => 8,
                'name' => '鳥取県',
                'charter_shipping_price' => 70000,
            ],
            [
                'region_id' => 8,
                'name' => '島根県',
                'charter_shipping_price' => 80000,
            ],
            [
                'region_id' => 8,
                'name' => '岡山県',
                'charter_shipping_price' => 60000,
            ],
            [
                'region_id' => 8,
                'name' => '広島県',
                'charter_shipping_price' => 80000,
            ],
            [
                'region_id' => 8,
                'name' => '山口県',
                'charter_shipping_price' => 90000,
            ],
            [
                'region_id' => 9,
                'name' => '徳島県',
                'charter_shipping_price' => 80000,
            ],
            [
                'region_id' => 9,
                'name' => '香川県',
                'charter_shipping_price' => 80000,
            ],
            [
                'region_id' => 9,
                'name' => '愛媛県',
                'charter_shipping_price' => 80000,
            ],
            [
                'region_id' => 9,
                'name' => '高知県',
                'charter_shipping_price' => 80000,
            ],
            [
                'region_id' => 10,
                'name' => '福岡県',
                'charter_shipping_price' => 100000,
            ],
            [
                'region_id' => 10,
                'name' => '佐賀県',
                'charter_shipping_price' => 100000,
            ],
            [
                'region_id' => 10,
                'name' => '長崎県',
                'charter_shipping_price' => 100000,
            ],
            [
                'region_id' => 11,
                'name' => '熊本県',
                'charter_shipping_price' => 150000,
            ],
            [
                'region_id' => 10,
                'name' => '大分県',
                'charter_shipping_price' => 100000,
            ],
            [
                'region_id' => 11,
                'name' => '宮崎県',
                'charter_shipping_price' => 150000,
            ],
            [
                'region_id' => 11,
                'name' => '鹿児島県',
                'charter_shipping_price' => 150000,
            ],
            [
                'region_id' => 12,
                'name' => '沖縄県',
                'charter_shipping_price' => null,
            ],
            [
                'region_id' => null,
                'name' => '',
                'charter_shipping_price' => null,
            ],
        ]);
    }
}
