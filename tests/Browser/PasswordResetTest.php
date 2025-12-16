<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\PasswordReset;

class PasswordResetTest extends DuskTestCase
{
    //use DatabaseMigrations;

    /**
     * A Dusk test example.
     */
    public function testExample()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'user1@example.com')
                ->type('password', 'kaerusan')
                ->click('#submit')
                ->assertSee('サンプルFC');
        });
        /*
                $user = factory(User::class)->create([

                    'email'=> 'test@example.com',
                    'name' => 'サンプルFCテスト',
                    'company_name' => 'サンプルFCテスト株式会社',
                    'company_ruby' => 'さんぷるえふしーてすとかぶしきがいしゃ',
                    'zipcode' => '1234567',
                    'pref' => 'サンプル県',
                    'city' => 'サンプル市',
                    'street' => 'サンプル通り123',
                    'latitude' => '35.658581',
                    'longitude' => '139.745438',
                    'tel' => '09048484848',
                    'staff' => 'サンプル花子',
                    'staff_ruby' => 'さんぷるはなこ',
                    'rank_id' => '1',
                    'admin' => '2',
                    'status' => '0',
                    'token' => 'OvWwDpSmVmbnz0WZCqhBBv4pNauIQvwDwdatmEKdlJ3RBQuwkcuu2JzpKFJt',
                    'email_verified_at' => 'NULL',
                ]);

                $saveToken = New PasswordReset;
                $saveToken->email = $user->email;
                $saveToken->token = $user->token;
                $saveToken->save();

                $token = PasswordReset::where('email, $user->email)->first();
                $this->assertTrue(!empty($token));

                $this->browse(function (Browser $browser) {
                    $browser->visit('/fc/password/reset/{token}')
                            ->assertSee('パスワードを設定')
                            ->type('email', 'test@example.com')
                            ->type('password', 'password')
                            ->type('password_confirm', 'password')
                            ->click('#submit')
                            ->visit('/login')
                            ->type('email', 'test@example.com')
                            ->type('password', 'password')
                            ->click('#submit')
                            ->assertSee('サンプルFC');
                });
        */
    }
}
