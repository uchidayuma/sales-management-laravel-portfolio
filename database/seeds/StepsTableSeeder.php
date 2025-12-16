<?php

use Illuminate\Database\Seeder;

class StepsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('steps')->truncate();
        DB::table('steps')->insert([
          [
              'id' => 1,
              'name' => 'お問い合わせが来た && フランチャイズへの見積もり依頼を本部が行うフェーズ',
              'alias' => 'STEP_ASSIGN',
          ],
          [
              'id' => 2,
              'name' => 'FCから顧客へのアポイントを取得',
              'alias' => 'STEP_APPOINT',
          ],
          [
              'id' => 3,
              'name' => '顧客宅の現場確認報告',
              'alias' => 'STEP_ONSITE_CONFIRM',
          ],
          [
              'id' => 4,
              'name' => '見積もり作成',
              'alias' => 'STEP_QUOTATION',
          ],
          [
              'id' => 5,
              'name' => '商談の結果入力',
              'alias' => 'STEP_RESULT',
          ],
          [
              'id' => 6,
              'name' => 'FCから本部に部材発注',
              'alias' => 'STEP_TRANSACTION',
          ],
          [
              'id' => 7,
              'name' => '本部が発送費用を連絡',
              'alias' => 'STEP_SHIPPING_COST_INPUT',
          ],
          [
              'id' => 8,
              'name' => 'FCが送料含めた金額を確認＆支払い',
              'alias' => 'STEP_FC_PAYMENT',
          ],
          [
              'id' => 9,
              'name' => '本部がFCに発送連絡',
              'alias' => 'STEP_SHIPPING',
          ],
          [
              'id' => 10,
              'name' => 'FCが施工完了',
              'alias' => 'STEP_COMPLETE',
          ],
          [
              'id' => 11,
              'name' => 'FCが施工報告完了',
              'alias' => 'STEP_REPORT_COMPLETE',
          ],
          //依頼主の都合で途中でキャンセルになった場合も対応できると良いのではないでしょうか？
          [
            'id' => 99,
            'name' => 'キャンセル',
            'alias' => 'STEP_CANCELLATION',
          ],
          [
            'id' => 100,
            'name' => '過去顧客',
            'alias' => 'STEP_PAST_CUSTOMERS',
          ],
      ]);
    }
}
