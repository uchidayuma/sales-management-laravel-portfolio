<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Article;

class ArticleTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function testCreate()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/articles/create')
                ->pause(5000)
                ->type('#title', 'testtitle')
                ->type('#trumbowyg', 'testbody')
                ->pause(2000)
                ->click('#js-submit')
                ->assertSee('お知らせの作成が完了しました！');
            $article = Article::where('status', 1)->orderBy('created_at', 'DESC')->first();
            $browser->visit('/articles/'.$article->id)
                ->assertSee('testtitle')
                ->assertSee('testbody');
        });
    }

    public function testUpdate()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/articles/edit/1')
                ->pause(5000)
                ->type('#title', 'テスト編集')
                ->type('#trumbowyg', 'テスト編集ボディ')
                ->pause(2000)
                ->click('#js-submit')
                ->visit('/articles/1')
                ->assertSee('テスト編集')
                ->assertSee('テスト編集ボディ');
        });
    }

    public function testDraft()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                ->visit('/articles/create')
                ->pause(5000)
                ->type('#title', 'テスト下書き')
                ->type('#trumbowyg', 'テスト下書きボディ')
                ->pause(2000)
                ->click('#draft-submit')
                ->assertSee('お知らせの下書きが完了しました！');
            $article = Article::where('status', 0)->orderBy('created_at', 'DESC')->first();
            $browser->visit('/articles/'.$article->id)
                ->assertSee('テスト下書き')
                ->assertSee('テスト下書きボディ');
        });
    }
}
