<?php

use Illuminate\Database\Seeder;

class NotificationTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('notification_types')->truncate();
        DB::table('notification_types')->insert([
        [
          'id' => 1,
          'name' => 'アポイント未取得',
          'url' => '/contact/assigned/list',
          'step_id' => 2,
          'period' => 3,
        ],
        [
          'id' => 2,
          'name' => '見積もり未提出',
          'url' => '/contact/quotations/needs',
          'step_id' => 3,
          'period' => 3,
        ],
        [
          'id' => 3,
          'name' => '顧客の回答未入力',
          'url' => '/contact/pending/list',
          'step_id' => 4,
          'period' => 3,
        ],
      ]);
    }
}
