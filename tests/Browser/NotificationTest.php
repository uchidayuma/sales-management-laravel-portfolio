<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class NotificationTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    // TODO開発時にあるとうざいので後回し
    // public function testGet()
    // {
    //     $this->browse(function (Browser $browser) {
    //         $browser->loginAs(User::find(2))
    //                 ->visit('/rankings')
    //                 ->screenshot('notificate')
    //                 ->pause('10000')
    //                 ->screenshot('notificateh')
    //                 ->assertVisible('.js-info-circle-notification')
    //                 // 2を確認
    //                 ->click('@notification-btn')
    //                 ->pause('5000')
    //                 // 消えたことを確認
    //                 ->assertMissing('.js-info-circle-notification');
    //     });
    // }

    public function testIndex()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                    ->visit('/notifications')
                    ->screenshot('notificate-index')
                    ->assertSee('アポイント未取得')
                    ->click('@detail2')
                    ->assertPathIs('/contact/2');
        });
    }
}
