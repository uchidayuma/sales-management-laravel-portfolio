<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UserTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testUserCreate()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                    ->visit('/users/create')
                    ->assertSee('新規登録')
                    ->type('@name', 'FC-CI')
                    // ->check('@is_personal')
                    ->type('@company_name', 'FC-CI会社')
                    ->type('@company_ruby', 'FC-CIかいしゃ')
                    ->type('@zipcode', '1000001')
                    // 郵便番号から自動入力にタイムラグがあるため
                    ->pause(2000)
                    ->type('@street', 'サンプルビル1F')
                    ->type('@email', 'fcci@gmail.com')
                    ->type('@tel', '0120123456')
                    ->type('@fax', '0120-123-456')
                    ->type('@contract-date', '2022-02-22')
                    ->select('@fc-apply-area', 1)
                    ->select('@prefecture',13)
                    ->type('@staff', '蛙教祖')
                    ->type('@staff_ruby', 'かえるきょうそ')
                    ->type('@s_tel', '09060622048')
                    ->click('#add-staff2')
                    ->type('@staff2', '蛙教祖その2')
                    ->type('@staff2_ruby', 'かえるきょうその2')
                    ->type('@s2_tel', '050-3561-2247')
                    ->type('@s_zipcode', '1000001')
                    ->type('@s_street', 'サンプルビル1F')
                    ->type('@memo', '本部だけが見れるメモ')
                    ->radio('fc[invoice_payments_type]', '1')
                    ->type('@account_info1', '口座情報1')
                    ->click('#add-account-infomation2')
                    ->type('@account_info2', '口座情報2')
                    ->pause(1000)
                    ->click('@submit')
                    ->maximize()
                    ->screenshot('user')
                    ->assertPathIs('/users');
                    
                // DBに正常な値が入っているかの確認
                $this->assertDatabaseHas('users', 
                    [
                        'name' => e('FC-CI'),
                        'company_name' => e('FC-CI会社'),
                        'company_ruby' => e('FC-CIかいしゃ'),
                        'zipcode' => '1000001',
                        'street' => e('サンプルビル1F'),
                        'email' => 'fcci@gmail.com',
                        'tel' => '0120123456',
                        'fax' => '0120-123-456',
                        'prefecture_id' => '13',
                        'staff' => e('蛙教祖'),
                        'staff_ruby' => e('かえるきょうそ'),
                        'contract_date' => e('2022-02-22'),
                        'fc_apply_area_id' => '1',
                        's_tel' => '09060622048',
                        'staff2' => e('蛙教祖その2'),
                        'staff2_ruby' => e('かえるきょうその2'),
                        's2_tel' => '050-3561-2247',
                        's_zipcode' => '1000001',
                        's_street' => e('サンプルビル1F'),
                        'memo' => e('本部だけが見れるメモ'),
                        'invoice_payments_type' => '1',
                        'account_infomation1' => e('口座情報1'),
                        'account_infomation2' => e('口座情報2')
                    ]);
        });
        
    }

    public function testUserEdit()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                    ->maximize()
                    ->visit('/users/edit/2')
                    ->assertSee('FC情報編集')
                    ->type('@name', 'FC-CI-UPDATE')
                    ->click('#add-staff2')
                    ->type('@staff2', '蛙教祖その2')
                    ->type('@staff2_ruby', 'かえるきょうその2')
                    ->click('#add-staff3')
                    ->select('@prefecture', 1)
                    ->type('@staff3', 'スタッフその3')
                    ->type('@staff3_ruby', 'スタッフその3')
                    ->type('@s3_tel', '0120-000-000')
                    ->type('@memo', '更新メモ')
                    ->radio('fc[invoice_payments_type]', '2')
                    ->type('@quotation-memo', 'デフォルト見積もり書備考欄')
                    ->type('@account_info1', '更新口座情報1')
                    ->click('#add-account-infomation2')
                    ->type('@account_info2', '更新口座情報2')
                    ->type('@optional-zipcode', '1010051')
                    ->pause(3000)
                    ->type('@optional-tel', '0120-000-000')
                    ->pause(2000)
                    ->screenshot('user-updated--name')
                    ->click('@submit')
                    ->screenshot('user-updated')
                    ->visit('/users/2')
                    ->assertSee('FC-CI-UPDATE')
                    ->assertSee('蛙教祖その2')
                    ->assertSee('北海道')
                    ->assertSee('かえるきょうその2')
                    ->assertSee('スタッフその3')
                    ->assertSee('0120-000-000')
                    ->assertSee('更新口座情報1')
                    ->assertSee('更新口座情報2')
                    ->assertSee('ブランド使用料')
                    ->assertSee('1010051')
                    ->assertSee('サンプル都 千代田区神田神保町')
                    ->assertSee('0120-000-000')
                    ->assertSee('更新メモ');

                // DBに正常な値が入っているかの確認
                $this->assertDatabaseHas('users', 
                [
                    'name' => e('FC-CI-UPDATE'),
                    'prefecture_id' => '1',
                    'staff2' => e('蛙教祖その2'),
                    'staff2_ruby' => e('かえるきょうその2'),
                    'staff3' => e('スタッフその3'),
                    'staff3_ruby' => e('スタッフその3'),
                    's3_tel' => '0120-000-000',
                    'memo' => e('更新メモ'),
                    'quotation_memo' => e('デフォルト見積もり書備考欄'),
                    'account_infomation1' => e('更新口座情報1'),
                    'account_infomation2' => e('更新口座情報2')
                ]);
        });
    }

    public function testUserPasswordEdit()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                    ->visit('/users/edit/2')
                    ->click('@edit_pass')
                    ->pause(1000)
                    ->type('@now_pass', 'kaerusan')
                    ->type('@new_pass1', 'shibapass1234')
                    ->type('@new_pass2', 'shibapass1234')
                    ->click('@submit')
                    ->pause(1000)
                    ->screenshot('password-updated')
                    ->assertSee('パスワードを変更しました。');
        });
    }

    public function testDeleteUserLogin()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(3))
                    ->visit('/')
                    ->assertSee('404');
        });
    }

    public function testRequirePrepaid()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                    ->visit('/users/2')
                    ->assertSee('制限なし')
                    ->visit('/users/edit/2')
                    ->check('fc[require_prepaid]')
                    ->click('@submit')
                    ->assertSee('前金のみ');
            $browser->loginAs(User::find(2))
                    ->visit('/users/edit/2')
                    ->assertDontSee('発注制限')
                    ->visit('/transactions/create/order')
                    ->assertDisabled('#prepaid0')
                    ->assertDisabled('#prepaid1');
        });
    }
    // 適格事業者番号のテスト
    public function testQualifiedBusinessNumber()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                    ->visit('/users/edit/2')
                    // ->click('@edit_pass')
                    ->pause(1000)
                    ->type('@qualified-business-number', '１２３４５６７８９０１２３')
                    ->click('@submit')
                    ->pause(1000)
                    ->assertSee('1234567890123')
                    ->visit('/quotations/2')
                    ->assertSee('登録番号')
                    ->assertSee('1234567890123');
        });
    }
}
