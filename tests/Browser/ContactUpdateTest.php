<?php

namespace tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ContactUpdateTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testUpdateContact()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/contact/edit/57')
                ->select('c[contact_type_id]', 1)
                ->type('c[free_sample]', '請求済み')
                ->type('@surname', 'サンプル')
                ->type('@name', '太郎')
                ->type('@surname-ruby', 'さんぷる')
                ->type('@name-ruby', 'たろう')
                ->select('c[user_id]', 9)
                ->type('c[zipcode]', 7700882)
                ->type('c[pref]', 'サンプル県')
                ->type('c[city]', 'サンプル市')
                ->type('c[street]', 'サンプル通り1-1-1')
                ->type('c[tel]', '09022224444')
                ->type('c[fax]', '08633428454')
                ->type('c[email]', 'edit_test@example.com')
                ->type('c[age]', '1980年代')
                ->type('c[ground_condition]', 'コンクリート')
                ->type('c[vertical_size]', 30)
                ->type('c[horizontal_size]', 5)
                ->type('c[desired_product]', '人工芝A')
                ->type('c[visit_address]', 'サンプル県サンプル市中央1-1-1')
                ->type('c[use_application]', 'グラウンドの改造')
                ->type('c[where_find]', 'インターネット')
                ->type('c[sns]', 'facebook')
                ->type('c[comment]', 'コメント編集')
                ->type('c[requirement]', '必要事項編集')
                ->screenshot('sample-send')
                ->click('.common__update')
                ->pause(3000)
                ->assertSee('お問い合わせ内容が更新されました')
                ->visit('/contact/57')
                ->screenshot('visit-after-edit')
                ->assertSee('サンプル太郎')
                ->assertSee('さんぷるたろう')
                ->assertSee('未送付')
                ->assertSee('グラウンドの改造');
        });
    }

    public function testContactImageEdit()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(1))
                ->visit('/contact/123')
                ->assertSee('施工前画像')
                ->assertSee('施工後画像')
                ->assertSee('案件詳細')
                ->assertSee('編集')
                ->click('@edit')
                ->assertSee('施工前画像')
                ->assertSee('施工後画像')
                // 画像を選択→アップロード
                ->attach('#js-before-image1', storage_path('app/public/testing/before1.jpg'))
                ->attach('#js-before-image2', storage_path('app/public/testing/before2.jpg'))
                ->attach('#js-before-image3', storage_path('app/public/testing/before3.jpg'))
                ->attach('#js-after-image1', storage_path('app/public/testing/after1.jpg'))
                ->attach('#js-after-image2', storage_path('app/public/testing/after2.jpg'))
                ->attach('#js-after-image3', storage_path('app/public/testing/after3.jpg'))
                ->screenshot('contactUpdateImageAttach')
                ->click('@update-button')
                // アップロードされていることを確認
                ->assertVisible('#before1')
                ->assertVisible('#before2')
                ->assertVisible('#before3')
                ->assertVisible('#after1')
                ->assertVisible('#after2')
                ->assertVisible('#after3')
                ->click('@edit')
                ->click('@before-image-remove1')
                ->click('@after-image-remove2')
                ->click('@update-button')
                // 削除を確認
                ->assertMissing('#before1')
                ->assertVisible('#before2')
                ->assertVisible('#before3')
                ->assertVisible('#after1')
                ->assertMissing('#after2')
                ->assertVisible('#after3');
        });
    }

    public function testCancelContact()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/contact/4')
                ->click('@cancel')
                ->acceptDialog()
                ->assertSee('キャンセルに設定しました');
        });
    }

    public function testOtherFc()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(3))
                ->visit('/contact/11')
                ->assertPathIs('/contact/assigned/list')
                ->assertSee('他FCの案件にはアクセスできません');
        });
    }

    public function assignByUpdate()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/contact/edit/140')
                ->select('@select-fc', 2)
                ->click('@update-button')
                ->assertSee('お問い合わせ内容が更新されました');
            $browser->loginAs(User::find(2))
                ->visit('/contact/assigned/list')
                ->assertSeeLink('140')
                ->clickLink('140')
                ->assertSeeLink('依頼日');
        });
    }
}
