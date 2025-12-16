<?php

namespace tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Laravel\Dusk\Chrome;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class SampleSend extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @test
     * 
     * @return void
     */

    //テストクラスは、必ず「testClassName」のようにtestをつける
    public function testSampleSend()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/contact/sample/list')
                ->assertSee('サンプル送付一覧')
                ->check('@check1')
                ->press('送付完了')
                ->pause(4000)
                ->assertSee('サンプル送付が完了しました');
        });
    }
}
