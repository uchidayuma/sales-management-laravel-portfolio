<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class RankingTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testSales()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/rankings?order=number&year=' . date('Y') . "&month=" . date('m'))
                ->assertSee('ランキング')
                ->assertSee('施工件数')
                ->clickLink('FCランキング')
                ->assertSee('ランキング')
                ->assertSee('売り上げ');
        });
    }

    //FCアカウントでは5件表示になっているか
    public function testSaleRnakFc()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->visit('/rankings?order=sales')
                ->screenshot('salse-rank')
                ->pause(1000)
                ->assertDontSee('エイチスペース株式会社');
        }); 
    }

    //本部アカウントでは6件目以降も見えているか
    public function testSaleRnakAdmin()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/rankings?order=sales&year=' . date('Y') . "&month=" . date('m'))
                ->screenshot('salse-rank-admin')
                ->pause(1000)
                ->assertSee('FCランキング');
        }); 
    }


}
