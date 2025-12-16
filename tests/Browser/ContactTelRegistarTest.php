<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ContactTelRegistarTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    // 個人サンプル請求
    public function testCreatePersonalSample()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                //新規問い合わせ電話受付
                ->visit('/contact/new')
                ->assertSee('新規顧客登録')
                ->check('@type1')
                ->type('@p1-surname', 'サンプル')->type('@p1-name', '花子')
                ->type('@p1-surname_ruby', 'さんぷる')->type('@p1-name_ruby', 'はなこ')
                ->type('@p1-zipcode', 1000003)->type('@p1-pref', 'サンプル県')->type('@p1-city', 'サンプル市')->type('@p1-street', 'サンプルタウン1-1-1')
                ->type('@p1-tel', '09022224444')
                ->type('@p1-tel2', '08011112222')
                ->type('@p1-fax', '08633428454')
                ->type('@p1-email', 'test@example.com')
                ->select('@p1-age', 1980)
                ->check('#p1-use1')->check('#p1-use3')->type('@p1-use_application-etc', 'その他花壇の整備')
                ->check('#p1-where1')->check('#p1-where2')->type('@p1-where_find-etc', 'その他インターネット')
                ->check('#p1-sns1')->check('#p1-sns2')->check('#p1-sns3')->type('@p1-sns-etc', 'その他SNS')
                ->press('@contact1-submit')
                ->pause(2000)
                ->assertSee('サンプル花子')
                ->assertSee('サンプル市')
                ->assertPathIs('/contact/customers/list');
        });
    }

    // 個人図面見積もり
    public function testCreatePersonalDrow()
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
                ->type('@p2-surname', 'サンプル')->type('@p2-name', '花子')
                ->type('@p2-surname_ruby', 'さんぷる')->type('@p2-name_ruby', 'はなこ')
                ->type('@p2-zipcode', 1000003)->type('@p2-pref', 'サンプル県')->type('@p2-city', 'サンプル市')->type('@p2-street', 'サンプルタウン1-1-1')
                ->type('@p2-tel', '09022224444')
                ->type('@p2-tel2', '08011112222')
                ->select('@p2-age', 1960)
                ->check('#p2-ground1')->check('#p2-ground3')->check('#p2-ground2')->type('c[ground_condition][etc]', 'その他グランドコンティション')
                ->type('@p2-vertical_size', 30)->type('@p2-horizontal_size', 20)
                ->check('#p2-product1')->check('#p2-product3')->check('#p2-product4')
                ->check('#p2-use1')->check('#p2-use3')->check('#p2-use4')->type('@p2-use_application-etc', 'その他花壇の整備')
                ->type('@p2-comment', 'dusk test ご要望備考欄')
                ->screenshot('tel2-registar')
                ->press('@contact2-submit')
                ->pause(2000)
                ->assertPathIs('/contact/customers/list')
                ->assertSee('新しいお問い合わせを登録しました。')
                ->assertSee('サンプル市サンプルタウン1-1-1')
                ->assertSee('サンプル花子')
                ->press('@contact-detail')
                ->pause(2000)
                ->screenshot('visit-detail')
                ->assertSee('08011112222');
        });
    }

    // 個人訪問見積もり
    public function testCreatePersonalVisit()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                //新規問い合わせ電話受付
                ->visit('/contact/new')
                ->assertSee('新規顧客登録')
                ->check('@type3')
                ->pause(3000)
                ->radio('#p3-freesample3', '不要')
                ->pause(1000)
                ->type('@p3-surname', 'サンプル')->type('@p3-name', '裕馬')
                ->type('@p3-surname_ruby', 'さんぷる')->type('@p3-name_ruby', 'ゆうま')
                ->type('@p3-zipcode', 1000003)->type('@p3-pref', 'サンプル県')->type('@p3-city', 'サンプル市')->type('@p3-street', 'サンプルタウン1-1-1')
                ->type('@p3-tel', '09022224444')
                ->type('@p3-tel2', '09011115555')
                ->type('@p3-desired_datetime1-date', '2020-04-21')
                ->select('@p3-desired_datetime1-time', 13)
                ->type('@p3-desired_datetime2-date', '2020-04-23')
                ->select('@p3-desired_datetime2-time', 17)
                ->type('@p3-visit_address', 'サンプル県サンプル市中央2-2-2')
                ->check('#p3-ground1')->check('#p3-ground3')->check('#p3-ground2')->type('@p3-ground_condition-etc', 'その他グランドコンティション')
                ->check('#p3-use1')->type('@p3-use_application-etc', 'その他花壇の整備')
                ->type('@p3-comment', 'dusk test 個人訪問ご要望備考欄')
                ->screenshot('tel3-registar')
                ->press('@contact3-submit')
                ->pause(2000)
                ->assertPathIs('/contact/customers/list')
                ->assertSee('新しいお問い合わせを登録しました。')
                // 改行したので、市町村いかに変更
                ->assertSee('サンプル市サンプルタウン1-1-1')
                ->assertSee('サンプル裕馬')
                ->press('@contact-detail')
                ->pause(2000)
                ->screenshot('visit-detail')
                ->assertSee('09011115555');
        });
    }

    // 個人その他
    public function testCreatePersonalEtc()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                //新規問い合わせ電話受付
                ->visit('/contact/new')
                ->assertSee('新規顧客登録')
                ->check('@type4')
                ->pause(3000)
                ->radio('#p4-freesample3', '不要')
                ->pause(1000)
                ->type('@p4-surname', 'サンプル')->type('@p4-name', 'その他')
                ->type('@p4-surname_ruby', 'さんぷる')->type('@p4-name_ruby', 'そのた')
                ->type('@p4-zipcode', 1000003)->type('@p4-pref', 'サンプル県')->type('@p4-city', 'サンプル市')->type('@p4-street', 'サンプルタウン1-1-1')
                ->type('@p4-tel', '09022224444')
                ->type('@p4-tel2', '09011115555')
                ->check('#p4-quote1')->check('#p4-quote3')->check('#p4-quote2')
                ->type('@p4-comment', 'dusk test 個人その他ご要望備考欄')
                ->screenshot('tel4-registar')
                ->press('@contact4-submit')
                ->pause(2000)
                ->assertPathIs('/contact/customers/list')
                ->assertSee('新しいお問い合わせを登録しました。')
                ->assertSee('サンプル市サンプルタウン1-1-1')
                ->assertSee('サンプルその他')
                ->press('@contact-detail')
                ->pause(2000)
                ->screenshot('visit-detail')
                ->assertSee('09011115555');
        });
    }

    // 法人サンプル請求
    public function testCreateCompanySample()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                //新規問い合わせ電話受付
                ->visit('/contact/new')
                ->maximize()
                ->assertSee('新規顧客登録')
                ->check('@type5')
                ->type('@h1-company_name', 'DuskTest株式会社')
                ->type('@h1-company_ruby', 'だすくてすとかぶしきがいしゃ')
                ->type('@h1-surname', 'DUSK担当者')
                ->type('@h1-surname-ruby', 'ダースクタントウシャ')
                ->type('@h1-industry', 'IT業界')
                ->type('@h1-zipcode', 1000001)->type('@h1-pref', 'サンプル都')->type('@h1-city', 'サンプル中央区')->type('@h1-street', 'サンプルタウン1-1-1 サンプルビル1F')
                ->type('@h1-tel', '050-3561-2247')
                ->type('@h1-tel2', '090-1111-5555')
                ->check('#h1-use1')->check('#h1-use3')->check('#h1-use4')->type('@h1-use_application-etc', 'その他花壇の整備')
                ->check('#h1-where1')->check('#h1-where2')->check('#h1-where3')->type('@h1-where_find-etc', 'その他インターネット')
                ->screenshot('tel5-registar')
                ->press('@contact5-submit')
                ->pause(2000)
                ->assertPathIs('/contact/customers/list');
        });
    }

    // 法人図面見積もり
    public function testCreateCompanyDrow()
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
                ->type('@h2-company_name', 'DuskTest株式会社')
                ->type('@h2-company_ruby', 'だすくてすとかぶしきがいしゃ')
                ->type('@h2-surname', 'DUSK担当者')
                ->type('@h2-surname-ruby', 'ダースクタントウシャ')
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
                ->screenshot('tel6-registar')
                ->press('@contact6-submit')
                ->pause(2000)
                ->assertPathIs('/contact/customers/list')
                ->assertSee('新しいお問い合わせを登録しました。')
                ->assertSee('DuskTest株式会社')
                ->press('@contact-detail')
                ->pause(2000)
                ->screenshot('visit-detail')
                ->assertSee('090-1111-5555');
        });
    }

    // 法人訪問見積もり
    public function testCreateCompanyVisit()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                //新規問い合わせ電話受付
                ->visit('/contact/new')
                ->assertSee('新規顧客登録')
                ->check('@type7')
                ->pause(3000)
                ->radio('#h3-freesample3', '不要')
                ->pause(1000)
                ->type('@h3-company_name', 'DuskTest株式会社')
                ->type('@h3-company_ruby', 'だすくてすとかぶしきがいしゃ')
                ->type('@h3-surname', 'DUSK担当者')
                ->type('@h3-surname-ruby', 'ダースクタントウシャ')
                ->type('@h3-industry', 'IT業界')
                ->type('@h3-zipcode', 1000003)->type('@h3-pref', 'サンプル県')->type('@h3-city', 'サンプル市')->type('@h3-street', 'サンプルタウン1-1-1')
                ->type('@h3-tel', '090-2222-4444')
                ->type('@h3-tel2', '090-1111-5555')
                ->type('@h3-desired_datetime1-date', '2020-04-21')
                ->select('@h3-desired_datetime1-time', 13)
                ->type('@h3-desired_datetime2-date', '2020-04-23')
                ->select('@h3-desired_datetime2-time', 17)
                ->type('@h3-visit_address', 'サンプル県サンプル市中央2-2-2')
                ->check('#h3-ground1')->check('#h3-ground3')->check('#h3-ground2')->type('@h3-ground_condition-etc', 'その他グランドコンティション')
                ->check('#h3-use1')->type('@h3-use_application-etc', 'その他花壇の整備')
                ->type('@h3-comment', 'dusk test 法人訪問ご要望備考欄')
                ->screenshot('tel7-registar')
                ->press('@contact7-submit')
                ->pause(2000)
                ->assertPathIs('/contact/customers/list')
                ->assertSee('新しいお問い合わせを登録しました。')
                ->assertSee('サンプル市サンプルタウン1-1-1')
                ->press('@contact-detail')
                ->pause(2000)
                ->screenshot('visit-detail')
                ->assertSee('090-1111-5555');
        });
    }

    // 法人その他
    public function testCreateCompanyEtc()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                //新規問い合わせ電話受付
                ->visit('/contact/new')
                ->assertSee('新規顧客登録')
                ->check('@type8')
                ->pause(3000)
                ->radio('#h4-freesample3', '不要')
                ->pause(1000)
                ->type('@h4-company_name', 'DuskTest株式会社')
                ->type('@h4-company_ruby', 'だすくてすとかぶしきがいしゃ')
                ->type('@h4-surname', 'DUSK担当者')
                ->type('@h4-surname-ruby', 'ダースクタントウシャ')
                ->type('@h4-industry', 'IT業界')
                ->type('@h4-zipcode', 1000003)->type('@h4-pref', 'サンプル県')->type('@h4-city', 'サンプル市')->type('@h4-street', 'サンプルタウン1-1-1')
                ->type('@h4-tel', '090-2222-4444')
                ->type('@h4-tel2', '090-1111-5555')
                ->check('#h4-quote1')->check('#h4-quote3')->check('#h4-quote2')
                ->type('@h4-comment', 'dusk test 法人その他ご要望備考欄')
                ->screenshot('tel8-registar')
                ->press('@contact8-submit')
                ->pause(2000)
                ->assertPathIs('/contact/customers/list')
                ->assertSee('新しいお問い合わせを登録しました。')
                ->assertSee('サンプル市サンプルタウン1-1-1')
                ->assertSee('その他')
                ->assertSee('DuskTest株式会社')
                ->press('@contact-detail')
                ->pause(2000)
                ->screenshot('visit-detail')
                ->assertSee('090-1111-5555');
        });
    }
}
