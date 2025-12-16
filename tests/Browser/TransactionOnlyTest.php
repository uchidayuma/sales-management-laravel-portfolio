<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TransactionOnlyTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testTransctionNullCreate()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->maximize()
                ->visit('/transactions/create/order')
                ->pause(1000)
                ->radio('t[address_type]', '1')
                ->select('@turf-select', 1)
                ->type('@turf-number1', 1)
                ->pause(1000)
                ->select('@cut-turf-select', 1)
                ->pause(1000)
                ->type('@turf-number1', 1)
                ->pause(1000)
                ->type('vertical', 10)
                ->pause(1000)
                ->type('horizontal', 1)
                ->pause(1000)
                // 切り売り人工芝に紐づくカットメニュー
                ->select('.js-cut-select', 51)
                ->pause(1000)
                ->screenshot('null-transaction-9')
                ->type('.js-cut-length', 5)
                ->pause(1000)
                ->select('@sub-select', 12)
                ->type('@sub-number', 5)
                ->click('#js-sales-plus')
                ->pause(1000)
                ->screenshot('null-transaction-2')
                ->click('@sales-select')
                ->mouseover('@product31')
                ->click('@product31')
                ->pause(1000)
                ->screenshot('null-transaction-2')
                ->type('@sales-number', 5)
                ->screenshot('transaction')
                ->click('#js-etc-plus')
                ->pause(1000)
                ->type('@other-name', 'テスト手動入力')
                ->type('@other-price', 3333)
                ->type('@other-unit', '本')
                ->type('@other-count', 3)
                ->click('#js-calc')
                ->screenshot('transaction-before-post-null')
                ->pause(2000)
                // ->click('@delivery')
                // ->pause(2000)
                // ->click('.flatpickr-next-month')
                // ->click('.flatpickr-day')
                ->type('@delivery', '2028-01-01')
                ->click('@post')
                ->pause(15000)
                ->screenshot('transaction-after-post-null')
                ->click('@commit')
                ->pause(7000)
                ->assertSee('発注が確定されました。');
        });
    }

    public function testDispatch()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->maximize()
                ->visit('/transactions/dispatch/pending')
                ->click('#input-23')
                ->pause(1000)
                ->click('@input-cost')
                ->pause(1000)
                ->type('@create-cost', '1111')
                ->select('@create-shipping-company', 2)
                ->type('@create-shipping-date', '2022-10-10')
                ->pause(1000)
                ->type('@create-number', '1111111,111111,1111')
                ->type('@create-message', 'かえるのーうーたーがーきーこーえーてーくるーよー')
                ->click('@create-cost-submit')
                ->assertSee('FCに部材発注連絡を行いました');
        });
    }

    public function testShippingUpdate()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->maximize()
                ->visit('/transactions/7')
                ->click('@shipping-edit')
                ->pause(1000)
                ->select('@edit-shipping-company', 4)
                ->click('@edit-cost-submit')
                ->assertSee('発送情報を更新')
                ->visit('/transactions/7')
                ->click('@shipping-info')
                ->pause(1000)
                ->assertSee('日本郵政');
        });
    }

    // FC発送済み確認テスト
    public function testTransportShow()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->maximize()
                ->visit('/transactions/dispatched')
                ->click('@modal-show-40')
                ->pause(2000)
                ->assertInputValue('#shipping-number-0', '123456789')
                ->assertInputValue('#shipping-number-1', '987654321')
                ->assertSee('発送');
        });
    }

    public function testTransctionFloatCreate()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->maximize()
                ->visit('/transactions/create/order')
                ->pause(1000)
                ->select('@turf-select', 1)
                ->pause(1000)
                ->radio('t[address_type]', '2')
                ->pause(1000)
                ->type('t[consignee]', '少数テスト')
                ->type('t[tel]', '0120-000-000')
                ->pause(1000)
                ->type('@turf-number1', 1)
                ->pause(1000)
                ->select('@cut-turf-select', 1)
                ->pause(1000)
                ->type('@turf-number1', 1)
                ->pause(1000)
                ->type('vertical', 5.5)
                ->pause(1000)
                ->type('horizontal', 0.8)
                ->pause(1000)
                // 切り売り人工芝に紐づくカットメニュー
                ->select('.js-cut-select', 51)
                ->pause(1000)
                ->type('.js-cut-length', 5)
                ->pause(1000)
                // ここまでOK
                ->select('@sub-select', 12)
                ->type('@sub-number', 5)
                // ->click('@delivery')
                // ->pause(2000)
                // ->click('.flatpickr-next-month')
                // ->click('.flatpickr-day')
                ->type('@delivery', '2028-01-01')
                ->click('.header')
                ->click('#js-calc')
                ->pause(2000)
                ->click('@post')
                ->pause(1000)
                ->assertSee('0.8')
                ->assertSee('5.5')
                ->click('@commit')
                ->pause(7000)
                ->assertSee('発注が確定されました。');
        });
    }

    public function testFullPrepaid()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->maximize()
                ->visit('/transactions/35')
                ->assertSee('送料のみを入力')
                ->click('@input-shipping-cost-modal-show')
                ->pause(2000)
                ->type('@shipping-cost', 1000)
                ->click('@shipping-cost-submit')
                ->visit('/transactions')
                ->assertSeeLink('35')
                ->clickLink('35')
                ->click('@input-cost')
                ->pause(4000)
                ->select('@create-shipping-company', 2)
                ->type('@create-shipping-date', '2020-10-10')
                ->type('@create-number', '1111111,111111,1111')
                ->click('@create-cost-submit')
                ->assertSee('FCに部材発注連絡を行いました');
        });
    }

    // 行の計算結果が少数にならないことをテスト（小数点以下が出てしまう不具合用テスト）
    public function testAfewCalc()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->maximize()
                ->visit('/transactions/create/order')
                ->click('#js-cut-sub-plus')
                ->with('.js-cut-sub-row', function ($tr) {
                    $tr->select('.js-cut-sub-select', 11)
                        ->type('.js-product-count', 19.6)
                        ->assertValue('.js-price', "14112");
                });
        });
    }

    // 後で紐付けテスト
    public function testTransctionAfterContact()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->maximize()
                ->visit('/transactions/create/order')
                ->assertSelectHasOption('@contact-select', 141)
                ->select('@contact-select', 141)
                ->radio('t[address_type]', '1')
                ->select('@turf-select', 1)
                ->type('@turf-number1', 1)
                ->pause(1000)
                ->select('@cut-turf-select', 1)
                ->pause(1000)
                ->type('@turf-number1', 1)
                ->pause(1000)
                ->type('vertical', 10)
                ->type('horizontal', 1)
                ->pause(1000)
                // 切り売り人工芝に紐づくカットメニュー
                ->select('.js-cut-select', 51)
                ->pause(1000)
                ->type('.js-cut-length', 5)
                ->pause(1000)
                ->select('@sub-select', 12)
                ->type('@sub-number', 5)
                ->type('@delivery', '2028-01-01')
                ->click('.header')
                ->click('#js-calc')
                ->click('@post')
                ->assertSee('案件No.141')
                ->click('@commit')
                ->assertPathIs('/');
        });
    }
}
