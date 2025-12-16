<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleRead;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mail;
use App\Mail\ArticleNoticeMail;

class ArticleController extends MyController
{
    public function __construct(Article $article)
    {
        parent::__construct();
        $this->model = $article;
        $this->breadcrumbs->addCrumb('<i class="fas fa-hourglass"></i>お知らせ', 'articles');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->breadcrumbs->addCrumb('お知らせ記事一覧');
        $breadcrumbs = $this->breadcrumbs;

        $articles = $this->model->where('status', 1)->where('published_at', '<=', Carbon::now())->orderBy('published_at', 'DESC')->paginate(30);
        $privateArticles = $this->model->where('status', 1)->where('published_at', '>', Carbon::now())->orderBy('published_at', 'DESC')->get();
        $draftArticles = $this->model->where('status', 0)->orderBy('created_at', 'DESC')->get();

        return view('share.article.index', compact('breadcrumbs', 'articles', 'privateArticles', 'draftArticles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.article.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $post = $request->all();

        $validatedData = $request->validate([
          'a.title' => 'required|max:255',
          'trumbowyg' => 'required',
        ]);

        $post['a']['body'] = $post['trumbowyg'];
        if ($post['a']['status'] == '0') { // If it's a draft
            $post['a']['published_at'] = null;
        } elseif ($post['a']['status'] != '0' && $post['publish_date'] && $post['publish_time']) {
            $post['a']['published_at'] = $post['publish_date'].' '.sprintf('%02d:00:00', $post['publish_time']);
        } elseif ($post['a']['status'] != '0' && empty($post['publish_date'])) {
            $post['a']['published_at'] = date('Y-m-d H:i:s');
        }

        $this->model->insert($post['a']);

        if ($post['a']['status'] == '1') {
            return redirect('/articles')->with('success', 'お知らせの作成が完了しました！');
        } else {
            return redirect('/articles')->with('info', 'お知らせの下書きが完了しました！');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->breadcrumbs->addCrumb('お知らせ記事');
        $breadcrumbs = $this->breadcrumbs;

        $user = \Auth::user();
        $read = ArticleRead::where('article_id', intval($id))->where('user_id', $user->id)->exists();

        if (isFc() && !$read) {
            ArticleRead::insert(['article_id' => $id, 'user_id' => $user->id]);
        }
        $readFcs = ArticleRead::where('article_id', $id)
            ->where('u.role', 2)->whereIn('u.status', [1,3,4])
            ->leftJoin('users AS u', 'u.id', '=', 'article_reads.user_id')
            ->orderBy('user_id', 'ASC')
            ->get();

        $noReadFcs = User::select('users.*', 'ar.*')
            ->where('users.role', 2)
            ->whereIn('users.status', [1,3,4])
            // 既読FCののみ排除 サブクエリ遣わないとむりぽ
            ->whereNotIn('users.id', function($query) use ($id){
                $query->select('article_reads.user_id')
                ->from('article_reads')
                ->where('article_id', $id)
                ->get();
            })
            ->leftJoin('article_reads AS ar', 'ar.user_id', '=', 'users.id')
            ->orderBy('users.id', 'ASC')
            ->groupBy('users.id')
            ->get();

        $article = $this->model->findOrFail($id);

        return view('share.article.detail', compact('breadcrumbs', 'id', 'article', 'readFcs', 'noReadFcs'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $article = $this->model->findById($id);

        return view('admin.article.create', compact('id', 'article'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $post = $request->all();

        $validatedData = $request->validate([
          'a.title' => 'required|max:255',
          'trumbowyg' => 'required',
        ]);

        $post['a']['body'] = $post['trumbowyg'];

        if ($post['publish_date'] && $post['publish_time']) {
            $post['a']['published_at'] = $post['publish_date'].' '.sprintf('%02d:00:00', $post['publish_time']);
        } elseif ($post['publish_date'] && empty($post['publish_time'])) {
            $post['a']['published_at'] = $post['publish_date'].' '.'12:00:00';
        } elseif (empty($post['publish_date']) && $post['publish_time']) {
            $post['a']['published_at'] = Carbon::today().' '.sprintf('%02d:00:00', $post['publish_time']);
        }

        $this->model->where('id', $id)->update($post['a']);

        return redirect('/articles')->with('success', 'お知らせの更新が完了しました！');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        adminOnly();
        $this->model->where('id', $id)->update(['status' => 2]);

        return redirect('/articles')->with('danger', 'お知らせを削除しました！');
    }

    public function ajaxImageUpload(Request $request)
    {
        $image = $request->file('image');
        $path = $image->store('/images/articles', 's3', 'public');

        return response()->json(['success' => true, 'url' => s3url().$path]);
    }

    public function ajaxUnreadGet()
    {
        $unreads = Article::where('ar.user_id')->get();
    }

    public function cronNotice()
    {
        $fcs = User::where('role', 2)->whereIn('status', [1, 3, 4])->where('allow_email', 1)->get();
        $currentPublishArticle = Article::whereRaw('published_at > (NOW() - INTERVAL 1 HOUR)')->whereRaw('published_at < NOW()')->where('status', 1)->get()->toArray();
        \Log::debug(print_r($currentPublishArticle, true));
        foreach($currentPublishArticle as $article){
            Mail::to(config('mail.fallback_notification', 'notifications@example.com'))->send(new ArticleNoticeMail($article));
            foreach($fcs as $f){
                sleep(1);
                Mail::to($f['email'])->send(new ArticleNoticeMail($article));
                if (!empty($f['email2'])) {
                    Mail::to($f['email2'])->send(new ArticleNoticeMail($article));
                }
                if (!empty($f['email3'])) {
                    Mail::to($f['email3'])->send(new ArticleNoticeMail($article));
                }
            }
        }
    }
}
