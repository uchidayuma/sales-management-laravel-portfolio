<?php

use Illuminate\Database\Seeder;

class FcApplyAreasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('fc_apply_areas')->truncate();
        DB::table('fc_apply_areas')->insert([
            [
              'name' => '生駒エリア',
              'content' => '（生駒市・大和高田市・平郡町・大和郡山市・三郷町・王寺町・上牧町・香芝市・広陵町・川西町・三宅町・田原本町・斑鳩町・安堵町）'
            ],
            [
              'name' => '福島エリア',
              'content' => '（福島市・南相馬市・相馬市・浪江町・葛尾村・川内村・飯館村・伊達市・田村市・双葉町・大熊町・富岡町・広野町・檜葉町・新地町・国見町・桑折町・川俣町）'
            ],
            [
              'name' => '横須賀エリア',
              'content' => '（横須賀市・三浦市・鎌倉市・逗子市・葉山町）'
            ],
            [
              'name' => '那覇エリア',
              'content' => '（那覇市・南風原町・豊見城市・与那原町・南城市・八重瀬町・糸満市）'
            ],
            [
              'name' => '豊田エリア',
              'content' => '（豊田市・瀬戸市・尾張旭市・長久手市・日進市・東郷町・みよし市・豊明市）'
            ],
        ]);
    }
}
