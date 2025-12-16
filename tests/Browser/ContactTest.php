<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Contact;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Carbon\Carbon;

class ContactTest extends DuskTestCase
{
    /**
     * @test
     * A Dusk test example.
     */
    public function testAssignFc()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->maximize()
                ->visit('/contact/assign/10')
                ->pause(5000)
                ->click('@fc2')
                ->click('#submit-btn')
                ->acceptDialog()
                ->assertSee('FCに見積もりを依頼しました');
        });
    }

    /*
    public function testAdminFinish()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->maximize()
                ->visit('/contact/39')
                ->click('.datail-table')
                ->driver->executeScript('window.scrollTo(0, 1000);');
            $browser
                ->screenshot('contact-scroll')
                ->click('@finish')
                ->assertSee('納品登録を完了しました。');
        });
    }
    */

    public function testPendingCancel()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->maximize()
                ->visit('/contact/pending/list')
                ->click('@select-modal99')
                ->pause(5000)
                ->click('@bussiness-cancel')
                ->pause(5000)
                ->assertSee('ステータスを失注に設定しました');
        });
    }

    // 受注日の確認
    public function testContractDate()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->maximize()
                ->visit('/contact/pending/list')
                ->click('@select-modal142')
                ->pause(5000)
                ->click('@quotation-success')
                ->pause(5000)
                ->assertPathIs('/transactions/dispatch/pending');
        });
        Contact::where('id', 142)->update(['contracted_at' => '2022-01-11 11:11:11', 'step_id' => 9]);
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->maximize()
                ->visit('/transactions/dispatch/pending')
                ->assertSee('2022年01月11日')
                ->assertSee('2022年02月22日');
        });
    }

    public function testResultCustomer()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->maximize()
                ->visit('/contact/pending/list')
                ->click('@select-modal41')
                ->pause(5000)
                ->click('@quotation-success')
                ->pause(5000)
                ->click('@select-quotation-modal')
                ->pause(5000)
                ->click('@radio1')
                ->click('@quotation-submit')
                ->screenshot('Result-success')
                ->assertSee('顧客が選んだ見積もりを確定しました');
        });
    }

    public function testViewAlert()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->maximize()
                ->visit('/contact/assigned/list')
                // ->assertVisible('.alert-tr')
                ->visit('/contact/quotations/needs')
                ->assertVisible('.alert-tr')
                ->visit('/contact/quotations/needs')
                ->assertVisible('.alert-tr');
        });
    }

    // アサインしたときに確認時間が入るかどうかのテスト
    public function testFcConfirmedAt()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->visit('/contact/134')
                ->assertSee('FC案件確認テスト')
                ->assertDontSee('FC案件確認日時');
            $browser->loginAs(User::find(1))
                ->visit('/contact/134')
                ->assertSee('FC案件確認日時');
        });
    }

    // 本部画面から放置リストが見えるかどうか
    public function testFcLeaveAloneList()
    {
        $now = new Carbon();
        $created_at = new Carbon();
        // FCにアサインした日を2日前に設定
        Contact::where('id', '135')->update(['created_at' => $created_at->subDay(2)->subHour(1)->format('Y-m-d H:i:s'), 'fc_assigned_at' => $now->subDay(2)->format('Y-m-d H:i:s')]);
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/notifications/create')
                ->select('@leave-select', '1')
                ->click('@leave-submit')
                ->assertSee('放置日数設定を更新しました！')
                ->click('@leave-alone-list')
                ->pause(2000)
                // ->assertSee('FC放置案件確認テスト')
                // ->assertSeeLink(135)
                // 放置設定を変えたら消えるか
                ->visit('/notifications/create')
                ->select('@leave-select', '5')
                ->click('@leave-submit')
                ->assertSee('放置日数設定を更新しました！')
                ->click('@leave-alone-list')
                ->pause(1500)
                ->assertDontSeeLink(135);
        });
    }
    // サンプル送付依頼やり直し
    public function testSampleSendAt()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                    ->visit('/contact/edit/152')
                    ->click('@admin')
                    ->click('@update-button');
            $browser->loginAs(User::find(1))
                    ->visit('/contact/sample/list')
                    ->assertSeeLink('2-152')
                    ->visit('/contact/edit/152')
                    ->value('@sample-send-at-input', '1979-01-01')
                    ->screenshot('testSampleSendAt')
                    ->click('@update-button')
                    ->pause(1000)
                    ->visit('/contact/sample/list')
                    ->assertDontSeeLink('2-152');
        });
    }
    // サンプル送付依頼入力
    public function testInputSampleSendAt()
    {
        $this->browse(function (Browser $browser) {
            $now = new Carbon();
            $browser->loginAs(User::find(1))
                    ->visit('/contact/sample/list')
                    ->assertSeeLink('161')
                    ->visit('/contact/edit/161')
                    ->value('@sample-send-at-input', $now->format('Y-m-d'))
                    ->click('@update-button')
                    ->pause(1000)
                    ->visit('/contact/sample/list')
                    ->assertDontSeeLink('161');
        });
    }
}
