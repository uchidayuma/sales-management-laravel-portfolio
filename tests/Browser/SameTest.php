<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class SameTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testPersonalSameContact()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                //新規問い合わせ電話受付
                ->visit('/contact/new')
                ->assertSee('新規顧客登録')
                ->check('@type2')
                ->pause(1000)
                ->radio('c[free_sample]', '請求済み')
                ->pause(1000)
                ->type('@p2-surname', 'テスト')->type('@p2-name', 'カエル')
                ->type('@p2-surname_ruby', 'てすと')->type('@p2-name_ruby', 'かえる')
                ->type('@p2-zipcode', 1000006)->type('@p2-pref', 'サンプル都')->type('@p2-city', 'サンプル北区')->pause(2000)->type('@p2-street', 'サンプル通り4番地')
                ->type('@p2-email', 'test@gmail.com')
                ->type('@p2-tel', '09012345678')
                ->select('@p2-age', 1960)
                ->check('#p2-ground1')->check('#p2-ground3')->check('#p2-ground2')->type('c[ground_condition][etc]', 'その他グランドコンティション')
                ->type('@p2-vertical_size', 30)->type('@p2-horizontal_size', 20)
                ->check('#p2-product1')->check('#p2-product3')->check('#p2-product4')
                ->check('#p2-use1')->check('#p2-use3')->check('#p2-use4')->type('@p2-use_application-etc', 'その他花壇の整備')
                ->type('@p2-comment', 'dusk test ご要望備考欄')
                ->press('@contact2-submit')
                ->assertSee('新しいお問い合わせを登録しました。')
                ->visit('/contact/50')
                ->screenshot('same-contact')
                ->assertVisible('@same-link');
        });
    }

    // 個人名前とメールアドレス一致確認
    public function testPersonalSameNameEmailContact()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                //新規問い合わせ電話受付
                ->visit('/contact/new')
                ->assertSee('新規顧客登録')
                ->check('@type2')
                ->pause(1000)
                ->radio('c[free_sample]', '請求済み')
                ->pause(5000)
                ->type('@p2-surname', '名前とメールアドレス')->type('@p2-name', '同一顧客')
                ->type('@p2-email', 'nameemail@gmail.com')
                ->type('@p2-surname_ruby', 'てすと')->type('@p2-name_ruby', 'かえる')
                ->type('@p2-zipcode', 1000006)->type('@p2-pref', 'サンプル都')->type('@p2-city', 'サンプル西区')->pause(2000)->type('@p2-street', '4番地')
                ->type('@p2-tel', '09012345678')
                ->select('@p2-age', 1960)
                ->check('#p2-ground1')->check('#p2-ground3')->check('#p2-ground2')->type('c[ground_condition][etc]', 'その他グランドコンティション')
                ->type('@p2-vertical_size', 30)->type('@p2-horizontal_size', 20)
                ->check('#p2-product1')->check('#p2-product3')->check('#p2-product4')
                ->check('#p2-use1')->check('#p2-use3')->check('#p2-use4')->type('@p2-use_application-etc', 'その他花壇の整備')
                ->type('@p2-comment', 'dusk test ご要望備考欄')
                ->press('@contact2-submit')
                ->assertSee('新しいお問い合わせを登録しました。')
                ->visit('/contact/51')
                ->pause(10000)
                ->screenshot('same-contact')
                ->assertVisible('@same-link');
        });
    }

    // 個人名前と電話番号一致確認
    public function testPersonalSameNameTelContact()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                //新規問い合わせ電話受付
                ->visit('/contact/new')
                ->assertSee('新規顧客登録')
                ->check('@type2')
                ->pause(1000)
                ->radio('c[free_sample]', '請求済み')
                ->pause(1000)
                ->type('@p2-surname', 'テスト')->type('@p2-name', 'カエル')
                ->type('@p2-email', 'tests@gmail.com')
                ->type('@p2-surname_ruby', 'てすと')->type('@p2-name_ruby', 'かえる')
                ->type('@p2-zipcode', 1000001)->type('@p2-pref', 'サンプル都')->type('@p2-city', 'サンプル中央区')->pause(2000)->type('@p2-street', '4番地')
                ->type('@p2-tel', '09012345678')
                ->select('@p2-age', 1960)
                ->check('#p2-ground1')->check('#p2-ground3')->check('#p2-ground2')->type('c[ground_condition][etc]', 'その他グランドコンティション')
                ->type('@p2-vertical_size', 30)->type('@p2-horizontal_size', 20)
                ->check('#p2-product1')->check('#p2-product3')->check('#p2-product4')
                ->check('#p2-use1')->check('#p2-use3')->check('#p2-use4')->type('@p2-use_application-etc', 'その他花壇の整備')
                ->type('@p2-comment', 'dusk test ご要望備考欄')
                ->press('@contact2-submit')
                ->assertPathIs('/contact/customers/list')
                ->visit('/contact/50')
                ->screenshot('same-contact')
                ->assertVisible('@same-link');
        });
    }

    // 同じ会社のテスト
    public function testCompanySameContact()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                //新規問い合わせ電話受付
                ->visit('/contact/new')
                ->assertSee('新規顧客登録')
                ->check('@type6')
                ->pause(1000)
                ->radio('#h2-freesample1', '必要')
                ->pause(1000)
                ->type('@h2-company_name', '株式会社ルートプラス')
                ->type('@h2-company_ruby', 'かぶしきがいしゃルートプラス')
                ->type('@h2-surname', 'DUSK担当者')
                ->type('@h2-industry', 'IT業界')
                ->type('@h2-zipcode', 1000001)->type('@h2-pref', 'サンプル都')->type('@h2-city', 'サンプル中央区')->type('@h2-street', 'サンプルタウン1-1-1 サンプルビル1F')
                ->type('@h2-tel', '050-3561-2247')
                ->type('@h2-tel2', '090-1111-5555')
                ->check('#h2-ground1')->check('#h2-ground3')->check('#h2-ground2')->type('@h2-ground_condition-etc', 'その他グランドコンティション')
                ->type('@h2-vertical_size', 30)->type('@h2-horizontal_size', 20)
                ->check('#h2-product1')->check('#h2-product3')->check('#h2-product4')
                ->check('#h2-use1')->check('#h2-use3')->check('#h2-use4')->type('@h2-use_application-etc', 'その他花壇の整備')
                ->check('#h2-where1')->check('#h2-where2')->check('#h2-where3')->type('@h2-where_find-etc', 'その他インターネット')
                ->type('@h2-comment', 'dusk test 法人図面見積もりご要望備考欄')
                ->press('@contact6-submit')
                ->assertPathIs('/contact/customers/list');
            // ->assertSee('新しいお問い合わせを登録しました。')
            // ->visit('/contact/48')
            // ->screenshot('contact-new-samelink')
            // ->assertVisible('@same-link');
        });
    }

    // 同一顧客紐付け手動編集テスト
    public function testSameContactEdit()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                //新規問い合わせ電話受付
                ->visit('/contact/new')
                ->assertSee('新規顧客登録')
                ->check('@type6')
                ->pause(1000)
                ->radio('#h2-freesample1', '必要')
                ->pause(1000)
                ->type('@h2-company_name', '株式会社ルートプラス')
                ->type('@h2-company_ruby', 'かぶしきがいしゃルートプラス')
                ->type('@h2-surname', 'DUSK担当者')
                ->type('@h2-industry', 'IT業界')
                ->type('@h2-zipcode', 1000001)->type('@h2-pref', 'サンプル都')->type('@h2-city', 'サンプル中央区')->type('@h2-street', 'サンプルタウン1-1-1 サンプルビル1F')
                ->type('@h2-email', 'test@gmail.com')
                ->type('@h2-tel', '050-3561-2247')
                ->type('@h2-tel2', '090-1111-5555')
                ->check('#h2-ground1')->check('#h2-ground3')->check('#h2-ground2')->type('@h2-ground_condition-etc', 'その他グランドコンティション')
                ->type('@h2-vertical_size', 30)->type('@h2-horizontal_size', 20)
                ->check('#h2-product1')->check('#h2-product3')->check('#h2-product4')
                ->check('#h2-use1')->check('#h2-use3')->check('#h2-use4')->type('@h2-use_application-etc', 'その他花壇の整備')
                ->check('#h2-where1')->check('#h2-where2')->check('#h2-where3')->type('@h2-where_find-etc', 'その他インターネット')
                ->type('@h2-comment', 'dusk test 法人図面見積もりご要望備考欄')
                ->press('@contact6-submit')
                ->assertSee('新しいお問い合わせを登録しました。')
                ->visit('/contact/48')
                ->assertVisible('@same-link')
                ->visit('/contact/edit/48')
                ->type('@same-input', '53,55')
                ->click('@update-button')
                ->visit('/contact/48')
                ->assertSeeLink('53')
                ->assertSeeLink('55');
        });
    }

    // 個人名と住所一致確認
    public function testPersonalSameCurNameAddress()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                //新規問い合わせ電話受付
                ->visit('/contact/new')
                ->assertSee('新規顧客登録')
                ->check('@type2')
                ->pause(1000)
                ->radio('c[free_sample]', '請求済み')
                ->pause(1000)
                ->type('@p2-surname', 'テスト')->type('@p2-name', 'カエル')
                ->type('@p2-email', '')
                ->type('@p2-surname_ruby', 'てすと')->type('@p2-name_ruby', 'かえる')
                ->type('@p2-zipcode', 1000006)->type('@p2-pref', 'サンプル都')->type('@p2-city', 'サンプル北区')->pause(2000)->type('@p2-street', '桜台4丁')
                ->type('@p2-tel', '2102414101201dsfs11222')
                ->select('@p2-age', 1960)
                ->check('#p2-ground1')->check('#p2-ground3')->check('#p2-ground2')->type('c[ground_condition][etc]', 'その他グランドコンティション')
                ->type('@p2-vertical_size', 30)->type('@p2-horizontal_size', 20)
                ->check('#p2-product1')->check('#p2-product3')->check('#p2-product4')
                ->check('#p2-use1')->check('#p2-use3')->check('#p2-use4')->type('@p2-use_application-etc', 'その他花壇の整備')
                ->type('@p2-comment', 'dusk test ご要望備考欄')
                ->press('@contact2-submit')
                ->assertPathIs('/contact/customers/list')
                ->click('@contact-detail')
                ->screenshot('same-contact')
                ->pause(3000)
                ->assertVisible('@same-link');
        });
    }

    // 同一顧客紐付け削除テスト
    public function testSameContactDelete()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/contact/edit/80')
                ->type('@same-input', '77')
                ->click('@update-button')
                ->pause(1000)
                ->assertSeeLink('77')
                ->click('@delete')
                ->acceptDialog()
                ->pause(1000)
                ->assertSee('お問い合わせを削除しました')
                ->pause(1000)
                ->visit('/contact/77')
                ->pause(1000)
                ->assertDontSeeLink('80')
                ->visit('/contact/80')
                ->assertSee('この案件は存在しません')
                ->screenshot('delete');
        });
    }
}
