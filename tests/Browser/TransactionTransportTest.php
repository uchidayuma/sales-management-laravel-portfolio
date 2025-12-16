<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Transaction;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TransactionTransportTest extends DuskTestCase
{
    // 分納テスト
    public function testSecondInput()
    {
        $this->browse(function (Browser $browser) {
            $transaction = new Transaction;
            $arrival_date = $transaction->getDeliveryDate(7);
            $previous_date = $transaction->getDeliveryDate(7)->subDay();
            $next_date = $transaction->getDeliveryDate(7)->addDay();
            $next_date5 = $transaction->getDeliveryDate(7)->addDay(5);
            $next_date6 = $transaction->getDeliveryDate(7)->addDay(6);
            $next_date7 = $transaction->getDeliveryDate(7)->addDay(7);
            $browser->loginAs(User::find(2))
                ->visit('/transactions/create/order/136')
                ->pause(1000)
                ->click('.js-remove-row')
                ->pause(1500)
                ->click('.js-remove-row')
                ->pause(1500)
                ->radio('t[address_type]', '1')
                ->click('#js-turf-plus')
                ->select('@turf-select', 1)
                // ここで2つめのカレンダーが登場
                ->type('@turf-number4', 7)
                // ここで3つめのカレンダーが登場
                ->type('@turf-number4', 12)
                // 40mmなら条件変わる
                ->select('@turf-select', 67)
                // ここで2つめのカレンダーが登場
                ->type('@turf-number4', 5)
                // ここで3つめのカレンダーが登場
                ->type('@turf-number4', 8)
                ->select('@sub-select', 9)
                ->type('@sub-number', 1)
                ->pause(500)
                ->type('@delivery', $arrival_date->format('Y-m-d'))
                ->screenshot('TEST-TRANSPORT')
                ->pause(1500)
                ->click('.header')
                ->screenshot('TEST-TRANSPORT2')
                ->pause(1500)
                ->type('@delivery2', $next_date->format('Y-m-d'))
                ->click('.header')
                ->pause(1500)
                ->type('@delivery', $next_date5->format('Y-m-d'))
                ->click('.header')
                // 第2納品希望日入力してから、第1納品希望日変える → 第2納品希望日よりも後なら第2納品希望日のinputがリセットされている
                ->assertInputValue('@delivery2', '')
                ->type('@delivery2', $next_date6->format('Y-m-d'))
                ->click('.header')
                ->type('@delivery3', $next_date7->format('Y-m-d'))
                ->click('.header')
                ->click('#js-calc')
                ->screenshot('transportTestAll')
                ->click('@post')
                ->assertSee('納品希望日')
                ->assertSee('第2納品希望日')
                ->assertSee('第3納品希望日');
        });
    }
    // 分納発注書の修正テスト
    /*
    public function testSecondEdit()
    {
        $this->browse(function (Browser $browser) {
            $transaction = Transaction::where('status', 1)->latest();
            $browser->loginAs(User::find(1))
                ->visit("/transactions/edit/$transaction->id")
                // 2つ目と3つ目のカレンダーがアクティブなことを確認
                ->click('@delivery2')
                ->pause(500)
                ->assertVisible('flatpickr-calendar')
                ->click('@delivery3')
                ->pause(500)
                ->assertVisible('flatpickr-calendar');
        });
    }
    */

    // 分納判定でも工場引取なら分納にしないテスト
    public function testFactoryReceive()
    {
        $transaction = new Transaction;
        $arrival_date = $transaction->getDeliveryDate(5);
        $previous_date = $transaction->getDeliveryDate(5)->subDay();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->visit('/transactions/create/order/136')
                ->pause(1000)
                ->click('.js-remove-row')
                ->pause(1500)
                ->click('.js-remove-row')
                ->pause(1500)
                ->click('.js-remove-row')
                ->pause(1500)
                ->click('#js-turf-plus')
                ->radio('t[address_type]', '1')
                ->select('@turf-select', 1)
                ->type('@turf-number4', 12)
                ->pause(4000)
                ->assertSee('納品希望日2')
                ->assertSee('納品希望日3')
                ->radio('t[address_type]', '3')
                ->assertDontSee('納品希望日2')
                ->assertDontSee('納品希望日3');
        });
    }

    // 本部アカウントで分納の発送連絡
    public function testAdminDispatch()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/transactions/41')
                ->click('@input-cost2')
                ->pause(2000)
                ->assertSee('第2納品希望日')
                ->select('@create-shipping-company', 2)
                ->type('@create-shipping-date', '2025-10-02')
                ->type('@create-number', '2222222222,2020202020')
                ->click('@create-cost-submit')
                ->assertSee('FCに部材発注連絡を行いました')
                ->visit('/transactions/41')
                ->click('@input-cost3')
                ->pause(4000)
                ->assertSee('第3納品希望日')
                ->select('@create-shipping-company', 3)
                ->type('@create-shipping-date', '2025-10-03')
                ->type('@create-number', '33333333333,3030303030')
                ->click('@create-cost-submit')
                ->pause(2000)
                // ->assertSee('FCに部材発注連絡を行いました')
                ->visit('/transactions/41')
                ->assertSee('発送2の送料と追跡番号を修正')
                ->assertSee('発送3の送料と追跡番号を修正');
        });
    }

    // FCアカウントで分納発送情報を見られる
    public function textFcViewTrakkingInfo()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->visit('/transactions/dispatched')
                ->click('@modal-show-41')
                ->pause(1000)
                ->assertSee('1個目の発送情報')
                ->assertSee('2個目の発送情報')
                ->assertSee('3個目の発送情報');
        });
    }

    // 本部案件の発送連絡
    public function testCustomerDispatch()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/transactions/dispatch/pending')
                ->click('@admin-dispatch18')
                ->pause(3000)
                ->type('@shipping-date', '2022-10-10')
                ->type('@number', '1111111,111111,1111')
                ->type('@create-message', 'かえるのーうーたーがーきーこーえーてーくるーよー')
                ->click('@admin-submit')
                ->acceptDialog()
                ->assertSee('顧客に商品発送連絡を行いました');
        });
    }

    // 追加発送連絡
    public function testAddDelivery()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/transactions/44')
                ->click('@add-input')
                ->acceptDialog()
                ->pause(500)
                ->assertSee('発送1の送料と追跡番号を入力')
                ->assertSee('発送2の送料と追跡番号を入力')
                ->click('@input-cost2')
                ->pause(2000)
                // ->assertSee('第2納品希望日')
                ->select('@create-shipping-company', 2)
                ->type('@create-shipping-date', '2025-10-02')
                ->type('@create-number', '2222222222,2020202020')
                ->type('@create-message', '2つ目のテスト発送')
                ->click('@create-cost-submit')
                ->assertSee('FCに部材発注連絡を行いました')
                ->visit('/transactions/44')
                ->assertSee('発送2の送料と追跡番号を修正');
                // ->acceptDialog()
                // ->assertSee('顧客に商品発送連絡を行いました');
        });
    }
}
