<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Laravel\Dusk\Chrome;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class Register extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @test
     * 
     * @return void
     */

    //テストクラスは、必ず「testClassName」のようにtestをつける
    public function testUserRegisteration()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'user1@example.com')
                ->type('password', 'kaerusan')
                ->click('#submit')
                ->visit('/users/create')
                ->assertSee('FC名')
                ->type('fc[name]', 'テストFC')
                ->type('fc[company_name]', 'テストFC株式会社')
                ->type('fc[company_ruby]', 'てすとえふしーかぶしきがいしゃ')
                ->type('fc[email]', 'testfc@gmail.com')
                ->type('fc[zipcode]', '1001111')
                ->type('fc[pref]', 'サンプル都')
                ->type('fc[city]', 'サンプル中央区')
                ->type('fc[street]', 'テスト番地123')
                ->type('fc[latitude]', '35.689738')
                ->type('fc[longtitude]', '139.700391')
                ->type('fc[tel]', '09010001000')
                ->type('fc[staff]', 'テストFCスタッフ')
                ->type('fc[staff_ruby]', 'てすとえふしーすたっふ')
                ->press('FCを登録')
                ->assertPathIs('/users/store');
        });
        //DBからデータをとってくる
        //how to get the last record!
        $data = User::where('role, 2')->orderBy('id', 'DESC')->first();
        $this->assertTrue(!empty($data));
    }
}
