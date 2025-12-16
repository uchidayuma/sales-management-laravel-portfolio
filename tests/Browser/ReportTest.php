<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class ReportTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testAllReport()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                    ->visit('/report/list')
                    ->assertSeeLink('120')
                    ->assertSeeLink('129');
        });
    }

    public function testShowFcReport()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                    ->visit('/report/list')
                    ->assertSeeLink('120')
                    ->assertDontSeeLink('129');
        });
    }

    public function testOrder()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                    ->visit('/report/pending')
                    ->click('@create-55')
                    ->click('@finish-date')
                    ->pause(500)
                    ->click('.today')
                    ->attach('c[after_image1]', public_path('images/logo.jpg'))
                    ->click('@finish-submit')
                    ->screenshot('report-click-today3')
                    ->assertSee('施工後報告が完了しました');
        });
    }
    public function testSpCreateReport()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
            ->resize(375, 812)
            ->visit('report/pending')
            ->assertDontSee('依頼種別')
            ->screenshot('report-sp-list');
        });
    }
}
