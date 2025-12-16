<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class ContactCopyTest extends DuskTestCase
{
    /**
     * @test
     * A Dusk test example.
     */
    // 個人の案件コピー機能
    public function testIndividualCopy()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->maximize()
                ->visit('/contact/customers/list')
                ->click('@contact-copy62')
                ->pause(2000)
                ->assertInputValue('.js-copy-surname','個人案件コピー姓')
                ->assertInputValue('.js-copy-name','個人案件コピー名')
                ->assertInputValue('.js-copy-surname_ruby','こじんあんけんこぴーせい')
                ->assertInputValue('.js-copy-name_ruby','こじんあんけんこぴーめい')
                ->assertInputValue('.js-copy-tel','09012345678')
                ->assertInputValue('.js-copy-fax','09012345678')
                ->assertInputValue('.js-copy-zipcode','7777777')
                ->assertInputValue('.js-copy-pref','サンプル都')
                ->assertInputValue('.js-copy-city','サンプル中央区')
                ->assertInputValue('.js-copy-street','サンプル通り7番地')
                ->assertInputValue('.js-copy-email','test@gmail.com')
                ->pause(1000)
                ->press('@contact2-submit')
                ->pause(1000)
                ->screenshot('contact-copy-kozin')
                ->assertSee('新しいお問い合わせを登録しました');
        });
    }

    // 法人の案件コピー機能
    public function testCorporationCopy()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->maximize()
                ->visit('/contact/customers/list')
                ->click('@contact-copy63')
                ->pause(2000)
                ->assertInputValue('.js-copy-surname','法人案件コピー姓法人案件コピー名')
                ->assertInputValue('.js-copy-surname_ruby','ほうじんあんけんこぴーせいほうじんあんけんこぴーめい')
                ->assertInputValue('.js-copy-tel','09012345678')
                ->assertInputValue('.js-copy-fax','09012345678')
                ->assertInputValue('.js-copy-zipcode','7777777')
                ->assertInputValue('.js-copy-pref','サンプル都')
                ->assertInputValue('.js-copy-city','サンプル中央区')
                ->assertInputValue('.js-copy-street','サンプル通り7番地')
                ->assertInputValue('.js-copy-email','test@gmail.com')
                ->pause(1000)
                ->press('@contact6-submit')
                ->pause(1000)
                ->screenshot('contact-copy-houzin')
                ->assertSee('新しいお問い合わせを登録しました');
        });
    }
}
