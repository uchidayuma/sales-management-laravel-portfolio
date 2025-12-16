@component('mail::message')
@include('emails.return-message-fc')
<h1>FC本部から見積もり依頼が届きました。</h1>
<p>案件内容を確認の上、ご対応をお願いいたします。</p>

<ul>
<li>案件No：<a href= {{route('contact.show', ['id' => $contact['id']])}} >{{ $contact['id'] }}</a></li>
<li>案件種別：{{ returnContactType($contact['contact_type_id']) }}</li>
<li>顧客名：{{ isCompany($contact) ? $contact->company_name. ' ' .$contact->surname : $contact->surname.$contact->name }}</li>
<li>顧客住所：〒{{$contact->zipcode}} {{$contact['pref'].$contact->city.$contact->street}} </li>
</ul>

@component('mail::button', ['url' => $contact['contact_type_id'] == 3 || $contact['contact_type_id'] == 7 ? route('contact.show', ['id' => $contact['id']]) : route('quotations.needs')] )
担当案件を確認する
@endcomponent

@endcomponent
