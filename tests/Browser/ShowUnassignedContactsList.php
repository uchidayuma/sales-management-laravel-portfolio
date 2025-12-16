<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ShowUnassignedContactsList extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @test
     * 
     * @return void
     */
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->type('email', 'user1@example.com')
                    ->type('password', 'kaerusan')
                    ->click('#submit')
                    ->assertSee('お問い合わせ管理')
                    ->click('#contact')
                    ->visit('contact/unassigned/list')
                    ->assertSee('お問い合わせ一覧')
                    ->assertSee('FCを選択')
                    ->click('#btn btn-outline-info')
                    ->assertPathIs('assign/{id}');
        });
    }
}
