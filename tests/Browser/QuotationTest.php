<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Contact;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class QuotationTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testCreateQuotation()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
              ->visit('/contact/quotations/needs')
              ->click('@create-button')
              ->assertInputValue('.js-product-name', '下地作業費')
              ->type('q[name]', 'かえるの見積もり')
              ->pause(1000)
              ->select('product-select-1', 1)
              ->pause(1000)
              ->type('.dusk-count-1', 30)
              ->pause(1000)
              ->type('.dusk-unit-1', 'm2')
              ->pause(1000)
              ->type('.dusk-unit-price-1', 5000)
              ->pause(1000)
              ->select('product-select-2', 5)
              ->pause(1000)
              ->type('.dusk-count-2', 30)
              ->pause(1000)
              ->type('.dusk-unit-2', 'm2')
              ->pause(1000)
              ->type('.dusk-unit-price-2', 10000)
              ->pause(1000)
              ->select('.dusk-type-3', 1)
              ->pause(1000)
              ->click('@remove-4')
              ->click('@remove-5')
              ->click('@remove-6')
              ->type('.dusk-product-name-7', 'かえるの交通費')
              ->pause(1000)
              ->type('.dusk-count-3', 1)
              ->pause(1000)
              ->type('.dusk-unit-3', '往復')
              ->pause(1000)
              ->type('.dusk-unit-price-3', 10000)
              ->pause(1000)
              ->type('q[memo]', 'かえるの備考欄')
              ->pause(1000)
              ->click('.js-discount-plus')
              ->pause(2000)
              ->select('.js-account-infomation', 'カエルの口座番号2')
              ->assertInputValue('.js-payee', 'カエルの口座番号2')
              ->type('@discount', 1000)
              ->click('.js-plus')
              ->screenshot('quotation.create')
              ->pause(2000)
              ->select('@account_info_select', 1)
              ->pause(2000)
              ->click('#post-quotation')
              ->acceptDialog()
              ->acceptDialog()
              ->dismissDialog()
              ->assertSee('見積もり作成');
        });
    }

    public function testUpdateQuotation()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
              ->visit('/quotations/edit/8')
              ->click('.js-discount-plus')
              ->type('.js-discount', 5000)
              ->click('#post-quotation')
              ->assertSee('見積もりを修正しました')
              ->assertSee('180,000円');
        });
    }

    public function testAdminCreateQuotation()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
              ->visit('/quotations/new/2')
              ->pause(3000)
              ->assertSee('管理者は材料のみの問い合わせにしか見積もりができません！');
        });
    }

    // コピーして作成メソッド
    public function testCopyQuotation()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
              ->visit('/quotations')
              ->click('@copy1')
              ->pause(3000)
              ->type('q[name]', 'CI-TEST')
              ->type('q[client_name]', 'CI-NAME')
              ->click('.js-remove-row')
              ->pause(2000)
              ->click('.js-discount-plus')
              ->type('@discount', 3000)
              ->type('q[memo]', 'CI-TEST-MEMO')
              ->screenshot('remove')
              ->click('#post-quotation')
              ->pause(2000)
              ->waitForDialog($seconds = 5)
              ->acceptDialog()
              ->dismissDialog()
              ->assertSee('新しい見積もりを作成');
        });
    }

    //見積作成からメインFCを決める 
    public function testSetMainFc()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout();
            $browser->loginAs(User::find(2))
              ->visit('/quotations/new/78')
              ->pause(2000)
              ->select('product-select-1', 1)
              ->pause(2000)
              ->type('.dusk-count-1', 30)
              ->pause(2000)
              ->select('product-select-2', 5)
              ->pause(2000)
              ->type('.dusk-count-2', 30)
              ->pause(2000)
              ->select('product-select-3', 4)
              ->pause(2000)
              ->type('.dusk-count-3', 30)
              ->pause(2000)
              ->click('@remove-4')
              ->click('@remove-5')
              ->click('@remove-6')
              ->type('.dusk-count-7', 1)
              ->pause(2000)
              ->type('.dusk-unit-price-7', 1000)
              ->pause(2000)
              ->type('.dusk-count-8', 1)
              ->pause(2000)
              ->type('.dusk-unit-price-8', 1000)
              ->pause(2000)
              ->type('.dusk-unit-price-9', 1000)
              ->pause(2000)
              ->type('.dusk-unit-price-10', 1000)
              ->pause(2000)
              ->type('.dusk-unit-price-11', 1000)
              ->pause(1000)
              ->click('#post-quotation')
              ->pause(1000)
              ->acceptDialog()
              ->dismissDialog()
              ->pause(1000)
              ->screenshot('set-mainfc78')
              ->visit('contact/78')
              ->screenshot('set-mainfc77');

            $browser->logout();
            // $browser->loginAs(User::find(3))
            //   ->visit('/contact/77')
            //   ->pause(3000)
            //   ->screenshot('set-main2')
            //   ->assertSee('見積書の作成はできません');
        });
    }
    // 消費税計算のテスト
    public function testCalcTax()
    {
        $this->browse(function (Browser $browser) {
            $browser->logout();
            $browser->driver->manage()->deleteAllCookies();
            $browser->loginAs(User::find(2))
              // 四捨五入テスト
              ->visit('/quotations/new/131')
              ->type('q[name]', 'かえるの消費税計算見積もり')
              ->pause(1000)
              ->click('.js-remove-row')
              ->pause(3000)
              ->click('.js-remove-row')
              ->pause(3000)
              ->click('.js-remove-row')
              ->pause(3000)
              ->click('.js-remove-row')
              ->pause(3000)
              ->click('.js-remove-row')
              ->pause(3000)
              ->click('.js-remove-row')
              ->pause(3000)
              ->click('.js-remove-row')
              ->pause(1000)
              ->click('.js-remove-row')
              ->pause(1000)
              ->click('.js-remove-row')
              ->pause(1000)
              ->click('.js-remove-row')
              ->pause(1000)
              ->type('.dusk-unit-price-11', 5555)
              ->pause(1000)
              ->assertSee('￥556')
              ->click('#post-quotation')
              ->acceptDialog()
              ->dismissDialog()
              ->pause(1000)
              ->click('@quotation-show')
              ->assertSee('556円')
              // 切り上げテスト
              ->visit('/users/edit/2')
              ->select('@quotation-tax', '1')
              ->click('@submit')
              ->visit('/quotations/new/131')
              ->type('q[name]', 'かえるの消費税計算見積もり')
              ->pause(1000)
              ->click('.js-remove-row')
              ->pause(1000)
              ->click('.js-remove-row')
              ->pause(1000)
              ->click('.js-remove-row')
              ->pause(1000)
              ->click('.js-remove-row')
              ->pause(1000)
              ->click('.js-remove-row')
              ->pause(1000)
              ->click('.js-remove-row')
              ->pause(1000)
              ->click('.js-remove-row')
              ->pause(1000)
              ->click('.js-remove-row')
              ->pause(1000)
              ->click('.js-remove-row')
              ->pause(1000)
              ->click('.js-remove-row')
              ->pause(1000)
              ->type('.dusk-unit-price-11', 4444)
              ->pause(1000)
              ->assertSee('￥445')
              ->click('#post-quotation')
              ->acceptDialog()
              ->dismissDialog()
              ->pause(3000)
              ->click('@quotation-show')
              ->assertSee('445円')
              // 切り捨てテスト
              ->visit('/users/edit/2')
              ->select('@quotation-tax', '2')
              ->click('@submit')
              ->visit('/quotations/new/131')
              ->type('q[name]', 'かえるの消費税計算見積もり')
              ->pause(1000)
              ->click('.js-remove-row')
              ->pause(1000)
              ->click('.js-remove-row')
              ->pause(1000)
              ->click('.js-remove-row')
              ->pause(1000)
              ->click('.js-remove-row')
              ->pause(1000)
              ->click('.js-remove-row')
              ->pause(1000)
              ->click('.js-remove-row')
              ->pause(1000)
              ->click('.js-remove-row')
              ->pause(1000)
              ->click('.js-remove-row')
              ->pause(1000)
              ->click('.js-remove-row')
              ->pause(1000)
              ->click('.js-remove-row')
              ->pause(1000)
              ->type('.dusk-unit-price-11', 5555)
              ->pause(1000)
              ->assertSee('￥555')
              ->click('#post-quotation')
              ->acceptDialog()
              ->dismissDialog()
              ->pause(3000)
              ->click('@quotation-show')
              ->assertSee('555円')
              ->visit('/users/edit/2')
              ->select('@quotation-tax', '0')
              ->click('@submit');
        });
    }
}
