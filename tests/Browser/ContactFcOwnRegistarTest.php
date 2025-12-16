<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ContactFcOwnRegistarTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    // 個人サンプル請求
    public function testCreatePersonalSample()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->visit('/contact/new')
                ->assertSee('自己獲得案件登録')
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
                ->assertDontSee('本部にのみ表示されます')
                ->press('@contact1-submit')
                ->pause(2000)
                ->screenshot('tel-registar')
                ->assertPathIs('/contact/customers/list');
        });
    }

    // 個人サンプル請求→図面への変更
    public function testCreatePersonalSampleToDraw()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout();
            $browser->loginAs(User::find(2))
                ->visit('/contact/edit/78')
                ->click('@from-sample')
                ->select('#contact_type', 2)
                ->waitForDialog(10)
                ->acceptDialog()
                ->pause(3000)
                ->assertSee('見積もり作成');
        });
    }

    // 個人図面見積もり
    public function testCreatePersonalDrow()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                //新規問い合わせ電話受付
                ->visit('/contact/new')
                ->assertSee('自己獲得案件登録')
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
                ->attach('c[document1]', public_path('images/logo.jpg'))
                ->assertDontSee('本部にのみ表示されます')
                ->press('@contact2-submit')
                ->pause(2000)
                ->assertPathIs('/contact/quotations/needs')
                ->screenshot('tel2-registar')
                ->assertSee('新しいお問い合わせを登録しました。');
        });
    }

    // 個人訪問見積もり
    public function testCreatePersonalVisit()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                //新規問い合わせ電話受付
                ->visit('/contact/new')
                ->assertSee('自己獲得案件登録')
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
                ->assertDontSee('本部にのみ表示されます')
                ->press('@contact3-submit')
                ->pause(2000)
                ->assertPathIs('/contact/assigned/list')
                ->assertSee('新しいお問い合わせを登録しました。')
                ->assertSee('サンプル県サンプル市サンプルタウン1-1-1')
                ->assertSee('サンプル裕馬')
                ->press('@contact-detail');
        });
    }

    // 個人その他
    public function testCreatePersonalEtc()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                //新規問い合わせ電話受付
                ->visit('/contact/new')
                ->assertSee('自己獲得案件登録')
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
                ->press('@contact4-submit')
                ->pause(2000)
                ->assertPathIs('/contact/customers/list')
                ->assertSee('新しいお問い合わせを登録しました。')
                ->screenshot('tel4-registar');
        });
    }

    // 法人サンプル請求
    public function testCreateCompanySample()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                //新規問い合わせ電話受付
                ->visit('/contact/new')
                ->maximize()
                ->assertSee('自己獲得案件登録')
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
                ->assertDontSee('本部にのみ表示されます')
                ->press('@contact5-submit')
                ->pause(2000)
                ->assertPathIs('/contact/customers/list');
        });
    }
    // 法人サンプル請求→訪問への変更
    public function testCreateCompanySampleToVisit()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout();
            $browser->loginAs(User::find(2))
                ->visit('/contact/customers/list')
                ->click('@contact-edit')
                ->click('@from-sample')
                ->select('#contact_type', 7)
                ->waitForDialog($seconds = null)
                ->acceptDialog()
                ->pause(2000)
                ->assertSee('FCから顧客へのアポイントを取得');
        });
    }


    // 法人図面見積もり
    public function testCreateCompanyDrow()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                //新規問い合わせ電話受付
                ->visit('/contact/new')
                ->assertSee('自己獲得案件登録')
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
                ->assertDontSee('本部にのみ表示されます')
                ->press('@contact6-submit')
                ->pause(2000)
                ->assertPathIs('/contact/quotations/needs')
                ->screenshot('tel6-registar') 
                ->assertSee('新しいお問い合わせを登録しました。');
        });
    }

    // 法人図面見積もり→訪問にしたときのステップ変化確認
    public function testCreateCompanyDrowToVisit()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->visit('/contact/customers/list')
                ->click('@contact-edit')
                ->click('@from-sample')
                ->select('#contact_type', 7)
                ->waitForDialog()
                ->acceptDialog()
                ->pause(3000)
                ->assertSee('FCから顧客へのアポイントを取得');
        });
    }

    // 法人訪問見積もり
    public function testCreateCompanyVisit()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                //新規問い合わせ電話受付
                ->visit('/contact/new')
                ->assertSee('自己獲得案件登録')
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
                ->assertDontSee('本部にのみ表示されます')
                ->press('@contact7-submit')
                ->pause(2000)
                ->assertPathIs('/contact/assigned/list')
                ->assertSee('新しいお問い合わせを登録しました。')
                ->assertSee('サンプル県サンプル市サンプルタウン1-1-1');
        });
    }

    // 法人その他
    public function testCreateCompanyEtc()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                //新規問い合わせ電話受付
                ->visit('/contact/new')
                ->assertSee('自己獲得案件登録')
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
                ->assertDontSee('本部にのみ表示されます')
                ->press('@contact8-submit')
                ->pause(2000)
                ->assertPathIs('/contact/customers/list')
                ->assertSee('新しいお問い合わせを登録しました。')
                ->assertSee('DuskTest株式会社')
                ->screenshot('tel8-registar');
        });
    }

    // 個人サンプル請求　サンプル送付を本部に依頼
    public function testPersonalSampleAdmin()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->visit('/contact/new')
                ->assertSee('自己獲得案件登録')
                ->check('@type1')
                ->type('@p1-surname', 'サンプル依頼')->type('@p1-name', 'テスト')
                ->screenshot('personal-sample-leave-to-admin')
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
                ->screenshot('personal-sample-leave-to-admin2')
                ->assertDontSee('本部にのみ表示されます')
                ->check('#sampleAdmin1')
                ->press('@contact1-submit')
                ->pause(2000)
                ->assertPathIs('/contact/customers/list');
            
            $browser->loginAs(User::find(1))
                ->visit('/contact/sample/list')
                ->screenshot('personal-sample-leave-to-admin3')
                ->assertSee('サンプル依頼テスト');
        });
    }

    // 個人サンプル請求本部一任
    public function testCreatePersonalSampleAdmin()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout();
            $browser->loginAs(User::find(2))
                ->visit('/contact/new')
                ->assertSee('自己獲得案件登録')
                ->check('@type1')
                ->type('@p1-surname', '個人サンプル請求')->type('@p1-name', '本部一任')
                ->type('@p1-surname_ruby', 'こじんさんぷるせいきゅう')->type('@p1-name_ruby', 'ほんぶいちにん')
                ->type('@p1-zipcode', 1000003)->type('@p1-pref', 'サンプル県')->type('@p1-city', 'サンプル市')->type('@p1-street', 'サンプルタウン1-1-1')
                ->type('@p1-tel', '09022224444')
                ->type('@p1-tel2', '08011112222')
                ->type('@p1-fax', '08633428454')
                ->type('@p1-email', 'test@example.com')
                ->select('@p1-age', 1980)
                ->check('#p1-use1')->check('#p1-use3')->type('@p1-use_application-etc', 'その他花壇の整備')
                ->check('#p1-where1')->check('#p1-where2')->type('@p1-where_find-etc', 'その他インターネット')
                ->check('#p1-sns1')->check('#p1-sns2')->check('#p1-sns3')->type('@p1-sns-etc', 'その他SNS')
                ->assertDontSee('本部にのみ表示されます')
                ->check('#leaveToAdmin1')
                ->press('@contact1-submit')
                ->pause(2000)
                ->assertPathIs('/contact/customers/list')
                ->screenshot('leave-to-admin1');

            $browser->logout();
            $browser->loginAs(User::find(1))
                ->visit('/contact/customers/list')
                ->pause(2000)
                ->assertSee('個人サンプル請求本部一任');
        });
    }

    // 個人図面見積もり本部一任
    public function testCreatePersonalDrowAdmin()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout();
            $browser->loginAs(User::find(2))
                ->visit('/contact/new')
                ->pause(1000)
                ->assertSee('自己獲得案件登録')
                ->check('@type2')
                ->screenshot('leave-to-admin2')
                ->pause(1000)
                ->radio('c[free_sample]', '請求済み')
                ->pause(1000)
                ->type('@p2-surname', '個人図面見積もり')->type('@p2-name', '本部一任')
                ->type('@p2-surname_ruby', 'こじんずめんみつもり')->type('@p2-name_ruby', 'ほんぶいちにん')
                ->type('@p2-zipcode', 1000003)->type('@p2-pref', 'サンプル県')->type('@p2-city', 'サンプル市')->type('@p2-street', 'サンプルタウン1-1-1')
                ->type('@p2-tel', '09022224444')
                ->type('@p2-tel2', '08011112222')
                ->select('@p2-age', 1960)
                ->check('#p2-ground1')->check('#p2-ground3')->check('#p2-ground2')->type('c[ground_condition][etc]', 'その他グランドコンティション')
                ->type('@p2-vertical_size', 30)->type('@p2-horizontal_size', 20)
                ->check('#p2-product1')->check('#p2-product3')->check('#p2-product4')
                ->check('#p2-use1')->check('#p2-use3')->check('#p2-use4')->type('@p2-use_application-etc', 'その他花壇の整備')
                ->type('@p2-comment', 'dusk test ご要望備考欄')
                ->assertDontSee('本部にのみ表示されます')
                ->check('#leaveToAdmin2')
                ->press('@contact2-submit')
                ->pause(2000)
                ->assertPathIs('/contact/quotations/needs')
                ->assertSee('新しいお問い合わせを登録しました。');
            
            $browser->logout();
            $browser->loginAs(User::find(1))
                ->visit('/contact/customers/list')
                ->pause(2000)
                ->assertSee('個人図面見積もり本部一任');
        });
    }

    // 個人訪問見積もり本部一任
    public function testCreatePersonalVisitAdmin()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout();
            $browser->loginAs(User::find(2))
                ->visit('/contact/new')
                ->assertSee('自己獲得案件登録')
                ->check('@type3')
                ->pause(3000)
                ->radio('#p3-freesample3', '不要')
                ->pause(1000)
                ->type('@p3-surname', '個人訪問見積もり')->type('@p3-name', '本部一任')
                ->type('@p3-surname_ruby', 'こじんほうもんみつもり')->type('@p3-name_ruby', 'ほんぶいちにん')
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
                ->assertDontSee('本部にのみ表示されます')
                ->check('#leaveToAdmin3')
                ->press('@contact3-submit')
                ->pause(2000)
                ->assertPathIs('/contact/assigned/list')
                ->assertSee('新しいお問い合わせを登録しました。');

            $browser->loginAs(User::find(1))
                ->visit('/contact/customers/list')
                ->pause(2000)
                ->screenshot('leave-to-admin3')
                ->assertSee('個人訪問見積もり本部一任');
        });
    }

    // 個人その他本部一任
    public function testCreatePersonalEtcAdmin()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout();
            $browser->loginAs(User::find(2))
                ->visit('/contact/new')
                ->assertSee('自己獲得案件登録')
                ->check('@type4')
                ->pause(3000)
                ->radio('#p4-freesample3', '不要')
                ->pause(1000)
                ->type('@p4-surname', '個人その他')->type('@p4-name', '本部一任')
                ->type('@p4-surname_ruby', '個人その他')->type('@p4-name_ruby', 'ほんいちにん')
                ->type('@p4-zipcode', 1000003)->type('@p4-pref', 'サンプル県')->type('@p4-city', 'サンプル市')->type('@p4-street', 'サンプルタウン1-1-1')
                ->type('@p4-tel', '09022224444')
                ->type('@p4-tel2', '09011115555')
                ->check('#p4-quote1')->check('#p4-quote3')->check('#p4-quote2')
                ->type('@p4-comment', 'dusk test 個人その他ご要望備考欄')
                ->check('#leaveToAdmin4')
                ->press('@contact4-submit')
                ->pause(2000)
                ->assertPathIs('/contact/customers/list')
                ->assertSee('新しいお問い合わせを登録しました。');

            $browser->logout();
            $browser->loginAs(User::find(1))
                ->visit('/contact/customers/list')
                ->pause(2000)
                ->screenshot('leave-to-admin4')
                ->assertSee('個人その他本部一任');
        });
    }
    
    // 法人サンプル請求本部一任
    public function testCreateCompanySampleAdmin()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout();
            $browser->loginAs(User::find(2))
                //新規問い合わせ電話受付
                ->visit('/contact/new')
                ->assertSee('自己獲得案件登録')
                ->check('@type5')
                ->type('@h1-company_name', '法人サンプル請求本部一任')
                ->type('@h1-company_ruby', 'ほうじんサンプルせいきゅうほんぶいちにん')
                ->type('@h1-surname', 'DUSK担当者')
                ->type('@h1-surname-ruby', 'ダースクタントウシャ')
                ->type('@h1-industry', 'IT業界')
                ->type('@h1-zipcode', 1000001)->type('@h1-pref', 'サンプル都')->type('@h1-city', 'サンプル中央区')->type('@h1-street', 'サンプルタウン1-1-1 サンプルビル1F')
                ->type('@h1-tel', '050-3561-2247')
                ->type('@h1-tel2', '090-1111-5555')
                ->check('#h1-use1')->check('#h1-use3')->check('#h1-use4')->type('@h1-use_application-etc', 'その他花壇の整備')
                ->check('#h1-where1')->check('#h1-where2')->check('#h1-where3')->type('@h1-where_find-etc', 'その他インターネット')
                ->assertDontSee('本部にのみ表示されます')
                ->check('#leaveToAdmin5')
                ->press('@contact5-submit')
                ->pause(2000)
                ->assertPathIs('/contact/customers/list');

            $browser->loginAs(User::find(1))
                ->visit('/contact/customers/list')
                ->pause(2000)
                ->screenshot('leave-to-admin5')
                ->assertSee('法人サンプル請求本部一任');
        });
    }

    // 法人図面見積もり本部一任
    public function testCreateCompanyDrowAdmin()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout();
            $browser->loginAs(User::find(2))
                //新規問い合わせ電話受付
                ->visit('/contact/new')
                ->pause(1000)
                ->assertSee('自己獲得案件登録')
                ->check('@type6')
                ->pause(1000)
                ->radio('#h2-freesample1', '必要')
                ->pause(1000)
                ->type('@h2-company_name', '法人図面見積もり本部一任')
                ->type('@h2-company_ruby', 'ほうじんずめんみつもりほんぶいちにん')
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
                ->assertDontSee('本部にのみ表示されます')
                ->check('#leaveToAdmin6')
                ->press('@contact6-submit')
                ->pause(2000)
                ->assertPathIs('/contact/quotations/needs');

            $browser->logout();
            $browser->loginAs(User::find(1))
                ->visit('/contact/customers/list')
                ->pause(2000)
                ->screenshot('leave-to-admin6')
                ->assertSee('法人図面見積もり本部一任');
        });
    }

    // 法人訪問見積もり本部に一任
    public function testCreateCompanyVisitAdmin()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout();
            $browser->loginAs(User::find(2))
                //新規問い合わせ電話受付
                ->visit('/contact/new')
                ->assertSee('自己獲得案件登録')
                ->check('@type7')
                ->pause(3000)
                ->radio('#h3-freesample3', '不要')
                ->pause(1000)
                ->type('@h3-company_name', '法人訪問見積もり本部一任')
                ->type('@h3-company_ruby', 'ほうじんほうもんみつもりほんぶいちにん')
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
                ->assertDontSee('本部にのみ表示されます')
                ->check('#leaveToAdmin7')
                ->press('@contact7-submit')
                ->pause(3000)
                ->assertPathIs('/contact/assigned/list');
                
            $browser->logout();
            $browser->loginAs(User::find(1))
                ->visit('/contact/customers/list')
                ->pause(2000)
                ->screenshot('leave-to-admin7')
                ->assertSee('法人訪問見積もり本部一任');
        });
    }

    // 法人その他本部一任
    public function testCreateCompanyEtcAdmin()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout();
            $browser->loginAs(User::find(2))
                ->visit('/contact/new')
                ->pause(1000)
                ->assertSee('自己獲得案件登録')
                ->check('@type8')
                ->pause(3000)
                ->radio('#h4-freesample3', '不要')
                ->pause(1000)
                ->type('@h4-company_name', '法人その他本部一任')
                ->type('@h4-company_ruby', 'ほうじんそのたほんぶいちにん')
                ->type('@h4-surname', 'DUSK担当者')
                ->type('@h4-surname-ruby', 'ダースクタントウシャ')
                ->type('@h4-industry', 'IT業界')
                ->type('@h4-zipcode', 1000003)->type('@h4-pref', 'サンプル県')->type('@h4-city', 'サンプル市')->type('@h4-street', 'サンプルタウン1-1-1')
                ->type('@h4-tel', '090-2222-4444')
                ->type('@h4-tel2', '090-1111-5555')
                ->check('#h4-quote1')->check('#h4-quote3')->check('#h4-quote2')
                ->type('@h4-comment', 'dusk test 法人その他ご要望備考欄')
                ->assertDontSee('本部にのみ表示されます')
                ->check('#leaveToAdmin8')
                ->press('@contact8-submit')
                ->pause(2000)
                ->assertPathIs('/contact/customers/list')
                ->assertSee('新しいお問い合わせを登録しました。');
            
            $browser->logout();
            $browser->loginAs(User::find(1))
                ->visit('/contact/customers/list')
                ->pause(2000)
                ->screenshot('leave-to-admin8')
                ->assertSee('法人その他本部一任')
                ->click('@contact-detail')
                ->assertSee('FCかえる');
        });
    }

    //お問い合わせ種別未選択テスト
    public function testContactTypeError()
    {   
        $this->browse(function (Browser $browser) {
            $browser->logout();
            $browser->loginAs(User::find(2))
                ->visit('/contact/new')
                ->pause(1000)
                ->assertSee('自己獲得案件登録')
                ->pause(1000)
                ->type('c[surname]', '問合せ種別')->type('c[name]', '未選択テスト')
                ->type('c[surname_ruby]', 'といあわせ')->type('c[name_ruby]', 'みせんたく')
                ->type('c[zipcode]', 1000003)->type('c[pref]', 'サンプル県')->type('c[city]', 'サンプル市')->type('c[street]', 'サンプルタウン1-1-1')
                ->type('c[tel]', '09022224444')
                ->type('c[tel2]', '08011112222')
                ->type('c[fax]', '08633428454')
                ->type('c[email]', 'test@example.com')
                ->select('c[age]', 1980)
                ->check('#use1')->check('#use3')->check('#use4')->type('c[use_application][etc]', 'その他花壇の整備')
                ->check('#where1')->check('#where2')->check('#where3')->type('c[where_find][etc]', 'その他インターネット')
                ->check('#sns1')->check('#sns2')->check('#sns3')->type('c[sns][etc]', 'その他SNS')
                ->assertDontSee('本部にのみ表示されます')
                ->press('@contact-submit')
                ->pause(2000)
                ->screenshot('dont-type-contact')
                ->assertSee('「お問い合わせ種別」は必須項目です');
        }); 
    }
}