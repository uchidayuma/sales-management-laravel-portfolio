<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class FcApplyAreaTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testIndex()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                    ->visit('/settings/fc-apply-areas')
                    ->assertSee('生駒エリア')
                    ->assertSee('福島エリア')
                    ->assertSee('横須賀エリア')
                    ->assertSee('那覇エリア')
                    ->assertSee('豊田エリア');
        });
    }

    public function testStore()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                    ->visit('/settings/fc-apply-areas')
                    ->type('@input-name', 'テストエリア')
                    ->type('@input-content', 'テスト市、テスト町、テスト村')
                    ->click('@input-submit')
                    ->assertPathIs('/settings/fc-apply-areas')
                    ->assertSee('テストエリア');
        });
    }

    public function testUpdate()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                    ->visit('/settings/fc-apply-areas')
                    ->click('@modal-open-6')
                    ->waitFor('@area-name')
                    ->assertSee('テストエリア')
                    ->type('@area-name', 'UPDATEテストエリア')
                    ->type('@area-content', 'UPDATEテスト市、テスト町、テスト村')
                    ->click('@update-area')
                    ->assertPathIs('/settings/fc-apply-areas')
                    ->assertSee('UPDATEテストエリア')
                    ->assertSee('UPDATEテスト市、テスト町、テスト村');
        });
    }

    public function testDestroy()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                    ->visit('/settings/fc-apply-areas')
                    ->click('@delete-area6')
                    ->waitForDialog()
                    ->acceptDialog()
                    ->assertPathIs('/settings/fc-apply-areas')
                    ->assertDontSee('テストエリア');
        });
    }
}
