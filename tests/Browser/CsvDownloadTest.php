<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class CsvDownloadTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testOpenModal()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/contact/customers/list')
                ->click('@csv-export')
                ->pause(5000)
                ->click('@datepicker_first')
                ->pause(2000)
                ->click('@datepicker_last')
                ->pause(2000)
                ->assertSee('テレアポ業者用CSVエクスポート')
                ->screenshot("open-modal");
        });
    }
    public function testCustomOpenModal()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/contact/customers/list')
                ->click('@custom-csv-export')
                ->pause(5000)
                ->assertSee('選択式CSVエクスポート')
                ->screenshot("open-modal2");
        });
    }

    /* adminでcsvボタンが存在しつつ、fcではボタンが存在しないことの確認 */
    /* admin */
    public function adminCsvButton()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/contact/customers/list')
                ->assertSee('CSVエクスポート');
        });
    }
    /* fc */
    public function fcCsvButton()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->visit('/contact/customers/list')
                ->assertDontSee('CSVエクスポート')
                ->assertDontSee('ゆうプリ用CSVエクスポート')
                ->visit('/contact/sample/list')
                ->assertDontSee('ゆうプリ用CSVエクスポート');
        });
    }
}
