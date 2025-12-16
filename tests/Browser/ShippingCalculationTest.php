<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class ShippingCalculationTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testShippingCalculationSmall()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->visit('/transactions/create/order')
                ->pause(3000)
                ->click('.js-remove-row')
                ->pause(1000)
                ->click('.js-remove-row')
                ->pause(1000)
                ->radio('t[address_type]', 1)
                ->with('.dusk-row-3', function ($tr) {
                    $tr->select('@sub-select', 9)
                    ->type('@sub-number', 5);
                })
                ->click('#js-sub-plus')
                ->with('.dusk-row-4', function ($tr) {
                    $tr->select('@sub-select', 10)
                    ->type('@sub-number', 3);
                })
                ->click('#js-calc')
                ->pause(1000)
                ->assertValue('@shipping-cost', 2000)
                ->radio('t[address_type]', 5)
                ->value('#receiving-address-edit', '〒1000005 サンプル都サンプル中央区サンプル台')
                ->click('#js-calc')
                ->pause(1000)
                ->assertValue('@shipping-cost', 2600);
        });
    }
    // 大サイズだけの商品をカートに入れて送料を計算する
    public function testShippingCalculationLarge()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->visit('/transactions/create/order')
                ->pause(3000)
                ->click('.js-remove-row')
                ->pause(1000)
                ->radio('t[address_type]', 1)
                ->select('@cut-turf-select', 1)
                ->type('vertical', 3)
                ->pause(1000)
                ->type('horizontal', 1.1)
                ->pause(1000)
                ->select('@cut-menu-select', 50)
                ->with('.dusk-row-3', function ($tr) {
                    $tr->click('.js-remove-row');
                })
                ->click('#js-calc')
                ->pause(1000)
                ->assertValue('@shipping-cost', 1500)
                ->radio('t[address_type]', 5)
                ->value('#receiving-address-edit', '〒1000005 サンプル都北エリア')
                ->click('#js-calc')
                ->pause(1000)
                ->assertValue('@shipping-cost', 1700);
        });
    }
    // 特大サイズだけの商品をカートに入れて送料を計算する
    public function testShippingCalculationExtraLarge()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->visit('/transactions/create/order')
                ->radio('t[address_type]', 1)
                ->select('@turf-select', 67)
                ->type('@turf-number1', 5)
                ->select('@cut-turf-select', 1)
                ->type('vertical', 8.8)
                ->pause(1000)
                ->type('horizontal', 1.7)
                ->select('@cut-menu-select', 50)
                ->with('.dusk-row-3', function ($tr) {
                    $tr->click('.js-remove-row');
                })
                ->click('#js-calc')
                ->pause(1000)
                ->assertValue('@shipping-cost', 16200)
                ->radio('t[address_type]', 5)
                ->value('#receiving-address-edit', '福島県郡山市')
                ->click('#js-calc')
                ->pause(1000)
                ->assertValue('@shipping-cost', 19800);
        });
    }
    // 小サイズと大サイズの商品をカートに入れて送料を計算する
    public function testShippingCalculationSmallAndLarge()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->visit('/transactions/create/order')
                ->radio('t[address_type]', 1)
                ->select('@turf-select', 67)
                ->type('@turf-number1', 5)
                ->select('@cut-turf-select', 1)
                ->type('vertical', 3.3)
                ->pause(1000)
                ->type('horizontal', 1.1)
                ->select('@cut-menu-select', 50)
                ->with('.dusk-row-3', function ($tr) {
                    $tr->select('@sub-select', 9)
                    ->type('@sub-number', 5);
                })
                ->click('#js-calc')
                ->pause(1000)
                ->assertValue('@shipping-cost', 16000)
                ->radio('t[address_type]', 5)
                ->value('#receiving-address-edit', '北海道札幌市')
                ->click('#js-calc')
                ->pause(1000)
                ->assertValue('@shipping-cost', 22600);
        });
    }
    // 大サイズと特大サイズの商品をカートに入れて送料を計算する
    public function testShippingCalculationLargeAndExtraLarge()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->visit('/transactions/create/order')
                ->radio('t[address_type]', 1)
                ->select('@turf-select', 67)
                ->type('@turf-number1', 2)
                // 以下、大サイズと特大サイズの商品をカートに追加する処理
                ->select('@cut-turf-select', 1)
                ->type('vertical', 4.5)
                ->type('horizontal', 1.9)
                ->waitFor('.dusk-row-3')
                ->with('.dusk-row-3', function (Browser $tr) {
                    $tr->click('.js-remove-row');
                })
                ->click('#js-calc')
                ->pause(1000)
                ->assertValue('@shipping-cost', 7500)
                ->radio('t[address_type]', 5)
                ->value('#receiving-address-edit', '広島県広島市')
                ->click('#js-calc')
                ->pause(1000)
                ->assertValue('@shipping-cost', 8700);
        });
    }
    // 小サイズと特大サイズの商品をカートに入れて送料を計算する
    public function testShippingCalculationSmallAndExtraLarge()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->visit('/transactions/create/order')
                ->radio('t[address_type]', 1)
                ->select('@turf-select', 67)
                ->type('@turf-number1', 2)
                ->waitFor('.dusk-row-2')
                ->with('.dusk-row-2', function (Browser $tr) {
                    $tr->click('.js-remove-row');
                })
                ->waitFor('.dusk-row-3')
                ->with('.dusk-row-3', function (Browser $tr) {
                    $tr->select('@sub-select', 9)
                    ->type('@sub-number', 5);
                })
                ->pause(3000)
                ->click('#js-calc')
                ->pause(1000)
                ->assertValue('@shipping-cost', 7000)
                ->radio('t[address_type]', 5)
                ->value('#receiving-address-edit', '福岡県八女市')
                ->click('#js-calc')
                ->pause(1000)
                ->assertValue('@shipping-cost', 9100);
        });
    }
    // 小サイズ（副資材バラ売りと大サイズと特大サイズの商品をカートに入れて送料を計算する
    public function testShippingCalculationSmallAndLargeAndExtraLarge()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->visit('/transactions/create/order')
                ->radio('t[address_type]', 1)
                ->select('@turf-select', 67)
                ->type('@turf-number1', 2)
                ->waitFor('.dusk-row-2')
                ->select('@cut-turf-select', 1)
                ->type('vertical', 4.5)
                ->type('horizontal', 1.9)
                ->click('#js-cut-sub-plus')
                ->waitFor('.dusk-row-3')
                ->with('.dusk-row-3', function (Browser $tr) {
                    $tr->click('.js-remove-row');
                })
                ->with('.js-cut-sub-row', function (Browser $tr) {
                    $tr->select('@sub-cut-select', 14)
                    ->type('.js-product-count', 100);
                })
                ->pause(2000)
                ->click('#js-calc')
                ->pause(1000)
                ->assertValue('@shipping-cost', 8000)
                ->radio('t[address_type]', 5)
                ->value('#receiving-address-edit', '香川県高松市')
                ->click('#js-calc')
                ->pause(1000)
                ->assertValue('@shipping-cost', 10650);
        });
    }
    // 切り売りn特大サイズの複数枚注文
    public function testShippingCalculationCutExtraLarge()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->visit('/transactions/create/order')
                ->pause(3000)
                ->click('.js-remove-row')
                ->pause(1000)
                ->radio('t[address_type]', 1)
                ->with('.dusk-row-2', function ($tr) {
                    $tr->select('@cut-turf-select', 1)
                        ->type('vertical', 4.5)
                        ->type('horizontal', 2)
                        ->click('.js-add-cut-set');
                })
                ->with('.dusk-row-5', function ($tr) {
                    $tr->value('.js-cut-set-num', 3);
                })
                ->with('.dusk-row-3', function ($tr) {
                    $tr->click('.js-remove-row');
                })
                ->click('#js-calc')
                ->pause(1000)
                ->assertValue('@shipping-cost', 8100)
                ->radio('t[address_type]', 5)
                ->value('#receiving-address-edit', '高知県四万十市')
                ->click('#js-calc')
                ->pause(1000)
                ->assertValue('@shipping-cost', 10500);
        });
    } 


    // チャーター便の計算をする
    public function testShippingCalculationCharter()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->visit('/transactions/create/order')
                ->radio('t[address_type]', 1)
                // 以下、チャーター便の計算のための処理（サンプルとして）
                ->select('@turf-select', 80)
                ->type('@turf-number1', 40)
                ->with('.dusk-row-3', function ($tr) {
                    $tr->select('@sub-select', 9)
                    ->type('@sub-number', 5);
                })
                ->click('#js-calc')
                ->pause(1000)
                ->screenshot('shipping-calculation-test-2')
                ->assertSee('※チャーター便料金は目安になります。納期の件も含め本部までご相談ください。');
                // チャーター便の文言と参考価格
        });
    }
    // 販促商品のテスト
    public function testShippingCalculationPromotion()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->visit('/transactions/create/order')
                ->radio('t[address_type]', 1)
                ->with('.js-turf-row', function ($tr) {
                    $tr->click('.js-remove-row');
                })
                ->with('.js-cut-turf-row', function ($tr) {
                    $tr->click('.js-remove-row');
                })
                ->with('.js-sub-row', function ($tr) {
                    $tr->click('.js-remove-row');
                })
                ->click('#js-sales-plus')
                ->waitFor('.dusk-row-4')
                ->with('.dusk-row-4', function (Browser $tr) {
                    $tr->click('#dropdownMenuButton')
                    ->pause(1000)
                    ->click('@product68')
                    ->value('.js-product-count', 41);
                })
                ->click('#js-calc')
                ->pause(5000)
                ->assertValue('@shipping-cost', 1000)
                ->radio('t[address_type]', 5)
                ->value('#receiving-address-edit', '福岡県八女市')
                ->click('#js-calc')
                ->pause(3000)
                ->assertValue('@shipping-cost', 1500);
        });
    }
    // 販促商品のテストその2(同梱不可)
    public function testShippingCalculationPromotion2()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->visit('/transactions/create/order')
                ->radio('t[address_type]', 1)
                ->with('.js-turf-row', function ($tr) {
                    $tr->click('.js-remove-row');
                })
                ->with('.js-cut-turf-row', function ($tr) {
                    $tr->click('.js-remove-row');
                })
                ->with('.js-sub-row', function ($tr) {
                    $tr->click('.js-remove-row');
                })
                // 3種サンプルと20cmサンプルをカートに入れる
                ->click('#js-sales-plus')
                ->waitFor('.dusk-row-4')
                ->with('.dusk-row-4', function (Browser $tr) {
                    $tr->click('#dropdownMenuButton')
                    ->pause(1000)
                    ->click('@product27')
                    ->value('.js-product-count', 1);
                })
                ->click('#js-sales-plus')
                ->waitFor('.dusk-row-5')
                ->with('.dusk-row-5', function (Browser $tr) {
                    $tr->click('#dropdownMenuButton')
                    ->pause(1000)
                    ->click('@product57')
                    ->value('.js-product-count', 1);
                })
                ->click('#js-calc')
                ->pause(3000)
                ->assertValue('@shipping-cost', 1000)
                ->radio('t[address_type]', 5)
                ->value('#receiving-address-edit', '福岡県八女市')
                ->click('#js-calc')
                ->pause(3000)
                ->assertValue('@shipping-cost', 1500);
        });
    }
    // 副資材と販促商品の組み合わせテスト
    public function testShippingCalculationPromotionAndSub()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->visit('/transactions/create/order')
                ->radio('t[address_type]', 1)
                ->with('.js-turf-row', function ($tr) {
                    $tr->click('.js-remove-row');
                })
                ->with('.js-cut-turf-row', function ($tr) {
                    $tr->click('.js-remove-row');
                })
                ->with('.dusk-row-3', function ($tr) {
                    $tr->select('@sub-select', 9)
                    ->type('@sub-number', 5);
                })
                // 3種サンプルと20cmサンプルをカートに入れる
                ->click('#js-sales-plus')
                ->waitFor('.dusk-row-4')
                ->with('.dusk-row-4', function (Browser $tr) {
                    $tr->click('#dropdownMenuButton')
                    ->click('@product27')
                    ->value('.js-product-count', 1);
                })
                ->click('#js-sales-plus')
                ->waitFor('.dusk-row-5')
                ->with('.dusk-row-5', function (Browser $tr) {
                    $tr->click('#dropdownMenuButton')
                    ->click('@product57')
                    ->value('.js-product-count', 1);
                })
                ->click('#js-cut-sub-plus')
                ->waitFor('.dusk-row-6')
                ->with('.dusk-row-6', function (Browser $tr) {
                    $tr->select('@sub-cut-select', 13)
                    ->value('.js-product-count', 2);
                })
                ->click('#js-calc')
                ->pause(6000)
                ->assertValue('@shipping-cost', 2000)
                ->radio('t[address_type]', 5)
                ->value('#receiving-address-edit', '福岡県八女市')
                ->click('#js-calc')
                ->pause(3000)
                ->assertValue('@shipping-cost', 3000);
        });
    }
    // 砂とゴムチップは送料込み
    public function testSandAndRubberChipsIncludeShipping(){
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->visit('/transactions/create/order')
                ->radio('t[address_type]', 1)
                ->with('.js-turf-row', function ($tr) {
                    $tr->click('.js-remove-row');
                })
                ->with('.js-cut-turf-row', function ($tr) {
                    $tr->click('.js-remove-row');
                })
                ->with('.dusk-row-3', function ($tr) {
                    $tr->select('@sub-select', 25)
                    ->type('@sub-number', 5)
                    ->select('@sub-select', 26)
                    ->type('@sub-number', 1);
                })
                ->pause(5000)
                ->click('#js-calc')
                ->pause(1000)
                ->assertValue('@shipping-cost', 0);
        });
    }

    // 本部の管理画面から送料を更新する
    public function testShippingPriceUpdate()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/transactions/admin/settings')
                    // ->assertSee('送料設定')
                    ->value('input[name="p[13]"]', 55000)
                    ->click('@shipping-price-submit')
                    ->visit('/transactions/admin/settings')
                    ->assertValue('input[name="p[13]"]', 55000);
        });
    }
    // 無料商品設定テスト
    public function testFreeShippingItem()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                    ->visit('/transactions/admin/settings')
                    ->select('@free-shipping-item-id-select', 7)
                    ->click('@free-shipping-item-id-submit')
                    ->assertSee('SB30CP1を送料無料商品に設定しました！')
                    ->visit('/transactions/admin/settings')
                    ->assertValue('@free-shipping-item-id-select', 7);
        });
    }
}