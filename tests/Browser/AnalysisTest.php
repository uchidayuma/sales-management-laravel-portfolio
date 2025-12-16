<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class AnalysisTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testContacts()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->visit('/analysis')
                ->assertSee('Unauthorized');
            $browser->logout();
            $browser->loginAs(User::find(1))
                ->visit('/analysis')
                ->assertPresent('.jsgrid-table')
                ->click('#year')
                ->click('@filter-contact')
                ->assertPresent('.jsgrid-table')
                ->click('@type2')
                ->click('@filter-contact')
                ->assertPresent('.jsgrid-table')
                ->click('#yearmonth')
                ->click('@filter-contact')
                ->assertPresent('.jsgrid-table')
                ->click('@type3')
                ->click('@filter-contact')
                ->assertPresent('.jsgrid-table')
                ->click('#year')
                ->click('@filter-contact')
                ->assertPresent('.jsgrid-table');
        });
    }
}
