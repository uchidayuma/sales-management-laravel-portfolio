<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class QuotationMaterialTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testCreateMaterialQuotation()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
              ->visit('/contact/quotations/needs')
              ->screenshot('quotation.create')
              ->click('@create-button')
              ->click('@material-tab')
              ->pause(4000)
              ->type('@client_name', 'カエルマテリアル株式会社')
              ->select('@turf-select', 1)
              ->type('.js-material-product-count', 1)
              ->select('@cut-turf-select', 1)
              ->pause(1000)
              ->type('vertical', 5.5)
              ->type('horizontal', 0.8)
              ->pause(1000)
              // 切り売り人工芝に紐づくカットメニュー
              ->assertSelectHasOptions('@cut-menu-select', [0,55,56,71])
              ->select('@cut-menu-select', 71)
              ->pause(1000)
              ->click('#js-cut-sub-plus')
              ->assertSelectHasOptions('@sub-cut-select', [12,13,20])
              ->click('.js-add-cut-set')
              ->type('@turf-set-num', 2)
              ->with('#cut-sub-body', function ($body) {
                    $body->select('@sub-cut-select', 12)
                         ->type('.js-material-product-count', 3);
              })
              ->screenshot('material-create')
              ->type('@other-price', 3000)
              ->type('#material-discount', 1000)
              ->pause(1000)
              // ->select('.js-account-infomation', 'カエルの口座番号2')
              // ->assertInputValue('.js-memo', 'カエルの口座番号2')
              ->screenshot('material-post')
              ->click('#post-material-quotation')
              ->pause(1000)
              ->assertDialogOpened('この内容で見積書を作成します。よろしいですか？')
              ->acceptDialog()
              ->pause(1000)
              ->assertDialogOpened('続けて同じ案件に見積もりを作りますか？')
              ->acceptDialog()
              ->pause(1000)
              ->assertDialogOpened('見積もり内容をコピーして作りますか？')
              ->acceptDialog()
              ->pause(15000)
              ->assertSeeIn('.alert', '新しい見積もりを作成しました')
              ->assertSelected('@turf-select', 1);
        });
    }

    public function testShowMaterialQuotation()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
            ->visit('/quotations/14')
            ->assertSee('カエルマテリアル株式会社')
            ->assertSee('カット賃')
            ->assertSee('U字釘')
            ->assertSee('防草シート')
            ->assertSee('1.22m ×7.20m')
            ->assertSee('1.50m ×8m');
        });
    }

    public function testUpdateMaterialQuotation()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
            ->visit('/quotations/14')
            ->click('@edit')
            ->pause(7000)
            ->type('@title', 'UPDATE見積書')
            ->type('@name', 'UPDATEカエルマテリアル株式会社')
            ->type('@turf-number2', 1)
            ->with('.dusk-row-3', function ($tr) {
                $tr->text('.js-vertical', 7);
                $tr->click('.js-add-cut-set');
            })
            // 追加された行のセット数を増やす
            ->with('.dusk-row-5', function ($tr) {
                $tr->type('@turf-set-num', 33);
            })
            ->screenshot('material-update-tan')
            ->with('.dusk-row-6', function ($tr) {
                $tr->click('.js-add-cut-set');
                $tr->text('.js-horizontal', 0.7);
            })
            // 追加された行のセット数を増やす
            ->with('.dusk-row-9', function ($tr) {
                $tr->type('@turf-set-num', 44);
            })
            ->screenshot('type-set-num')
            ->with('.dusk-row-11', function ($tr) {
                $tr->type('.js-material-product-count', 20);
            })
            ->screenshot('material-update-tan2')
            ->type('#material-discount', 200)
            ->screenshot('material-update-discount')
            ->click('#post-material-quotation')
            ->screenshot('material-update-after-post')
            ->pause(10000)
            ->assertSee('修正しました')
            ->visit('/quotations/14')
            ->assertSee('3,056,004円')
            ->assertSee('UPDATE見積書')
            ->assertSee('UPDATEカエルマテリアル株式会社');
        });
    }

    public function testCopyMaterialQuotation()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
            ->visit('/quotations')
            ->click('@copy14')
            ->pause(10000)
            ->screenshot('copy-material')
            // ->click('[name="q[created_at]"]')
            // ->pause(1000)
            // ->click('.today')
            ->with('.dusk-row-15', function ($tr) {
                $tr->assertSelected('.js-material-product-select', 71);
                $tr->assertInputValue('.js-material-unit-price', 1500);
                $tr->assertInputValue('.js-unit', 'm');
                $tr->assertInputValue('.js-material-product-count', 8.4);
            })
            ->with('.dusk-row-19', function ($tr) {
                $tr->assertSelected('.js-material-product-select',71);
                $tr->assertInputValue('.js-material-unit-price', 1000);
                $tr->assertInputValue('.js-unit', 'm');
                $tr->assertInputValue('.js-material-product-count', 9.5);
            })
            ->screenshot('copy-material2')
            ->click('#post-material-quotation')
            ->acceptDialog()
            ->dismissDialog()
            ->screenshot('copy-material-after')
            ->pause(5000)
            ->assertSeeIn('.alert', '新しい見積もりを作成しました');
        });
    }
}
