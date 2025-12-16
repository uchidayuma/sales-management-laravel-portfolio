<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use App\Models\User;

class ContactDetailTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testCreateNewContact()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(1))
                ->visit('/contact/unassigned/list')
                ->assertSee('1')
                ->clickLink('1')
                ->assertSee('案件詳細')
                ->back()
                ->assertPathIs('/contact/unassigned/list')
                ->visit('/contact/assigned/list')
                ->assertSee('FC依頼済み一覧')
                ->clickLink('2')
                ->assertSee('案件詳細')
                ->assertSee('編集');
        });
    }

    public function testContactDetailCancel()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(1))
                ->visit('/contact/cancel')
                ->assertSee('キャンセル案件一覧')
                ->click('@contact-detail')
                ->assertSee('編集')
                ->assertDontSee('キャンセル案件に変更');
        });
    }


    public function testContactDetail()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(2))
                ->visit('/contact/customers/list')
                ->assertSee('案件一覧')
                ->assertVisible('.step__label')
                ->assertVisible('.contact-type__label')
                // Todo : 全件表示のコードに変更してテストも一緒に修正
                ->visit('/contact/123')
                ->assertSee('案件詳細')
                ->assertVisible('#before1')
                ->assertVisible('#before2')
                ->assertVisible('#before3')
                ->assertVisible('#after1')
                ->assertVisible('#after2')
                ->assertVisible('#after3')
                ->back()
                ->assertSee('案件一覧');
        });
    }

    //図面見積もりと訪問見積もりの切り替えボタンテスト
    public function testContactTypeSwitch()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(2))
            ->visit('contact/97')
            ->assertSee('法人図面見積もり')
            ->click('@switch')
            ->acceptDialog()
            ->pause(1000)
            ->screenshot('detail97')
            ->assertSee('法人訪問見積もり');
        });
    }

    public function testContactTypeSwitch2()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(2))
            ->visit('contact/98')
            ->assertSee('個人図面見積もり')
            ->click('@switch')
            ->acceptDialog()
            ->pause(1000)
            ->screenshot('detail98')
            ->assertSee('個人訪問見積もり');
        });
    }

    /* 自社案件か否かでアラートを表示 */
    public function testContactAlert()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(2))
                ->visit('/contact/71')
                ->assertSee('他FC対応中のため、見積書の作成はできません');
        });
    }

    public function testFcOwnContactDeleteOnly()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(2))
                ->visit('/contact/128')
                ->assertDontSee('お問い合わせ削除');
        });
    }

    /* 案件詳細のサンプル送付表示 */
    public function testSampleSend()
    {
        $this->browse(function ($browser) {
            $browser->loginAs(User::find(2))
                ->visit('/contact/151')
                ->assertSee('本部がサンプル送付（未送付）')
                ->visit('/contact/152')
                ->assertSee('自社でサンプル送付')
                ->visit('/contact/153')
                ->assertSee('2023年02月03日に本部がサンプル送付');
        });
    }
}
