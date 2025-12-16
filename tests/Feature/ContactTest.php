<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Contact;

class ContactTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function testContactInsert()
    {
        //第二引数にPOST値の配列を渡すだけ
        $response = $this->post('/api/contact/store', [
            'user_id' => 1,
            'step_id' => 1,
            'shipping_id' => 1,
            'name' => 'UNIT TEST',
            'company_ruby' => 'ゆにっとてすと',
            'free_sample' => 1,
            'email' => 'user1@example.com',
            'fax' => '090-0000-0000',
            'tel' => '090-0000-0000',
            'zipcode' => '1000001',
            'pref' => 'サンプル都',
            'city' => 'サンプル中央区',
            'street' => 'サンプルタウン1-1-1 サンプルビル1F',
            'personal' => 1,
            'ground_condition' => '土',
            'desired_datetime1' => '2019-09-27 00:00:00',
            'desired_datetime2' => '2019-09-28 00:00:00',
            'finished_datetime' => '2019-10-02 00:00:00',
            'industry' => 'イベント企画',
            'quote_details' => '見積もり内容',
            'vertical_size' => 5,
            'horizontal_size' => 10,
            'desired_product' => '希望商品',
            'file' => 'ファイル',
            'comment' => '備考欄',
            'birth' => '19701010',
            'requirement' => 'ご要件',
            'where_find' => 'インターネット広告',
            'sns' => 'facebook',
            'before_image1' => '施工前画像1',
            'before_image2' => '施工前画像1',
            'before_image3' => '施工前画像1',
            'after_image1' => '施工後画像1',
            'after_image2' => '施工後画像1',
            'after_image3' => '施工後画像1',
            'status' => 1,
        ]);
        $data = Contact::where('status', 1)->first();
        //dd($data);
        $this->assertTrue(!empty($data));
    }
}
