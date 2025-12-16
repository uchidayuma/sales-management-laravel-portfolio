@component('mail::message')
@include('emails.return-message-fc')
  @switch($inputs['notification_type'])
    @case(1)
      訪問アポイントメント未対応案件があります。すぐに確認してください。
      @break
    @case(2)
      見積もり未作成案件があります。すぐに確認してください。
      @break
    @case(3)
      商談回答未対応案件があります。すぐに確認してください。
      @break
  @endswitch

  @component('mail::button', ['url' => config('app.url').$inputs['url'] ])
      未対応案件を確認する
  @endcomponent

@include('emails.return-message-fc')
@endcomponent
