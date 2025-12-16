<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use App\Models\User;

class ConstructionReport extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testBeforeVisitReport()
    {
        $this->browse(function ($browser) {
            //FCアサイン済みお問い合わせ一覧
            $browser->loginAs(User::find(2))
              ->visit('/contact/before/report')
              ->select('@select', 56)
              ->assertSee('アップロードする画像をドロップ')
              ->attach('#image-01', storage_path('app/public/testing/report.png'))
              ->screenshot('report-date')
              ->click('@dusk-submit')
              ->assertSee('現場画像登録が完了しました。見積もり案件一覧から見積もりが作成できます');
        });
    }

    public function testCreateReport()
    {
        $this->browse(function ($browser) {
            //FCアサイン済みお問い合わせ一覧
            $browser->loginAs(User::find(2))
              ->visit('/report/create/55')
              ->pause(1000)
              ->type('@finish-date', '2020-05-22')
              ->attach('#image-01', storage_path('app/public/testing/report.png'))
              ->click('@finish-submit')
              ->assertPathIs('/report/list')
              ->assertSee('施工後報告が完了しました');
        });
    }

    public function testSpBforeVisitReport()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
            ->resize(375, 812)
            ->visit('/contact/before/report')
            ->assertDontSee('アップロードする画像をドロップ')
            ->attach('#image-01', storage_path('app/public/testing/report.png'))
            ->screenshot('report-date')
            ->click('@dusk-submit')
            ->assertSee('現場画像登録が完了しました。見積もり案件一覧から見積もりが作成できます');
        });
    }

    public function testSpCreateReport()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
            ->resize(375, 812)
            ->visit('/report/create/137')
            ->pause(1000)
            ->assertDontSee('アップロードする画像をドロップ')
            ->type('@finish-date', '2020-05-22')
            ->attach('#image-01', storage_path('app/public/testing/report.png'))
            ->click('@finish-submit')
            ->assertSee('施工後報告が完了しました');
        });
    }
}
