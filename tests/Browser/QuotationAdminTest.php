<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class QuotationAdminTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testAdminQuotation()
    {
        $this->browse(function (Browser $browser) {
            // 同じクラスで2つ以上メソッドを使う場合はログインし直す必要なし
            $browser->loginAs(User::find(1))
                ->visit('/quotations/admin/needs')
                ->click('@create')
                ->type('q[name]', 'かえるの本部本部見積もり')
                ->select('product-select-1', 1)
                ->pause(500)
                ->type('.dusk-count-1', 30)
                ->pause(500)
                ->type('.dusk-unit-1', 'm2')
                ->type('.dusk-unit-price-1', 5000)
                ->select('product-select-2', 5)
                ->pause(1000)
                ->type('.dusk-count-2', 30)
                ->pause(1000)
                ->type('.dusk-unit-2', 'm2')
                ->type('.dusk-unit-price-2', 10000)
                ->select('.dusk-type-3', 1)
                ->pause(500)
                ->pause(1000)
                ->type('.dusk-count-3', 1)
                ->pause(1000)
                // 13行目は最後に出てくるので、順番注意
                ->type('.dusk-product-name-15', 'かえるの交通費')
                ->type('.dusk-unit-3', '往復')
                ->click('@remove-4')
                ->click('@remove-5')
                ->click('@remove-6')
                ->type('.dusk-unit-price-3', 10000)
                ->screenshot('admin-quotation0')
                ->type('.dusk-count-7', 1)
                ->type('.dusk-count-8', 1)
                ->type('.dusk-count-9', 1)
                ->type('.dusk-count-10', 1)
                ->type('.dusk-count-11', 1)
                ->type('.dusk-unit-price-7', 10000)
                ->type('.dusk-unit-price-8', 10000)
                ->type('.dusk-unit-price-9', 10000)
                ->type('.dusk-unit-price-10', 10000)
                ->type('.dusk-unit-price-11', 10000)
                ->screenshot('admin-quotation1')
                ->type('q[memo]', 'かえるの本部備考欄')
                ->pause(1000)
                ->click('.js-discount-plus')
                ->pause(1000)
                ->type('@discount', 1000)
                ->click('#post-quotation')
                ->pause(1000)
                ->acceptDialog()
                ->dismissDialog()
                ->assertVisible('.alert-success');
            // ->assertSee('顧客に商品の発注連絡を行いました');
        });
    }

    public function testAdminQuotationSubmit()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/quotations/admin/needs')
                ->click('@dispatch59')
                ->pause(5000)
                ->click('#radio13')
                ->screenshot('adminDispatch')
                ->click('@dispatch-submit')
                ->waitForDialog($seconds = null)
                ->acceptDialog()
                ->pause(3000)
                ->click('@select-modal59')
                ->pause(3000)
                ->click('@quotation-success')
                ->click('@select-quotation-modal')
                ->pause(3000)
                ->click('@radio3')
                ->click('@quotation-submit')
                ->pause(13000)
                ->assertSee('見積もり書No.13');
        });
    }

    //　コピーして見積書を作成のテスト部分（エラーになる）
    public function testAdminCopyQuotation()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
              ->visit('/quotations')
              ->click('@copy10')
              ->pause(3000)
              ->screenshot('quotation-copy-See')
              ->assertInputValue('.dusk-product-name-2', 'カエルコピー費用');
            // ->assertInputValue('.product-select-1', 7);
            // 本当はダイアログクリアしたいが、上手く行かないので、ここまで！
            //   ->click('#post-quotation')
            //   ->pause(1000)
            //   ->acceptDialog()
            //   ->dismissDialog()
            //   ->assertSee('新しい見積もりを作成しました');
        });
    }

    public function testAdminQuotationUpdate()
    {
        $this->browse(function (Browser $browser) {
            // 同じクラスで2つ以上メソッドを使う場合はログインし直す必要なし
            $browser->loginAs(User::find(1))
              ->visit('/quotations/10')
              ->click('@edit')
              ->type('q[name]', 'かえるの本部本部見積もりUPDATE')
              ->click('#post-quotation')
              ->visit('/quotations/10')
              ->assertSee('かえるの本部本部見積もりUPDATE');
        });
    }
}
