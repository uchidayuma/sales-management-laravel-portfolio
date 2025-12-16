<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ContactUnassignedListTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                //FC未振り分け一覧(アドミンのみ)
                ->visit('contact/unassigned/list')
                ->assertSee('FC未振り分け一覧')
                ->click('@contact-detail')
                ->pause('3000')
                ->assertSee('案件詳細')
                ->back()
                ->assertPathIs('/contact/unassigned/list')
                ->assertSee('編集')
                //FCを選択
                ->click('@assign9')
                ->pause(8000)
                ->assertVisible('#map_wrapper_div')
                ->back();
        });
    }

    // 図面見積もりでFCの見積もり作成に来るように
    public function testDrawAssign()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/contact/assign/132')
                ->click('@fc2')
                ->click('#submit-btn')
                ->acceptDialog()
                ->assertSee('FCに見積もりを依頼しました');
            $browser->loginAs(User::find(2))
                ->visit('/contact/assigned/list')
                ->assertSee('FC見積もり依頼テスト（図面見積もりテスト')
                ->assertSeeLink('132');
        });
    }

    // 訪問見積もりでFCの見積もり作成に来るように
    public function testVisitAssign()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/contact/assign/133')
                ->click('@fc2')
                ->click('#submit-btn')
                ->acceptDialog()
                ->assertSee('FCに見積もりを依頼しました');
            $browser->loginAs(User::find(2))
                ->visit('/contact/quotations/needs')
                ->assertSee('FC見積もり依頼テスト（訪問見積もりテスト')
                ->assertSeeLink('133');
        });
    }
}
