<?php

use Illuminate\Database\Seeder;

class ShippingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('shippings')->insert([
            [
                'transport_company' => '西濃運輸',
                'trakking_url' => 'http://track.seino.co.jp/cgi-bin/gnpquery.pgm',
            ],
            [
                'transport_company' => 'ヤマト運輸',
                'trakking_url' => 'https://www.plus-a.net/d/y/?n=',
            ],
            [
                'transport_company' => '佐川急便',
                'trakking_url' => 'https://www.plus-a.net/d/s/?n=',
            ],
            [
                'transport_company' => '日本郵政',
                'trakking_url' => 'https://www.plus-a.net/d/p/?n=',
            ],
            [
                'transport_company' => 'チャーター便',
                'trakking_url' => '',
            ],
        ]);
    }
}
