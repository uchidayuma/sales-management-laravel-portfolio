<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class ProductTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testIndex()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->visit('/products')
                ->assertVisible('.stock-high')
                ->assertVisible('.stock-middle')
                ->assertVisible('.stock-low')
                ->loginAs(User::find(1))
                ->visit('/products')
                ->assertSee('在庫一覧')
                ->screenshot('product.index')
                ->assertSee('サンプル芝');
        });
    }

    public function testAjaxUpdate()
    {
        $this->browse(function (Browser $browser) {
            // 同じクラスで2つ以上メソッドを使う場合はログインし直す必要なし
            $browser->loginAs(User::find(1))
                ->visit('/products')
                ->assertSee('在庫一覧')
                ->assertSee('サンプル芝')
                ->type('stock', '1')
                ->pause(1000)
                ->screenshot('product.index')
                ->click('.stock-update')
                ->pause(3500)
                ->assertSee('在庫を1個に更新しました。');
        });
    }

    public function testUpdate()
    {
        $this->browse(function (Browser $browser) {
            // 同じクラスで2つ以上メソッドを使う場合はログインし直す必要なし
            $browser->loginAs(User::find(1))
                ->visit('/products/2')
                ->assertSee('製品詳細')
                ->assertSee('サンプル芝COOL')
                ->assertSee('商品名')
                ->assertSee('登録日')
                ->assertSee('No')
                ->visit('/products/edit/2')
                ->type('p[material]', 'カットして送ります')
                ->click('#update-submit')
                ->assertSee('製品の修正が完了しました！');
        });
    }
}
