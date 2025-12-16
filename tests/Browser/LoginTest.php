<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class LoginTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testExampleAdmin()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                //Adminログインテスト
                ->visit('/')
                ->assertSee('サンプルFC')
                ->click('#icon_button')
                ->whenAvailable('.modal', function ($modal) {
                    $modal->pause(5000)->assertSee('ログアウト');
                })
                ->click('#logout')
                ->pause(5000)
                ->assertPathIs('/login')
                ->type('email', 'user1@example.com')
                ->type('password', 'kaerusan')
                ->click('#submit')
                ->pause(5000)
                ->screenshot('login-homeAdmin')
                ->assertSee('サンプルFC管理システム');
        });
    }

    public function testExampleFc()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
            //FCログインテスト
            ->visit('/')
            ->assertSee('サンプルFC')
            ->click('#icon_button')
            ->whenAvailable('.modal', function ($modal) {
                $modal->pause(5000)->assertSee('ログアウト');
            })
            ->click('#logout')
            ->pause(5000)
            ->assertPathIs('/login')
            ->type('email', 'user2@example.com')
            ->type('password', 'kaerusan')
            ->click('#submit')
            ->pause(5000)
            ->screenshot('login-homeFc');
        });
    }
}
