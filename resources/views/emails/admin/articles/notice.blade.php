@component('mail::message')
<h1>FC加盟店　各位</h1>
<p>貴社ますますご盛栄のこととお喜び申し上げます。</p>
<p>平素は格別のお引立てを賜り、厚く御礼申し上げます。</p>
<p>{{date("Y年m月d日")}}、新しいお知らせのご案内がございます。</p>

<h2>{{ $article['title'] }}</h2>
<p>以下のリンクよりご確認くださいますようお願い申し上げます。 </p>

@component('mail::button', ['url' => route('articles.show', ['article' => $article['id']])] )
お知らせを確認する
@endcomponent

@endcomponent
