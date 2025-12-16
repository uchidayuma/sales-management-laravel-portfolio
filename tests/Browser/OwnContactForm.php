<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class OwnContactForm extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testOwnContactForm()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(2))
                //自己獲得案件登録
                ->visit('/contact/fc/new')
                ->assertSee('自己獲得案件登録')
                ->select('c[contact_type_id]', 1)
                ->type('c[free_sample]','請求済み')
                ->type('c[company_name]','DuskTest株式会社')
                ->type('c[company_ruby]','だすくてすとかぶしきがいしゃ')
                ->type('c[surname]','サンプル')
                ->type('c[name]','花子')
                ->type('c[industry]','IT')
                ->type('c[zipcode]',1000003)
                ->type('c[pref]','サンプル県')
                ->type('c[city]','サンプル市')
                ->type('c[street]','サンプルタウン1-1-1')
                ->type('c[tel]','09022224444')
                ->type('c[fax]','08633428454')
                ->type('c[email]','test@example.com')
                ->type('c[age]','1980年代')
                ->type('c[quote_details]','問い合わせ内容')
                ->type('c[ground_condition]','土')
                ->type('c[vertical_size]',10)
                ->type('c[horizontal_size]',20)
                ->type('c[desired_product]','人工芝B')
                ->type('c[visit_address]','サンプル県サンプル市中央1-1-1')
                ->type('c[use_application]','花壇の整備')
                ->type('c[where_find]','インターネット')
                ->type('c[sns]','Twitter')
                ->type('c[comment]','コメント')
                ->type('c[requirement]', '必要事項')
                ->press('登録')
                ->pause(2000)
                ->assertPathIs('/contact/assigned/list')
                ->assertSee('新しいお問い合わせを登録しました。');
        });
    }
}
