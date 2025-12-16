<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ContactSameTest extends DuskTestCase
{
    // 同一顧客を追加
    public function testSameAdd()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/contact/customers/list')
                ->pause(1000)
                ->click('@same-customer-modal-97')
                ->pause(2000)
                ->assertSee('同一顧客一覧')
                ->type('@addSameId', '98')
                ->click('#same-add-button')
                ->pause(10000)
                ->assertDialogOpened('同一顧客を追加しました。')
                ->acceptDialog()
                ->pause(1000)
                ->click('@remodal-close')
                ->pause(1000)
                ->click('@same-customer-modal-98')
                ->pause(5000)
                ->screenshot('contact-same-add')
                ->assertSee('：97');
        });
    }
    // 同一顧客を削除
    public function testSameDelete()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/contact/customers/list')
                ->pause(1000)
                ->click('@same-customer-modal-97')
                ->pause(5000)
                ->assertSee('同一顧客一覧')
                ->pause(5000)
                ->click('@same-delete-98')
                ->pause(5000)
                ->assertDialogOpened('同一顧客を解除しました。')
                ->acceptDialog()
                ->pause(1000)
                ->click('@remodal-close')
                ->pause(1000)
                ->click('@same-customer-modal-98')
                ->pause(2000)
                ->screenshot('contact-same-Delete')
                ->assertDontSee('：97');
        });
    }
}
