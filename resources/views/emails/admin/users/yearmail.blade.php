@component('mail::message')
<h4>{{ !is_null($fc['company_name']) ? $fc['company_name'] : $fc['name']}} {{$fc['staff']}}様</h4>
<p>お世話になっております。【サンプルFC】本部 サンプル株式会社です。</p>
<p>{{$now_date}}をもちまして、ご加盟頂き一年が経過致しました。</p>
<p>日頃よりご尽力いただきまして誠にありがとうございます。</p>
<p>{{$next_month}}分より、毎月ブランド使用料33,000円（税込）をご請求させて頂きますのでご査収くださいますよう、よろしくお願い致します。</p>

@include('emails.return-message-fc')
@endcomponent
