<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class PastContactSearchTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testShowPastContactSearchPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/search')
                ->assertSee('顧客検索')
                ->type('name','テスト')
                ->type('tel','012093845424')
                ->type('pref','東京都')
                ->press('検索')
                ->assertSee('詳細');
        });
    }
}
