<?php

namespace Tests\Browser;

use App\Models\User;
use Tests\DuskTestCase;

class ContactAssignedListTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testShowAssignedContacts()
    {
        $this->browse(function ($browser) {
            //アドミンお問い合わせアサイン済み一覧
            $browser->loginAs(User::find(1))
                ->visit('/contact/assigned/list')
                ->assertSee('FC依頼済み一覧');
        });
    }

    public function testShowAssignedContactsFC()
    {
        $this->browse(function ($browser) {
            //FCアサイン済みお問い合わせ一覧
            $browser->loginAs(User::find(2))
                ->visit('contact/assigned/list')
                ->assertSee('見積もり要請案件一覧（要アポイントメント）')
                ->clickLink('2')
                ->pause('3000')
                ->assertSee('案件詳細')
                ->back()
                ->assertPathIs('/contact/assigned/list')
                ->assertSee('編集')
                ->assertSee('アポ日時を入力')
                ->click('@modal-open-40')
                ->pause(4000)
                /*
                ->whenAvailable('#appointment-modal', function ($modal) {
                    $modal->assertSee('案件アポ日時入力')
                          ->assertSee('現場報告をスキップ')
                          ->click('@btn-appointment')
                          ->pause(4000);
                })
                */
                ->assertSee('案件アポ日時入力')
                ->assertSee('現場報告をスキップ')
                ->click('@btn-appointment')
                ->pause(10000)
                ->assertSee('アポイント日時を登録');
        });
    }

    public function testSkip()
    {
        $this->browse(function ($browser) {
            //FCアサイン済みお問い合わせ一覧
            $browser->loginAs(User::find(2))
                ->visit('contact/assigned/list')
                ->assertPathIs('/contact/assigned/list')
                ->click('@modal-open-69')
                /*
                ->whenAvailable('#appointment-modal', function ($modal) {
                    $modal->assertSee('案件アポ日時入力')
                          ->screenshot('apoint-submit2')
                          ->assertSee('現場報告をスキップ')
                          ->click('@btn-skipOnsiteConfirmation')
                          ->screenshot('apoint-submit3')
                          ->pause(4000);
                })
                */
                ->pause(4000)
                ->assertSee('現場報告をスキップ')
                ->click('@btn-skipOnsiteConfirmation')
                ->screenshot('apoint-submit3')
                ->pause(10000)
                ->assertSee('現場報告をスキップしました')
                ->assertPathIs('/quotations/new/69')
                ->assertInputValue('q[client_name]', 'テストアポスキップ 様');
        });
    }

    public function googleMapCheck()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(1))
                ->visit('contact/assign/9')
                ->assertPathIs('/contact/assign/9')
                ->screenshot('google-map-check');
             });
    }
}
