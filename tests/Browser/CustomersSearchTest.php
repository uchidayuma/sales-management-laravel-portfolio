<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class CustomersSearchTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testShowCustomersSearchPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/contact/customers/list')
                ->assertSee('案件一覧')
                ->type('number', 1)
                ->type('name', 'ルートプラス')
                ->type('address', 'test@gmail.com')
                ->type('tell', '090-1111-1111')
                ->press('検索')
                ->assertSee('1')
                ->assertSee('株式会社ルートプラス');
        });
    }
    
    public function testFilteringContact()
    {
        $this->browse(function (Browser $browser) {
            $daysago = date('Y-m-d', strtotime("-2 day"));
            $dayslater = date("Y-m-d", strtotime("2 day"));
            $browser->loginAs(User::find(1))
                ->visit('/contact/customers/list')
                ->select('@select-fc', 2)
                ->select('@select-type', 1)
                ->pause(1000)
                ->type('@created-at', $daysago." から ".$dayslater)
                ->pause(1000)
                ->click('.header')
                ->pause(1000)
                ->click('@filter-contact')
                ->pause(2000)
                ->assertSeeLink('99')
                ->screenshot("filter-contact");
        });
    }

    /* 退会済みユーザーの案件も出る */
    public function testFilteringWithdrawnUser()
    {
        $this->browse(function (Browser $browser) {
            $daysago = date('Y-m-d', strtotime("-2 day"));
            $dayslater = date("Y-m-d", strtotime("2 day"));
            $browser->loginAs(User::find(1))
                ->visit('/contact/customers/list')
                ->select('@select-fc', 3)
                ->select('@select-type', 1)
                ->pause(1000)
                ->type('@created-at', $daysago." から ".$dayslater)
                ->click('.header')
                ->pause(1000)
                ->click('@filter-contact')
                ->screenshot('filter2')
                ->assertSeeLink('75');
        }); 
    }

    /* サンプル送付日で絞れるか */
    public function testFilteringSampleSendAt(){
        $this->browse(function (Browser $browser) {
            $today = date('Y-m–d');
            $tomorrow = date("Y-m-d", strtotime("1 day"));
 
 
            $browser->loginAs(User::find(1))
                ->visit('/contact/customers/list')
                ->select('@select-fc', 2)
                ->select('@select-type', 1)
                ->pause(1000)
                ->type('@sent-at', $today." から ".$tomorrow)
                ->pause(1000)
                ->click('.header')
                ->click('@filter-contact')
                ->pause(2000)
                ->screenshot('filter3')
                ->assertSee('サンプル送付日絞り込みテスト');
        });
    }

    /* FCには検索ボックスがでてこない */
    public function testFcDontseeFilteringArea()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->visit('/contact/customers/list')
                ->assertDontSee('案件絞り込み条件');
        });
    }
    // 都道府県でフィルターテスト
    public function testPrefecturesFilter()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
            ->visit('/contact/customers/list')
            ->select('@select-prefectures', '熊本県')
            ->click('@filter-contact')
            ->pause(2000)
            ->screenshot('filter4')
            ->assertSee('122')
            ->assertDontSee('123');
        });
    }
}
