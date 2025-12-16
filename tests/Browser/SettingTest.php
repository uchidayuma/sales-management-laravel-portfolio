<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Laravel\Dusk\Chrome;
use App\Models\User;

class SettingTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testFormAdd()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/settings/csv_export_options')
                ->type('@form-input', 'テストフォーム名')
                ->click('@form-submit')
                ->pause(1000)
                ->assertDialogOpened('フォーム名を追加しました。')
                ->acceptDialog()
                ->pause(2000)
                ->assertSee('テストフォーム名');
        });
    }

    public function testFormdelete()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/settings/csv_export_options')
                ->click('@form-delete')
                ->pause(1000)
                ->assertDialogOpened('フォーム名を削除しました。')
                ->acceptDialog()
                ->pause(2000)
                ->assertDontSee('テストフォーム名');
        });
    }
}
