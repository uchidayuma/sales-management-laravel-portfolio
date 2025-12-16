<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class DashboardTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testAdminDashboard()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
            ->maximize()
            ->visit('/')
            ->pause(5000)
            ->assertSee('合計')
            ->assertSee('昨日')
            ->assertSee('サンプル芝30mm');
        });
    }

    public function testFcDashboard()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
            ->maximize()
            ->visit('/')
            ->pause(5000)
            ->assertSee('本部 → 図面見積もり依頼リスト')
            ->assertSee('本部 → 訪問見積もりリスト')
            ->assertSee('今月売上件数')
            ->assertSee('お知らせ未確認一覧');
        });
    }

    public function testSpFcDashboard()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
            ->resize(375, 812)
            ->visit('/')
            // ->assertDontSee('本部 → 図面見積もり依頼リスト')
            // ->assertDontSee('本部 → 訪問見積もりリスト')
            // ->assertDontSee('今月売上件数')
            // ->assertDontSee('お知らせ未確認一覧')
            ->click('#nav-open')
            ->pause(500)
            ->assertDontSee('お問い合わせ管理');
        });
    }
    // 100m2以上案件のリストアップテスト
    public function testAdminDashboard100()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
            ->maximize()
            ->visit('/')
            ->click('#pills-large-tab')
            ->pause(2000)
            ->assertSee('FCかえる')
            ->assertSee('150㎡')
            ->assertSee('150');
        });
    }
}
