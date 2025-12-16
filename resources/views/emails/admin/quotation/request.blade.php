@component('mail::message')
@include('emails.return-message')
<h1>FC本部から見積もり依頼が届きました。</h1>
<p>案件内容を確認の上、ご対応をお願いいたします。</p>

{{-- <ul>
<li>案件No：{{$contact['id']}}</li>
<li>顧客名：{{ isCompany($contact) ? $contact->company_name. ' ' .$contact->surname : $contact->surname.$contact->name }}</li>
<li>顧客住所：〒{{$contact->zipcode}} {{$contact['pref'].$contact->city.$contact->street}} </li>
<li>顧客電話番号：{{$contact['tel']}}</li>
</ul> --}}

{{-- @component('mail::button', ['url' => route('assigned.list')] )
担当案件を確認する
@endcomponent --}}

@include('emails.return-message')
@endcomponent
