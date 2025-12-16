<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class InvoiceTest extends DuskTestCase
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
                    ->visit('/invoices')
                    ->assertSee('月別請求書');
        });
    }

    public function testFcRedirect()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                    ->visit('/invoices')
                    ->assertPathIs('/');
        });
    }

    /*
    public function testInvoiceList()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                    ->visit('/invoices?year=2021&month=3')
                    ->assertSee('月別請求書')
                    ->screenshot('invoices-202103')
                    ->assertSeeLink('17')
                    ->assertSeeLink('18')
                    // ->assertSeeLink('20')
                    ->clickLink('17');

            // 最後に開いたタブを取得
            $window = collect($browser->driver->getWindowHandles())->last();

            // タブを最後に開いたタブに切り替える
            $browser->driver->switchTo()->window($window);
   
            // テスト再開
            $browser->screenshot('invoices-202103-detail')
                    ->assertSee('17');
        });
    }
    */
}
