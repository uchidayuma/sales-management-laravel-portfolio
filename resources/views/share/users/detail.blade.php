@extends('layouts.layout')

@section('css')
<link href="{{ asset('styles/account/registration.min.css') }}" rel="stylesheet"> 
@endsection

@section('content')
{!! $breadcrumbs->render() !!}
<!--FC詳細ページ-->
<div class="fc-detail">
  <table class="common-table-stripes-column">
    <tbody>
      <tr>
        <th scope="row">No</th>
        <td>{{ $fc['id'] }}</td>
      </tr>
      <tr>
        <th scope="row">登録日</th>
        <td>{{ date('Y年m月d日', strtotime($fc['created_at'])) }}</td>
      </tr>
      <tr>
        <th>契約日</th>
        <td>{{ !is_null($fc['contract_date']) ? date('Y年m月d日', strtotime($fc['contract_date'])) : '未入力' }}</td>
      </tr>
      <tr>
        <th scope="row">FC名</th>
        <td>{{ $fc['name'] }}</td>
      </tr>
      <tr>
        <th scope="row">会社名</th>
        <td>{{ $fc['company_name'] }}</td>
      </tr>
      <tr>
        <th scope="row">会社名フリガナ</th>
        <td>{{ $fc['company_ruby'] }}</td>
      </tr>
      <tr>
        <th scope="row">所在都道府県</th>
        <td>{{ !empty($fc['prefecture_name']) ? $fc['prefecture_name'] : '未選択' }}</td>
      </tr>
      <tr>
        <th scope="row">住所</th>
        <td>〒{{ $fc['zipcode'] }} <br> {{ $fc['pref'] }} {{ $fc['city'] }}{{ $fc['street'] }}</td>
      </tr>
      <tr {{adminOnlyHidden()}}>
        <th scope="row">担当エリア</th>
        <td>{{ $fc['area_name'] . ' ' . $fc['area_content'] }}</td>
      </tr>
      <tr>
        <th scope="row">TEL</th>
        <td>{{ $fc['tel'] }}</td>
      </tr>
      <tr>
        <th scope="row">FAX</th>
        <td>{{ $fc['fax'] }}</td>
      </tr>
      <tr>
        <th scope="row">E-mail：{!! $fc['allow_email']=='1' ? '<i class="far fa-envelope mr5 color-link"></i>送信OK' : '<i class="far fa-envelope mr5 color-red"></i>送信NG' !!}</th>
        <td>{{ $fc['email'] }}</td>
      </tr>
  @if(!empty($fc['email2']))
      <tr>
        <th scope="row">E-mail:2（連絡用）</th>
        <td>{{ $fc['email2'] }}</td>
      </tr>
  @endif
  @if(!empty($fc['email3']))
      <tr>
        <th scope="row">E-mail:3（連絡用）</th>
        <td>{{ $fc['email3'] }}</td>
      </tr>
  @endif
      <tr>
        <th scope="row">担当者</th>
        <td>{{ $fc['staff'] }}</td>
      </tr>
      <tr>
        <th scope="row">担当者フリガナ</th>
        <td>{{ $fc['staff_ruby'] }}</td>
      </tr>
      <tr>
        <th scope="row">担当者携帯TEL</th>
        <td>{{ $fc['s_tel'] }}</td>
      </tr>
  @if(!empty($fc['staff2']))
      <tr>
        <th scope="row">第2担当者</th>
        <td>{{ $fc['staff2'] }}</td>
      </tr>
  @endif
  @if(!empty($fc['staff2_ruby']))
      <tr>
        <th scope="row">第2担当者フリガナ</th>
        <td>{{ $fc['staff2_ruby'] }}</td>
      </tr>
  @endif
  @if(!empty($fc['s2_tel']))
      <tr>
        <th scope="row">第2担当者携帯TEL</th>
        <td>{{ $fc['s2_tel'] }}</td>
      </tr>
  @endif
  @if(!empty($fc['staff3']))
      <tr>
        <th scope="row">第3担当者</th>
        <td>{{ $fc['staff3'] }}</td>
      </tr>
  @endif
  @if(!empty($fc['staff3_ruby']))
      <tr>
        <th scope="row">第3担当者フリガナ</th>
        <td>{{ $fc['staff3_ruby'] }}</td>
      </tr>
  @endif
  @if(!empty($fc['s3_tel']))
      <tr>
        <th scope="row">第3担当者携帯TEL</th>
        <td>{{ $fc['s3_tel'] }}</td>
      </tr>
  @endif
      <tr>
        <th scope="row">資材置き場住所</th>
        <td>〒{{ $fc['s_zipcode'] }} <br> {{ $fc['s_pref'] }} {{ $fc['s_city'] }}{{ $fc['s_street'] }} <br> TEL : {{$fc['storage_tel']}}</td>
      </tr>
      <tr>
        <th scope="row">任意受け取り場所住所</th>
        <td>〒{{ $fc['optional_zipcode'] }} <br> {{ $fc['optional_pref'] }} {{ $fc['optional_city'] }}{{ $fc['optional_street'] }} <br> TEL : {{$fc['optional_tel']}}  {{ $fc['optional_staff']}}</td>
      </tr>
      <tr>
        <th scope="row">ステータス</th>
  @switch($fc['status'])
    @case(1)
        <td>案件振り分けOK</td>
        @break
    @case(2)
        <td>退会済み</td>
        @break
    @case(3)
        <td>研修中</td>
        @break
    @case(4)
        <td>活動停止中</td>
        @break
  @endswitch
      </tr>
  @if(!empty($fc['quotation_memo']))
      <tr>
        <th scope="row">見積もり書<br>デフォルト備考欄</th>
        <td class="p-3">{!! nl2br($fc['quotation_memo']) !!}</td>
      </tr>
  @endif
  @if(!empty($fc['account_infomation1']))
      <tr>
        <th scope="row">口座情報</th>
        <td class="p-3">{!! nl2br($fc['account_infomation1']) !!}</td>
      </tr>
  @endif
  @if(!empty($fc['account_infomation2']))
      <tr>
        <th scope="row">第2口座情報</th>
        <td class="p-3">{!! nl2br($fc['account_infomation2']) !!}</td>
      </tr>
  @endif
  @if(!empty($fc['account_infomation3']))
      <tr>
        <th scope="row">第3口座情報</th>
        <td class="p-3">{!! nl2br($fc['account_infomation3']) !!}</td>
      </tr>
  @endif
      <tr {{adminOnlyHidden()}}>
        <th scope="row">メモ</th>
        <td class='p5'><textarea class='form-control f10 resize-none' readonly>{{ $fc['memo'] }}</textarea></td>
      </tr>
      <tr {{adminOnlyHidden()}}>
        <th scope="row">システム利用料種別</th>
        <td class='p-3'>{{ returnInvoicePaymentsType($fc['invoice_payments_type']) }}</td>
      </tr>
      <tr {{adminOnlyHidden()}}>
        <th scope="row">発注制限</th>
        <td class='p-3'>{{ ($fc['require_prepaid'] === 1 ? '前金のみ' : '制限なし') }}</td>
      </tr>
      <tr>
        <th scope="row">適格事業者番号</th>
        <td class='p-3'>{{ is_null($fc['qualified_business_number']) ? '登録無し' : $fc['qualified_business_number'] }}</td>
      </tr>
      <tr>
        <th scope="row">見積もり小消費税計算</th>
    @switch($fc['quotation_tax_option'] )
    @case(0)
        <td>四捨五入</td>
        @break
    @case(1)
        <td>切り上げ</td>
        @break
    @case(2)
        <td>切り捨て</td>
        @break
  @endswitch
      </tr>
      <tr>
        <th scope="row">サンプル送付設定</th>
        <td>{{$fc['admin_sample_send'] === 1 ? '本部が行う' : 'FC自身で行う' }}</td>
      </tr>
      <tr>
        <th scope="row">メール通知</th>
        <td>{!! $fc['allow_notification']=='1' ? '<i class="far fa-envelope mr5 color-link"></i>送信する' : '<i class="far fa-envelope mr5 color-red"></i>送信しない' !!}</td>
      </tr>
    <tr>
        <th scope="row">自己案件率</th>
    @if($count !== 0)
        <td>
          {{ contactRate($fc['id'], $count) }}%
          <button type="button" class="btn btn-info ml30 my-3" data-toggle="collapse" data-target="#collapseEmail" aria-expanded="false" aria-controls="collapseEmail" dusk='email-history' {{adminOnlyHidden()}}>エリア開放メールの送信履歴を見る</button>
          {{--  ・自己獲得案件5件以上あった → 件数チェック  → area_open_email_sendsにレコードが存在しない
                ・自己獲得案件5件未満で本部がチェック外した → area_open_email_sends.send_status = 2
                ・自己獲得案件5件未満でメール送った → area_open_email_sends.send_status = 1 --}}
          <ul class="list-group collapse mb20" id="collapseEmail">
    @foreach($fc['open_mail_sends'] AS $key => $value)
        @switch(true)
            @case($value==='success')
              <li class="list-group-item">{{$key}}年: 条件クリア</li>
                @break
            @case($value === 1)
              <li class="list-group-item">{{$key}}年: エリア開放メール送信済み</li>
                @break
            @case($value === 0)
              <li class="list-group-item">{{$key}}年: 本部が手動で送信停止</li>
                @break
            @default
              <li class="list-group-item">{{$key}}年: 条件クリア</li>
        @endswitch
    @endforeach
          </ul>
        </td>
    @else
        <td>0%</td>
    @endif
      </tr>
      <tr>
        <th scope="row">印鑑</th>
        <td class='p5'><img class="w-25" src="{{ !empty($fc['seal']) ? '/images/seals/'.$fc['seal'] : 'https://placehold.jp/80x80.png?text=角印' }}"/></td>
      </tr>
    </tbody>
  </table>
  <div class="mt-4 d-flex justify-content-between">
    <a href="{{route('users.index')}}" class="px-xl-5 btn btn-light">戻る</a>
  @if(isAdmin() || \Auth::id() == $fc['id'])
    <a href="{{ route('users.edit', ['id' => $fc['id']]) }}" class="px-xl-5 btn btn-primary">修正</a>
  @endif
  </div>
</div> 

@endsection