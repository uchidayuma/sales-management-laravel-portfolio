<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Transaction;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Facebook\WebDriver\WebDriverKeys;

class TransactionTest extends DuskTestCase
{
    // シンプル発注書作成テスト
    public function testCreate()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->maximize()
                ->pause(5000)
                ->visit('/transactions/create/order/39')
                ->radio('t[address_type]', '1')
                ->type('@memo', '備考欄')
                ->assertPathIs('/transactions/create/order/39')
                ->assertSeeLink('見積書を確認')
                ->select('@turf-select', 1)
                ->pause(1000)
                ->type('@turf-number1', 50)
                ->select('@cut-turf-select', 1)
                ->select('@cut-menu-select', 50)
                ->type('vertical', 10)
                ->pause(1000)
                ->type('horizontal', 1)
                ->pause(1000)
                // 切り売り人工芝に紐づくカットメニュー
                ->select('.js-cut-select', 1)
                ->pause(1000)
                ->type('.js-cut-length', 5)
                ->pause(1000)
                // 副資材まとめ売とバラ売り両方に存在することを確認
                ->assertSelectHasOptions('@sub-select', [12, 13, 20])
                ->select('@sub-select', 12)
                ->type('@sub-number', 5)
                ->click('#js-cut-sub-plus')
                ->assertSelectHasOptions('@sub-cut-select', [12, 13, 20])
                // ボンド＆ターポリンシートと屋上用シートは出さない
                ->assertSelectMissingOptions('@sub-cut-select', [64, 82])
                ->with('#cut-sub-body', function ($tr) {
                    $tr->click('.js-remove-row');
                })
                ->click('#js-sales-plus')
                ->pause(1000)
                ->click('@sales-select')
                ->mouseover('@product31')
                ->pause(3000)
                ->click('@product31')
                ->type('@sales-number', 5)
                ->click('#js-etc-plus')
                ->pause(1000)
                ->type('@other-name', 'テスト手動入力')
                ->type('@other-price', 3333)
                ->type('@other-unit', '本')
                ->type('@other-count', 3)
                ->pause(1000)
                ->value('@delivery', '2028-01-01')
                ->value('@delivery2', '2028-01-02')
                ->value('@delivery3', '2028-01-03')
                ->click('#js-calc')
                ->click('@post')
                ->click('@commit')
                ->pause(3000)
                ->assertPathIs('/')
                // 本部発送待ちで追加発注ができるかどうかテスト
                ->visit('/contact/transaction/pending/list')
                ->click('@create126')
                ->assertSee('追加発注分の発注書です。');
        });
    }
    // 工場引取テスト
    public function testFactoryPickUp()
    {
        $this->browse(function (Browser $browser) {
            $transaction = new Transaction;
            $arrival_date = $transaction->getDeliveryDate(2);
            $previous_date = $transaction->getDeliveryDate(2)->subDay();
            $browser->loginAs(User::find(2))
                ->maximize()
                ->visit('/transactions/create/order/39')
                ->pause(5000)
                ->radio('t[address_type]', '3')
                ->screenshot('factory-pickup-1')
                ->type('@memo', '備考欄')
                ->select('@turf-select', 1)
                ->type('@turf-number1', 1)
                ->with('#product-cut-body', function ($tr) {
                    $tr->click('.js-remove-row');
                })
                ->with('#sub-body', function ($tr) {
                    $tr->click('.js-remove-row');
                })
                ->click('#js-calc')
                ->pause(3000)
                ->value('@delivery', $arrival_date->format('Y-m-d'))
                ->click('@post')
                ->assertEnabled('@commit');
        });
    }

    // 発注書詳細確認
    public function testShow()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->maximize()
                ->visit('/transactions')
                ->click('@detail-button')
                ->back()
                ->click('@transaction-show')
                ->assertSee('発注書確認');
        });
    }
    //切り売りを複数枚発注するパターン
    //最短10営業日
    public function testOrderCutSet()
    {
        $this->browse(function (Browser $browser) {
            $transaction = new Transaction;
            $arrival_date = $transaction->getDeliveryDate(10);
            $previous_date = $transaction->getDeliveryDate(10)->subDay();
            \Log::debug(print_r($arrival_date, true));
            \Log::debug(print_r($previous_date, true));
            $browser->loginAs(User::find(2))
                ->maximize()
                ->visit('/transactions/create/order')
                ->radio('t[address_type]', '1')
                ->pause(1000)
                ->select('@turf-select', 1)
                ->type('@turf-number1', 2)
                ->pause(1000)
                ->select('@cut-turf-select', 1)
                ->select('@cut-menu-select', 50)
                ->type('vertical', 10)
                ->pause(1000)
                ->type('horizontal', 1)
                ->pause(1000)
                ->click('.js-add-cut-set')
                ->type('@turf-set-num', 2)
                ->pause(1000)
                ->select('@sub-select', 12)
                ->type('@sub-number', 5)
                ->click('#js-calc')
                ->type('@delivery', $previous_date->format('Y-m-d'))
                ->assertDialogOpened('到着希望日が最短お届け日時よりも短くなっています。到着希望日を選択し直してください。')
                ->acceptDialog()
                ->value('@delivery', $arrival_date->format('Y-m-d'))
                ->pause(1000)
                ->click('@post')
                ->assertSee($arrival_date->format('Y年m月d日'))
                ->click('@commit')
                ->assertPathIs('/');

            $max_id = Transaction::max('id');
            $browser->loginAs(User::find(2))
                ->visit('/transactions/' . $max_id)
                ->assertSee('2枚');
        });
    }
    // 登録案件テスト
    //人工芝1〜5反のみ4営業日
    public function testRegistrationOrderStatus5()
    {
        $this->browse(function (Browser $browser) {
            $transaction = new Transaction;
            $arrival_date = $transaction->getDeliveryDate(4);
            $previous_date = $transaction->getDeliveryDate(4)->subDay();
            $browser->loginAs(User::find(2))
                ->visit('/transactions/create/order/110')
                ->pause(5000)
                ->radio('t[address_type]', '1')
                ->click('.js-remove-row')
                ->pause(1000)
                ->click('.js-remove-row')
                ->pause(1000)
                ->click('.js-remove-row')
                ->pause(1000)
                ->click('#js-turf-plus')
                ->pause(3000)
                ->select('@turf-select', 1)
                ->type('@turf-number4', 4)
                ->pause(1000)
                ->click('#js-calc')
                ->type('@delivery', $previous_date->format('Y-m-d'))
                ->assertDialogOpened('到着希望日が最短お届け日時よりも短くなっています。到着希望日を選択し直してください。')
                ->acceptDialog()
                ->assertInputValue('@delivery', '')
                ->value('@delivery', $arrival_date->format('Y-m-d'))
                ->click('@post')
                ->assertSee($arrival_date->format('Y年m月d日'))
                ->click('@commit')
                ->assertPathIs('/');
            // ->assertSee('発注が確定されました。');
        });
    }
    // 登録案件副資材まとめ限定最短4営業日
    public function testRegistrationOrderSubOnly()
    {
        $this->browse(function (Browser $browser) {
            $transaction = new Transaction;
            $arrival_date = $transaction->getDeliveryDate(4);
            $previous_date = $transaction->getDeliveryDate(4)->subDay();
            $browser->loginAs(User::find(2))
                ->visit('/transactions/create/order/115')
                ->radio('t[address_type]', '1')
                ->pause(1000)
                ->click('.js-remove-row')
                ->pause(1000)
                ->click('.js-remove-row')
                ->pause(1000)
                ->select('@sub-select', 12)
                ->type('@sub-number', 5)
                ->pause(2000)
                ->click('#js-calc')
                ->type('@delivery', $previous_date->format('Y-m-d'))
                ->assertDialogOpened('到着希望日が最短お届け日時よりも短くなっています。到着希望日を選択し直してください。')
                ->acceptDialog()
                ->assertInputValue('@delivery', '')
                ->value('@delivery', $arrival_date->format('Y-m-d'))
                ->click('@post')
                ->assertSee($arrival_date->format('Y年m月d日'))
                ->click('@commit')
                ->assertPathIs('/');
            // ->assertSee('発注が確定されました。');
        });
    }

    // 副資材まとめとサンプルの組み合わせでも最短4営業日
    public function testRegistrationOrderSubAndSample()
    {
        $this->browse(function (Browser $browser) {
            $transaction = new Transaction;
            $arrival_date = $transaction->getDeliveryDate(4);
            $previous_date = $transaction->getDeliveryDate(4)->subDay();
            $browser->loginAs(User::find(2))
                ->visit('/transactions/create/order')
                ->radio('t[address_type]', '1')
                ->pause(1000)
                ->click('.js-remove-row')
                ->pause(1000)
                ->click('.js-remove-row')
                ->pause(1000)
                ->select('@sub-select', 12)
                ->type('@sub-number', 5)
                ->pause(1000)
                ->click('#js-sales-plus')
                ->pause(1000)
                ->with('.js-sales-row', function ($tr) {
                    $tr->click('#dropdownMenuButton')
                        ->pause(500)
                        ->click('@product28')
                        ->type('.js-product-count', 1);
                })
                ->click('#js-calc')
                ->type('@delivery', $previous_date->format('Y-m-d'))
                ->assertDialogOpened('到着希望日が最短お届け日時よりも短くなっています。到着希望日を選択し直してください。')
                ->acceptDialog()
                ->assertInputValue('@delivery', '')
                ->value('@delivery', $arrival_date->format('Y-m-d'))
                ->click('@post')
                ->assertSee($arrival_date->format('Y年m月d日'))
                ->click('@commit')
                ->assertPathIs('/');
            // ->assertSee('発注が確定されました。');
        });
    }
    //人工芝6〜10反5営業日
    public function testRegistrationOrderStatus0()
    {
        $this->browse(function (Browser $browser) {
            $transaction = new Transaction;
            $arrival_date = $transaction->getDeliveryDate(5);
            $arrival2_date = $transaction->getDeliveryDate(5)->addDay();
            $previous_date = $transaction->getDeliveryDate(5)->subDay();
            $browser->loginAs(User::find(2))
                ->visit('/transactions/create/order/111')
                ->radio('t[address_type]', '1')
                ->pause(1000)
                ->click('.js-remove-row')
                ->pause(1000)
                ->click('.js-remove-row')
                ->pause(1000)
                ->click('.js-remove-row')
                ->pause(1000)
                ->click('#js-turf-plus')
                ->pause(3000)
                ->select('@turf-select', 1)
                ->type('@turf-number4', 7)
                ->click('#js-calc')
                ->pause(5000)
                ->type('@delivery', $previous_date->format('Y-m-d'))
                ->assertDialogOpened('到着希望日が最短お届け日時よりも短くなっています。到着希望日を選択し直してください。')
                ->acceptDialog()
                ->assertInputValue('@delivery', '')
                ->value('@delivery', $arrival_date->format('Y-m-d'))
                ->value('@delivery2', $arrival2_date->format('Y-m-d'))
                ->pause(1000)
                ->click('@post')
                ->assertSee($arrival_date->format('Y年m月d日'))
                ->assertSee($arrival2_date->format('Y年m月d日'))
                ->click('@commit')
                // ->assertPathIs('/');
                ->assertSee('発注が確定されました。');
        });
    }
    //人工芝11反〜7営業日
    public function testRegistrationOrderStatus1()
    {
        $this->browse(function (Browser $browser) {
            $transaction = new Transaction;
            $arrival_date = $transaction->getDeliveryDate(7);
            $previous_date = $transaction->getDeliveryDate(7)->subDay();
            $arrival2_date = $transaction->getDeliveryDate(7)->addDay();
            $arrival3_date = $transaction->getDeliveryDate(7)->addDay(2);
            $browser->loginAs(User::find(2))
                ->visit('/transactions/create/order/112')
                ->pause(7000)
                ->radio('t[address_type]', '1')
                ->click('.js-remove-row')
                ->pause(1000)
                ->click('.js-remove-row')
                ->pause(1000)
                ->click('.js-remove-row')
                ->pause(1000)
                ->click('#js-turf-plus')
                ->pause(3000)
                ->select('@turf-select', 1)
                ->type('@turf-number4', 13)
                ->type('@delivery', $previous_date->format('Y-m-d'))
                ->assertDialogOpened('到着希望日が最短お届け日時よりも短くなっています。到着希望日を選択し直してください。')
                ->acceptDialog()
                ->assertInputValue('@delivery', '')
                ->value('@delivery', $arrival_date->format('Y-m-d'))
                ->value('@delivery2', $arrival2_date->format('Y-m-d'))
                ->value('@delivery3', $arrival3_date->format('Y-m-d'))
                ->click('#js-calc')
                ->click('@post')
                ->assertSee($arrival_date->format('Y年m月d日'))
                ->assertSee($arrival2_date->format('Y年m月d日'))
                ->assertSee($arrival3_date->format('Y年m月d日'))
                ->click('@commit')
                ->assertPathIs('/');
            // ->assertSee('発注が確定されました。');
        });
    }
    //切売り100㎡未満7営業日
    public function testRegistrationOrderCutStatus2()
    {
        $this->browse(function (Browser $browser) {
            $transaction = new Transaction;
            $arrival_date = $transaction->getDeliveryDate(7);
            $previous_date = $transaction->getDeliveryDate(7)->subDay();
            $browser->loginAs(User::find(2))
                ->visit('/transactions/create/order/113')
                ->pause(4000)
                ->radio('t[address_type]', '1')
                ->click('.js-remove-row')
                ->pause(1000)
                ->select('@cut-turf-select', 1)
                ->select('@cut-menu-select', 50)
                ->type('vertical', 10)
                ->pause(1000)
                ->type('horizontal', 2)
                ->pause(1000)
                ->select('@sub-select', 12)
                ->type('@sub-number', 5)
                ->type('@delivery', $previous_date->format('Y-m-d'))
                ->assertDialogOpened('到着希望日が最短お届け日時よりも短くなっています。到着希望日を選択し直してください。')
                ->acceptDialog()
                ->assertInputValue('@delivery', '')
                ->value('@delivery', $arrival_date->format('Y-m-d'))
                ->click('#js-calc')
                ->click('@post')
                ->assertSee($arrival_date->format('Y年m月d日'))
                ->click('@commit')
                ->assertPathIs('/');
            // ->assertSee('発注が確定されました。');
        });
    }
    // 人工芝200㎡〜発注テスト最短15営業日
    public function testMaterialOrderStatus3()
    {
        $this->browse(function (Browser $browser) {
            $transaction = new Transaction;
            $arrival_date = $transaction->getDeliveryDate(15);
            $previous_date = $transaction->getDeliveryDate(15)->subDay();
            $browser->loginAs(User::find(2))
                ->visit('/transactions/create/order')
                ->pause(5000)
                ->radio('t[address_type]', 3)
                ->select('@turf-select', 1)
                ->type('@turf-number1', 11)
                ->pause(1000)
                ->select('@cut-turf-select', 1)
                ->select('@cut-menu-select', 50)
                ->type('vertical', 10)
                ->pause(1000)
                ->type('horizontal', 1)
                ->pause(1000)
                ->select('@sub-select', 12)
                ->type('@sub-number', 5)
                ->driver->executeScript('window.scrollTo(0, 3000);');
            $browser->value('@delivery', $previous_date->format('Y-m-d'))
                ->click('@delivery')
                ->screenshot('397')
                ->radio('t[address_type]', 1)
                ->pause(1000)
                ->assertDialogOpened('到着希望日が最短お届け日時よりも短くなっています。到着希望日を選択し直してください。')
                ->acceptDialog()
                ->assertInputValue('@delivery', '')
                ->value('@delivery', $arrival_date->format('Y-m-d'))
                ->click('#js-calc')
                ->click('@post')
                ->assertSee($arrival_date->format('Y年m月d日'))
                ->click('@commit')
                ->assertPathIs('/');
            // ->assertSee('発注が確定されました。');
        });
    }
    //切売り100㎡以上10営業日
    public function testRegistrationOrderCutStatus1()
    {
        $this->browse(function (Browser $browser) {
            $transaction = new Transaction;
            $arrival_date = $transaction->getDeliveryDate(10);
            $previous_date = $transaction->getDeliveryDate(10)->subDay();
            $browser->loginAs(User::find(2))
                ->visit('/transactions/create/order/114')
                ->pause(5000)
                ->radio('t[address_type]', 1)
                ->click('.js-remove-row')
                ->pause(1000)
                ->select('@cut-turf-select', 1)
                ->select('@cut-menu-select', 50)
                ->type('vertical', 10)
                ->pause(1000)
                ->type('horizontal', 2)
                ->pause(1000)
                ->click('#js-cut-turf-plus')
                ->pause(2000)
                ->select('product-select-6', 1)
                ->pause(1000)
                ->select('product-select-7', 51)
                ->type('#vertical-6', 10)
                ->pause(1000)
                ->type('#horizontal-6', 2)
                ->pause(1000)
                ->click('#js-cut-turf-plus')
                ->pause(2000)
                ->select('product-select-9', 1)
                ->select('product-select-10', 50)
                ->type('#vertical-9', 10)
                ->pause(1000)
                ->type('#horizontal-9', 2)
                ->pause(1000)
                ->click('#js-cut-turf-plus')
                ->pause(2000)
                ->select('product-select-12', 1)
                ->select('product-select-13', 50)
                ->type('#vertical-12', 10)
                ->pause(1000)
                ->type('#horizontal-12', 2)
                ->pause(1000)
                ->pause(1000)
                ->click('#js-cut-turf-plus')
                ->pause(2000)
                ->select('product-select-15', 1)
                ->select('product-select-16', 50)
                ->type('#vertical-15', 10)
                ->pause(1000)
                ->type('#horizontal-15', 2)
                ->pause(1000)
                ->click('#js-cut-turf-plus')
                ->pause(2000)
                ->select('product-select-18', 1)
                ->select('product-select-19', 50)
                ->type('#vertical-18', 10)
                ->pause(1000)
                ->type('#horizontal-18', 2)
                ->select('@sub-select', 12)
                ->type('@sub-number', 5)
                ->type('@delivery', $previous_date->format('Y-m-d'))
                ->assertDialogOpened('到着希望日が最短お届け日時よりも短くなっています。到着希望日を選択し直してください。')
                ->acceptDialog()
                ->assertInputValue('@delivery', '')
                ->value('@delivery', $arrival_date->format('Y-m-d'))
                ->click('#js-calc')
                ->pause(2000)
                ->click('@post')
                ->assertSee($arrival_date->format('Y年m月d日'))
                ->click('@commit')
                ->assertPathIs('/');
            // ->assertSee('発注が確定されました。');
        });
    }
    // 資材発注
    // 人工芝〜200㎡発注テスト最短10営業日
    public function testMaterialOrderStatus1()
    {
        $this->browse(function (Browser $browser) {
            $transaction = new Transaction;
            $arrival_date = $transaction->getDeliveryDate(10);
            $previous_date = $transaction->getDeliveryDate(10)->subDay();
            $browser->loginAs(User::find(2))
                ->visit('/transactions/create/order')
                ->pause(5000)
                ->select('@turf-select', 1)
                ->type('@turf-number1', 1)
                ->pause(1000)
                ->select('@cut-turf-select', 1)
                ->select('@cut-menu-select', 50)
                ->type('vertical', 10)
                ->type('horizontal', 1)
                ->select('@sub-select', 12)
                ->type('@sub-number', 5)
                ->pause(2000)
                ->value('@delivery', $previous_date->format('Y-m-d'))
                ->radio('t[address_type]', '1')
                ->pause(1000)
                ->assertDialogOpened('到着希望日が最短お届け日時よりも短くなっています。到着希望日を選択し直してください。')
                ->acceptDialog()
                ->assertInputValue('@delivery', '')
                ->value('@delivery', $arrival_date->format('Y-m-d'))
                ->click('#js-calc')
                ->click('@post')
                ->click('@commit')
                ->assertPathIs('/');
            // ->assertSee('発注が確定されました。');
        });
    }

    public function testUpdate()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->maximize()
                ->visit('/transactions/edit/1')
                ->pause(5000)
                ->assertRadioSelected('t[address_type]', '1')
                ->type('@memo', 'CIアップデートMEMO')
                ->value('.dusk-unit-price-1', 123456)
                ->clear('#horizontal-5')
                ->type('#horizontal-5', '1.1')
                ->value('#vertical-5', 0)
                ->type('#vertical-5', '5.5')
                ->type('@other-name', 'CIアップデート')
                ->type('@other-count', 3)
                ->type('@other-unit', 'CIアップデートテスト単位')
                ->type('@other-price', 10000)
                ->click('#js-calc')
                ->click('@post')
                ->pause(3000)
                ->assertSee('CIアップデート')
                ->assertSee('123,456円')
                ->assertSee('1.1')
                ->assertSee('5.5')
                ->assertSee('6.1');
        });
    }

    public function testDispatch()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->maximize()
                ->visit('/transactions/dispatch/pending')
                ->pause(4000)
                ->click('@detail-button')
                ->pause(1000)
                ->back()
                ->pause(4000)
                ->click('@show-detail')
                ->click('@input-cost')
                ->pause(1000)
                ->type('@create-cost', '1111')
                ->select('@create-shipping-company', 4)
                ->type('@create-shipping-date', '2020-10-10')
                ->type('@create-number', '1111111,111111,1111')
                ->type('@create-message', 'かえるのーうーたーがーきーこーえーてーくるーよー')
                ->click('@create-cost-submit')
                ->assertSee('FCに部材発注連絡を行いました');
        });
    }

    // テスト発注しないボタン
    public function testSkip()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->maximize()
                ->visit('/transactions/create/order/58')
                ->click('@skip')
                ->pause(4000)
                ->acceptDialog()
                ->assertSee('発注をスキップしました')
                ->assertSee('発注スキップテスト');
        });
    }

    public function testShippingUpdate()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->maximize()
                ->visit('/transactions/7')
                ->click('@shipping-edit')
                ->pause(4000)
                ->type('@edit-number', '123456789')
                ->click('@edit-cost-submit')
                ->visit('/transactions/7')
                ->click('@shipping-info')
                ->pause(4000)
                ->assertSee('123456789');
        });
    }

    public function testDestroy()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->maximize()
                ->visit('/transactions')
                ->click('@delete')
                ->acceptDialog()
                ->assertSee('発注書を削除しました');
        });
    }

    // その他商品しかない時の不具合テスト（NaN)
    public function testOtherEdit()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->maximize()
                ->visit('/transactions/edit/8')
                ->assertDontSee('NaN');
        });
    }

    public function testPrepaidCreate()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->maximize()
                ->visit('/transactions/create/order')
                ->pause(5000)
                ->radio('t[address_type]', '1')
                ->type('@memo', '備考欄')
                ->click('@prepaid2')
                ->select('@turf-select', 1)
                ->type('@turf-number1', 1)
                ->pause(1000)
                ->type('t[consignee]', '全額前金テスト')
                ->select('@cut-turf-select', 1)
                ->select('@cut-menu-select', 50)
                ->type('vertical', 10)
                ->pause(1000)
                ->type('horizontal', 1)
                ->pause(1000)
                // 切り売り人工芝に紐づくカットメニュー
                ->select('.js-cut-select', 1)
                ->pause(1000)
                ->type('.js-cut-length', 5)
                ->pause(1000)
                ->select('@sub-select', 12)
                ->type('@sub-number', 5)
                ->value('@delivery', '2022-02-02')
                ->click('#js-calc')
                ->click('@post')
                ->pause(10000)
                ->assertSee('この発注は全額前金での支払いになります');
        });
    }

    public function testCancelTransaction()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->visit('/transactions/15')
                ->click('@cancel-transaction')
                ->pause(2000)
                ->assertSee('発注をキャンセルしました');
        });
    }

    public function testAutoReceiveAddressInput()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                // 先に任意受け取り場所の設定
                ->visit('/users/edit/2')
                ->pause(5000)
                ->type('@optional-zipcode', '1000005')
                ->pause(500)
                ->type('@optional-tel', '080-3333-3333')
                ->type('@optional-staff', '任意受け取り人')
                ->click('@submit')
                ->visit('/transactions/create/order/130')
                ->maximize()
                ->radio('t[address_type]', '1')
                ->pause(500)
                ->assertInputValue('@receive', '〒1000002 サンプル県西区サンプル町')
                ->assertInputValue('@receive-name', 'FCかえる株式会社 テストスタッフ様')
                ->assertInputValue('@tel', '08000000002')
                ->radio('t[address_type]', '3')
                ->pause(500)
                ->assertInputValue('@receive', '〒1000003 サンプル県東区サンプル倉庫3-5')
                ->assertInputValue('@receive-name', 'FCかえる株式会社 テストスタッフ様')
                ->assertInputValue('@tel', '08000000002')
                ->radio('t[address_type]', '2')
                ->pause(500)
                ->assertInputValue('@receive', '〒1000001 サンプル都資材置き場番地住所')
                ->assertInputValue('@receive-name', 'FCかえる株式会社 テストスタッフ様')
                ->radio('t[address_type]', '5')
                ->pause(500)
                ->assertInputValue('@receive', '〒1000005 サンプル都サンプル中央区サンプル台')
                ->assertInputValue('@receive-name', 'FCかえる株式会社 任意受け取り人様')
                ->assertInputValue('@tel', '080-3333-3333')
                ->radio('t[address_type]', '4')
                ->pause(500)
                ->assertInputValue('@receive', '〒1000004 サンプル県南区サンプル浜')
                ->assertInputValue('@receive-name', '株式会社自動テスト 自動入力担当者テスト 様')
                ->assertInputValue('@tel', '09011111111')
                ->with('.js-turf-row-1', function ($tr) {
                    $tr->select('.js-turf-select', 1)
                        ->type('.js-product-count', 1);
                })
                ->select('@cut-turf-select', 1)
                ->select('@cut-menu-select', 50)
                ->type('vertical', 10)
                ->pause(1000)
                ->type('horizontal', 1)
                ->pause(1000)
                // 切り売り人工芝に紐づくカットメニュー
                ->select('.js-cut-select', 1)
                ->pause(1000)
                ->type('.js-cut-length', 5)
                ->with('#sub-body', function ($tr) {
                    $tr->click('.js-remove-row');
                })
                ->click('@delivery')
                ->pause(3000)
                ->value('@delivery', '2032-10-01')
                ->click('#js-calc')
                ->click('@post')
                ->assertSee('この資材は直接顧客に発送されます');
        });
    }
    // 資材発注大口割引テスト最短20営業日
    public function testMaterialOrderDiscount()
    {
        $this->browse(function (Browser $browser) {
            $transaction = new Transaction;
            $arrival_date = $transaction->getDeliveryDate(20);
            $previous_date = $transaction->getDeliveryDate(20)->subDay();
            $browser->loginAs(User::find(2))
                ->visit('/transactions/create/order')
                ->pause(5000)
                ->click('.js-remove-row')
                ->pause(1000)
                ->click('.js-remove-row')
                ->pause(1000)
                ->click('.js-remove-row')
                ->pause(1000)
                ->click('#js-turf-plus')
                ->pause(3000)
                ->select('@turf-select', 1)
                ->type('@turf-number4', 25)
                ->click('#js-etc-plus')
                ->pause(1000)
                ->type('.js-other-product-name', 'チャーター')
                ->type('@other-count', 1)
                ->type('@other-unit', 1)
                ->type('@other-price', 1)
                ->radio('t[address_type]', '1')
                ->click('#prepaid1')
                ->value('@delivery', $arrival_date->format('Y-m-d'))
                ->click('#js-calc')
                ->pause(1000)
                ->click('@post')
                ->assertSee($arrival_date->format('Y年m月d日'))
                ->click('@commit')
                // ->assertPathIs('/');
                ->assertSee('発注が確定されました。');
        });
    }

    // 副資材まとめ限定最短4営業日
    public function testMaterialOrderSubOnly()
    {
        $this->browse(function (Browser $browser) {
            $transaction = new Transaction;
            $arrival_date = $transaction->getDeliveryDate(4);
            $previous_date = $transaction->getDeliveryDate(4)->subDay();
            $browser->loginAs(User::find(2))
                ->visit('/transactions/create/order')
                ->pause(5000)
                ->click('.js-remove-row')
                ->pause(1000)
                ->click('.js-remove-row')
                ->pause(1000)
                ->select('@sub-select', 12)
                ->type('@sub-number', 5)
                ->pause(2000)
                ->value('@delivery', $previous_date->format('Y-m-d'))
                ->radio('t[address_type]', '1')
                ->pause(1000)
                ->assertDialogOpened('到着希望日が最短お届け日時よりも短くなっています。到着希望日を選択し直してください。')
                ->acceptDialog()
                ->assertInputValue('@delivery', '')
                ->value('@delivery', $arrival_date->format('Y-m-d'))
                ->click('#js-calc')
                ->click('@post')
                ->assertSee($arrival_date->format('Y年m月d日'))
                ->click('@commit')
                ->assertSee('発注が確定されました。');
            // ->assertPathIs('/');
        });
    }
    public function testAdminDispatchedList()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/transactions/admin/dispatched')
                ->assertSee('発注書請一覧')
                ->assertSeeLink('18')
                ->assertSeeLink('21');
        });
    }

    //追加発注テスト最短7営業日
    public function testSubTransaction()
    {
        $this->browse(function (Browser $browser) {
            $transaction = new Transaction;
            $arrival_date = $transaction->getDeliveryDate(7);
            $previous_date = $transaction->getDeliveryDate(7)->subDay();
            // 追加発注書作成
            $browser->loginAs(User::find(2))
                ->visit('report/pending')
                ->assertSee('完了報告待ち案件一覧')
                ->assertSeeLink('124')
                ->click('@contact124')
                ->pause(1000)
                ->assertSee('発注書作成')
                ->assertSee('追加発注分の発注書です。')
                ->radio('t[address_type]', 1)
                ->select('@turf-select', 1)
                ->type('@turf-number1', 1)
                ->pause(1000)
                ->select('@cut-turf-select', 1)
                ->select('@cut-menu-select', 50)
                ->type('vertical', 10)
                ->pause(1000)
                ->type('horizontal', 1)
                ->pause(1000)
                ->select('@sub-select', 12)
                ->type('@sub-number', 5)
                ->type('@delivery', $previous_date->format('Y-m-d'))
                ->assertDialogOpened('到着希望日が最短お届け日時よりも短くなっています。到着希望日を選択し直してください。')
                ->acceptDialog()
                ->assertInputValue('@delivery', '')
                ->value('@delivery', $arrival_date->format('Y-m-d'))
                ->click('#js-calc')
                ->click('@post')
                ->assertSee($arrival_date->format('Y年m月d日'))
                ->click('@commit')
                ->assertPathIs('/')
                // 作成できないかをチェック
                ->pause(1000)
                ->visit('report/pending')
                ->assertSee('完了報告待ち案件一覧')
                ->assertSeeLink('125')
                ->click('@contact125')
                ->pause(3000)
                ->assertSee('追加発注出来る上限に達しました。');
            $browser->loginAs(User::find(1))
                ->visit('transactions/dispatch/pending')
                // ->assertSeeLink('124')
                ->assertSeeLink('33');
        });
    }

    public function testSqubeTransrateTest()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/transactions/create/order/127')
                ->click('#convert-quotation')
                ->pause(18000)
                ->assertSelected('@sub-select', 75);
        });
    }

    // 40mmオンリーを
    public function testTransaction40mmPremium()
    {
        $this->browse(function (Browser $browser) {
            $transaction = new Transaction;
            $arrival_date = $transaction->getDeliveryDate(10);
            $previous_date = $transaction->getDeliveryDate(10)->subDay();
            // 追加発注書作成
            $browser->loginAs(User::find(2))
                ->visit('/transactions/create/order')
                ->pause(5000)
                ->select('@turf-select', 67)
                ->type('@turf-number1', 1)
                ->select('@cut-turf-select', 67)
                ->select('@cut-menu-select', 50)
                ->type('vertical', 10)
                ->pause(1000)
                ->type('horizontal', 1)
                ->select('@sub-select', 12)
                ->type('@sub-number', 1)
                ->pause(1000)
                ->value('@delivery', $previous_date->format('Y-m-d'))
                ->radio('t[address_type]', '1')
                ->pause(1000)
                ->assertDialogOpened('到着希望日が最短お届け日時よりも短くなっています。到着希望日を選択し直してください。')
                ->acceptDialog()
                ->assertInputValue('@delivery', '')
                ->value('@delivery', $arrival_date->format('Y-m-d'))
                ->click('#js-calc')
                ->click('@post')
                ->click('@commit')
                ->pause(6000)
                ->assertSee('発注が確定されました。');
        });
    }

    public function testTransaction40mm()
    {
        $this->browse(function (Browser $browser) {
            $transaction = new Transaction;
            $arrival_date = $transaction->getDeliveryDate(10);
            $previous_date = $transaction->getDeliveryDate(10)->subDay();
            // 追加発注書作成
            $browser->loginAs(User::find(2))
                ->visit('/transactions/create/order')
                ->pause(5000)
                ->select('@turf-select', 2)
                ->type('@turf-number1', 1)
                ->pause(1000)
                ->select('@cut-turf-select', 1)
                ->select('@cut-menu-select', 50)
                ->assertDontSee('カット追加')
                ->type('@horizontal', 1)
                ->type('@vertical', 10)
                ->pause(1000)
                ->select('@sub-select', 12)
                ->type('@sub-number', 1)
                ->pause(1000)
                ->value('@delivery', $previous_date->format('Y-m-d'))
                ->radio('t[address_type]', '1')
                ->pause(1000)
                ->assertDialogOpened('到着希望日が最短お届け日時よりも短くなっています。到着希望日を選択し直してください。')
                ->acceptDialog()
                ->assertInputValue('@delivery', '')
                ->value('@delivery', $arrival_date->format('Y-m-d'))
                ->click('#js-calc')
                ->click('@post')
                ->assertSee($arrival_date->format('Y年m月d日'))
                ->click('@commit')
                ->assertSee('発注が確定されました。');
        });
    }

    // 顧客への直接発送の追加発注を行っても本部のやることリストに出ないことを確認するテスト
    public function testAddDirectTransactionDontSeeAdmin()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('transactions/dispatch/pending')
                ->assertDontSeeLink('発注書No.42');
        });
    }
    // 発注書作成GOLF
    public function testCreateGolf()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->maximize()
                ->visit('/transactions/create/order/138')
                ->pause(5000)
                ->radio('t[address_type]', '1')
                ->type('@memo', '備考欄')
                ->assertPathIs('/transactions/create/order/138')
                ->assertSeeLink('見積書を確認')
                ->select('@turf-select', 1)
                ->pause(1000)
                ->type('@turf-number1', 50)
                ->select('@cut-turf-select', 5)
                ->pause(1000)
                ->type('vertical', 10)
                ->screenShot('cut-golf')
                ->select('@cut-menu-select', 51)
                ->click('@add-cut-turf')
                ->pause(1000)
                ->select('@cut-menu-select-2', 56)
                ->type('@cut-menu-num-2', 10)
                ->pause(1000)
                ->type('horizontal', 1)
                ->pause(1000)
                ->screenShot('cut-golf2')
                // 切り売り人工芝に紐づくカットメニュー
                ->select('.js-cut-select', 1)
                ->pause(1000)
                ->type('.js-cut-length', 5)
                ->pause(1000)
                ->click('#js-sales-plus')
                ->pause(1000)
                ->click('@sales-select')
                ->mouseover('@product31')
                ->pause(3000)
                ->click('@product31')
                ->type('@sales-number', 5)
                ->select('@sub-select', 12)
                ->pause(1000)
                ->type('@sub-number', 5)
                ->click('#js-etc-plus')
                ->pause(1000)
                ->type('@other-name', 'テスト手動入力')
                ->type('@other-price', 3333)
                ->type('@other-unit', '本')
                ->type('@other-count', 3)
                ->pause(1000)
                ->screenShot('cut-golf3')
                ->click('#js-calc')
                ->value('@delivery', '2028-01-01')
                ->value('@delivery2', '2028-01-02')
                ->value('@delivery3', '2028-01-03')
                ->click('@post')
                ->click('@commit')
                ->pause(3000)
                ->assertPathIs('/');
            // ->assertSee('発注が確定されました。');
        });
    }
    // 発注書作成GOLF以外の場合はカットメニューに特定のものがでない
    public function testCreateNotGolf()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                ->maximize()
                ->visit('/transactions/create/order/139')
                ->pause(5000)
                ->radio('t[address_type]', '1')
                ->type('@memo', '備考欄')
                ->assertPathIs('/transactions/create/order/139')
                ->assertSeeLink('見積書を確認')
                ->select('@turf-select', 1)
                ->pause(1000)
                ->type('@turf-number1', 50)
                ->select('@cut-turf-select', 1)
                ->select('@cut-menu-select', 50)
                ->type('vertical', 10)
                ->pause(1000)
                ->type('horizontal', 1)
                ->pause(1000)
                // 切り売り人工芝に紐づくカットメニュー
                ->select('.js-cut-select', 1)
                ->pause(1000)
                ->type('.js-cut-length', 5)
                ->pause(1000)
                ->click('#js-sales-plus')
                ->pause(1000)
                ->click('@sales-select')
                ->mouseover('@product31')
                ->pause(3000)
                ->click('@product31')
                ->type('@sales-number', 5)
                ->pause(1000)
                ->type('@sub-number', 5)
                ->click('#js-etc-plus')
                ->pause(1000)
                ->type('@other-name', 'テスト手動入力')
                ->type('@other-price', 3333)
                ->type('@other-unit', '本')
                ->type('@other-count', 3)
                ->pause(1000)
                ->value('@delivery', '2028-01-01')
                ->screenShot('Notcut-golf')
                ->assertSee('商品を選択してください');
        });
    }

    /*
    public function testTransactionDispatchedOrderBy()
    {
        $this->browse(function (Browser $browser) {
            // 発注請書ソート確認 発送日　降順
            $browser->loginAs(User::find(1))
            ->visit('/transactions/admin/dispatched')
            ->with('@row0', function ($tr) {
                $tr->assertSeeLink(23);
            })
            ->with('@row1', function ($tr) {
                $tr->assertSeeLink(36);
            })
            // 発注請書ソート確認 発送日　昇順
            ->select('@order-select', 'ascendingDeliveryDate')
            ->with('@row0', function ($tr) {
                $tr->assertSeeLink(5);
            })
            ->with('@row1', function ($tr) {
                $tr->assertSeeLink(35);
            })
            // 発注請書ソート確認 納品希望日　降順
            ->select('@order-select', 'descendingDeliveryPreferredDate')
            ->with('@row0', function ($tr) {
                $tr->assertSeeLink(38);
            })
            ->with('@row1', function ($tr) {
                $tr->assertSeeLink(5);
            })
            // 発注請書ソート確認 納品希望日　昇順
            ->select('@order-select', 'ascendingDeliveryPreferredDate')
            ->with('@row0', function ($tr) {
                $tr->assertSeeLink(39);
            })
            ->with('@row1', function ($tr) {
                $tr->assertSeeLink(5);
            });
        });
    }
    public function testTransactionPurchaseOrderOrderBy()
    {
        $this->browse(function (Browser $browser) {
            // 発注請書ソート確認 発送日　降順
            $browser->loginAs(User::find(1))
            ->visit('/transactions')
            ->with('@row0', function ($tr) {
                $tr->assertSeeLink(10);
            })
            ->with('@row1', function ($tr) {
                $tr->assertSeeLink(11);
            })
            // 発注請書ソート確認 発送日　昇順
            ->select('@order-select', 'ascendingOrderTime')
            ->pause(2000)
            ->with('@row0', function ($tr) {
                $tr->assertSeeLink(40);
            })
            ->with('@row1', function ($tr) {
                $tr->assertSeeLink(41);
            })
            // 発注請書ソート確認 納品希望日　降順
            ->select('@order-select', 'descendingDeliveryPreferredDate')
            ->pause(2000)
            ->with('@row0', function ($tr) {
                $tr->assertSeeLink(46);
            })
            ->with('@row1', function ($tr) {
                $tr->assertSeeLink(40);
            })
            // 発注請書ソート確認 納品希望日　昇順
            ->select('@order-select', 'ascendingDeliveryPreferredDate')
            ->pause(2000)
            ->with('@row0', function ($tr) {
                $tr->assertSeeLink(47);
            })
            ->with('@row1', function ($tr) {
                $tr->assertSeeLink(48);
            });
        });
    }
    */
}
