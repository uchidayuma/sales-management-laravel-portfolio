<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class AreaOpenEmailSendsTest extends DuskTestCase
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
                    ->visit('/users/contracts')
                    ->assertSee('株式会社アセンティア')->assertSee('有限会社秀水苑')
                    ->assertDontSee('株式会社大野');
        });
    }
}
