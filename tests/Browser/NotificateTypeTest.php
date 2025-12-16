<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class NotificateTypeTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                    ->visit('/notifications/create')
                    ->assertSee('アラート設定')
                    ->select('@period1', 1)
                    ->select('@period2', 2)
                    ->select('@period3', 3)
                    ->click('@submit');
        });
    }
}
