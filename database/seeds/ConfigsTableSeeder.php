<?php

use Illuminate\Database\Seeder;

class ConfigsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('config')->truncate();
        DB::table('config')->insert([
            [
              //案件に紐づいたsub発注の制限数
              'id' => 1,
              'name' => 'transactionsub_limit',
              'key' => 'transaction_sub_limit',
              'value' => '2'
            ],
            [
              // FCが案件詳細を見ずに放置した日数
              'id' => 2,
              'name' => 'FC放置日数',
              'key' => 'leave_alone_days',
              'value' => 3
            ],
            [
              // 人工芝切り売り時に GOLF以外では出さない商品ID（' , ' でIDを区切る）
              'id' => 3,
              'name' => '人工芝切り売り時に GOLF以外では出さない商品ID（カンマでIDを区切る）',
              'key' => 'transaction_cut_turf_invisible_ids',
              'value' => '55,56,71'
            ],
            [
              // エリア開放確認メールを送る日数（○日前）
              'id' => 4,
              'name' => 'エリア開放確認メールを送る日数（○日前）',
              'key' => 'pre_area_open_email_days',
              'value' => 7
            ],
            // ここからお問い合わせ詳細分析用
            [
              'id' => 5,
              'name' => 'お問い合わせの年代',
              'key' => 'contact_detail_ages',
              'value' => "1910年代,1920年代,1930年代,1940年代,1950年代,1960年代,1970年代,1980年代,1990年代,2000年代,2010年代",
            ],
            [
              'id' => 6,
              'name' => '人工芝の使用用途',
              'key' => 'contact_detail_turf_purpose',
              'value' => "雑草対策,ペット・ドッグラン用,スポーツ,ゴルフ,室内,景観目的,その他",
            ],
            [
              'id' => 7,
              'name' => 'サンプルFCをどこでお知りになりましたか？',
              'key' => 'contact_detail_where_find',
              'value' => "検索,ラジオ,チラシ,紹介・口コミ,Google広告,インスタ・facebook広告,その他WEB広告,通販サイト（楽天・アマゾンなど）,その他",
            ],
            [
              'id' => 8,
              'name' => '現在使用しているSNSはありますか？',
              'key' => 'contact_detail_sns',
              'value' => "Instagram,Facebook,Twitter,LINE,その他",
            ],
            [
              'id' => 9,
              'name' => '送料無料キャンペーン商品ID',
              'key' => 'free_shipping_product_id',
              'value' => '["80"]',
            ],
      ]);
    }
}
