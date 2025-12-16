@extends('layouts.layout')

@section('css')
<link href="{{ asset('plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet"/>
{{-- <link href="{{ asset('styles/share/drag-and-drop.css') }}" rel="stylesheet" /> --}}
<link href="{{ asset('styles/account/registration.min.css') }}" rel="stylesheet"> 
<link href="{{ asset('styles/account/edit.min.css') }}" rel="stylesheet" />
@endsection

@section('javascript')
<script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8" defer></script>
<script src="{{ asset('plugins/flatpickr/flatpickr.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/ja.js') }}" defer></script>
<script>
  window.fc = @json($fc);
  window.appEnv = "{{ \App::environment() }}";
</script>
<script src="{{ asset('js/users/create.js') }}" defer></script>
@endsection

@section('content')
{!! $breadcrumbs->render() !!}
<!--FC情報編集ページ-->
<div class="fc-management__user-edit">
  <form id='js-form' method="POST" action="{{ route('users.update', ['id' => $fc->id]) }}" enctype="multipart/form-data">
    @csrf

    <div class="fc-management__user-edit__body">
    @if($errors->any())
    <div class="error alert alert-warning">
      <ul class="validation-ul">
        @foreach($errors->all() as $message)
          <li>{{ $message }}</li>
        @endforeach
      </ul>
    </div><!-- error alert alert-warning -->
    @endif
  @if(isFc())
    <div class="alert alert-warning" role="alert">貴社住所など、本ページで変更できないものにつきましては、変更希望をFC本部までご連絡ください</div>
  @endif
    <!-- No -->
    <div class="form-group">
      <table class="common-table-stripes-column">
        <tr class="common-table-stripes-column__tr">
          <th class="common-table-stripes-column__th">
            <p> No</p>
          </th>
          <td class="common-table-stripes-column__td">
            <input class="common-table-stripes-column__input form-control" type="text" name="fc[name]" value="{{ $fc->id }}" readonly>
          </td>
        </tr> <!-- common-table-stripes-column__tr -->

        <!-- 登録日 -->
        <tr class="common-table-stripes-column__tr">
          <th class="common-table-stripes-column__th">
            <p>登録日</p>
          </th>
          <td class="common-table-stripes-column__td">
            <input class="common-table-stripes-column__input form-control w-auto" type="text" name="fc[name]" value="{{ date('Y年m月d日', strtotime($fc->created_at)) }}" readonly>
          </td>
        </tr> <!-- common-table-stripes-column__tr -->

        <!-- 登録日 -->
        <tr class="common-table-stripes-column__tr">
          <th class="common-table-stripes-column__th">
            <p>契約日</p>
          </th>
          <td class="common-table-stripes-column__td">
        @if(isFc())
            <input class="common-table-stripes-column__input form-control w-auto" type="text" value="{{ !is_null($fc->contract_date) ? date('Y年m月d日', strtotime($fc->contract_date)) : '未入力' }}" disabled />
            <input type="hidden" name="fc[contract_date]" value="{{ !is_null($fc->contract_date) ? date('Y-m-d', strtotime($fc->contract_date)) : '' }}" />
        @else
            <input class="common-table-stripes-column__input form-control datepicker" id='datepicker' type="text" name="fc[contract_date]" value="{{ !is_null($fc->contract_date) ? date('Y-m-d', strtotime($fc->contract_date)) : '' }}" dusk='contract-date'>
        @endif
          </td>
        </tr> <!-- common-table-stripes-column__tr -->

        <!-- FC名 -->
        <tr class="common-table-stripes-column__tr">
          <th class="common-table-stripes-column__th">
            <p>FC名</p>
          </th>
          <td class="common-table-stripes-column__td">
            <input class="common-table-stripes-column__input form-control w40" type="text" name="fc[name]" value="{{ $fc->name }}" dusk='name' {{isFc() ? 'readonly' : ''}}>
          </td>
        </tr> <!-- common-table-stripes-column__tr -->

        <!-- 会社名 -->
        <tr class="common-table-stripes-column__tr">
          <th class="common-table-stripes-column__th">
            <p>会社名</p>
          </th>
          <td class="common-table-stripes-column__td">
            <input class="common-table-stripes-column__input form-control w40" type="text" name="fc[company_name]" value="{{ $fc->company_name }}" {{isFc() ? 'readonly' : ''}}>
          </td>
        </tr> <!-- common-table-stripes-column__tr -->

        <!-- 会社名フリガナ -->
        <tr class="common-table-stripes-column__tr">
          <th class="common-table-stripes-column__th">
            <p>会社名フリガナ</p>
          </th>
          <td class="common-table-stripes-column__td">
            <input class="common-table-stripes-column__input form-control w40" type="text" name="fc[company_ruby]" value="{{ $fc->company_ruby }}" {{isFc() ? 'readonly' : ''}}>
          </td>
        </tr> <!-- common-table-stripes-column__tr -->

        <tr class="common-table-stripes-column__tr" {{ adminOnlyHidden()}}>
          <th class="common-table-stripes-column__th">
            <p>所在都道府県</p>
          </th>
          <td class="common-table-stripes-column__td d-flex justify-content-between">
              <select class="form-control w-25" name="fc[prefecture_id]" dusk='prefecture'>
                {{-- 48は未選択prefecture_id --}}
                <option value="48" >---</option>
            @foreach($prefectures as $p)
                <option value="{{$p['id']}}" {{selected($fc->prefecture_id == $p['id']) }}>{{ $p['name'] }}</option>
            @endforeach
              </select>
          </td>
        </tr> <!-- common-table-stripes-column__tr -->
        
        <!-- 郵便番号 -->
        <tr>
          <th class="common-table-stripes-column__th">
            <p>郵便番号</p>
          </th>
          <td class="common-table-stripes-column__td">
          <input type="text" name="fc[zipcode]" class="common-table-stripes-column__input form-control" size="8" maxlength="8" onKeyUp="AjaxZip3.zip2addr(this,'','fc[pref]','fc[city]','fc[street]');" value="{{ $fc->zipcode }}" required {{isFc() ? 'readonly' : ''}}>
          </td>
        </tr>

        <!-- 住所 -->
        <tr class="common-table-stripes-column__tr">
          <th class="common-table-stripes-column__th">
            <p>住所</p>
          </th>
          <td class="common-table-stripes-column__td d-flex justify-content-between">
            <input name="fc[pref]" type="text" class="form-control w15" value="{{ $fc->pref }}" placeholder='都道府県' required {{isFc() ? 'readonly' : ''}}>
            <input name="fc[city]" type="text" class="form-control w20" value="{{ $fc->city }}" placeholder='市町村' required {{isFc() ? 'readonly' : ''}}>
            <input name="fc[street]" type="text" class="form-control w50" value="{{ $fc->street }}" placeholder='番地以下を入力' required {{isFc() ? 'readonly' : ''}}>
          </td>
        </tr> <!-- common-table-stripes-column__tr -->

        <!-- 担当エリア 202210月以降の外部テーブルから取ってくる -->
        <tr class="common-table-stripes-column__tr" {{ adminOnlyHidden()}}>
          <th class="common-table-stripes-column__th">
            <p>担当エリア</p>
          </th>
          <td class="common-table-stripes-column__td d-flex justify-content-between">
            <div class="form-group">
              <select class="form-control" name="fc[fc_apply_area_id]" dusk='fc-apply-area'>
                <option value=''>---</option>
          @foreach($fc_apply_areas AS $area)
                <option value={{$area['id']}} @selected($fc->fc_apply_area_id == $area['id'])>{{$area['name'] . ' ' . $area['content']}}</option>
          @endforeach
              </select>
            </div>
          </td>
        </tr> <!-- common-table-stripes-column__tr -->

        <!-- TEL -->
        <tr class="common-table-stripes-column__tr">
          <th class="common-table-stripes-column__th">
            <p>TEL</p>
          </th>
          <td class="common-table-stripes-column__td">
            <input class="common-table-stripes-column__input form-control" type="text" name="fc[tel]" value="{{ $fc->tel }}" {{isFc() ? 'readonly' : ''}}>
          </td>
          <div>{{ $errors->first('fc.tel') }}</div>
        </tr> <!-- common-table-stripes-column__tr -->

        <!-- FAX -->
        <tr class="common-table-stripes-column__tr">
          <th class="common-table-stripes-column__th">
            <p class="common-table-stripes-column__label"> FAX </p>
          </th>
          <td class="common-table-stripes-column__td">
            <input class="common-table-stripes-column__input form-control" type="text" name="fc[fax]" value="{{ $fc->fax }}" {{isFc() ? 'readonly' : ''}}>
          </td>
          <div>   </div>
        </tr> <!-- common-table-stripes-column__tr -->

        <!-- E-mail -->
        <tr class="common-table-stripes-column__tr">
          <th class="common-table-stripes-column__th">
            <p> メインメールアドレス </p>
          </th>
          <td class="common-table-stripes-column__td d-flex align-items-center">
            <input class="common-table-stripes-column__input form-control w40" type="email" name="fc[email]" value="{{ $fc->email }}" {{isFc() ? 'readonly' : ''}}>
        @if(isAdmin())
            <div class="custom-control custom-checkbox ml20">
              <input type="checkbox" type='checkbox' name="fc[allow_email]" value='1' id='allow-email' class="custom-control-input" {{checked($fc->allow_email=='1')}}>
              <label class="custom-control-label" for="allow-email">メール送信を許可する場合はチェック</label>
            </div>
        @endif
          </td>
        </tr> <!-- common-table-stripes-column__tr -->
        <tr class="common-table-stripes-column__tr">
          <th class="common-table-stripes-column__th">
            <p>メールアドレス2（連絡用） </p>
          </th>
          <td class="common-table-stripes-column__td">
            <input class="common-table-stripes-column__input form-control w40" type="email" name="fc[email2]" value="{{ $fc->email2 }}">
          </td>
        </tr> <!-- common-table-stripes-column__tr -->
        <tr class="common-table-stripes-column__tr">
          <th class="common-table-stripes-column__th">
            <p>メールアドレス3（連絡用）</p>
          </th>
          <td class="common-table-stripes-column__td">
            <input class="common-table-stripes-column__input form-control w40" type="email" name="fc[email3]" value="{{ $fc->email3 }}">
          </td>
        </tr> <!-- common-table-stripes-column__tr -->

        <tr>
          <th>メール通知設定</th>
          <td class="common-table-stripes-column__td">
            <div class="form-group form-check mb0">
              <input type="checkbox" class="form-check-input" name="fc[allow_notification]" value="1" id="allow-notification" {{checked($fc->allow_notification === 1)}} dusk='allow-notification'>
              <label class="form-check-label" for="allow-notification">業務停滞通知を送信する場合はチェック（見積もり依頼など、重要な通知は届きます）</label>
            </div>
          </td>
        </tr>

        <!-- 担当者名 -->
        <tr>
          <th class="common-table-stripes__th  justify-content-between">担当者名<span class="common-table-stripes-column__span ml10">名前のみ必須</span>
            <button class='btn btn-primary mt10' id='add-staff2' type='button' {{ isHidden(!empty($fc->staff2)) }}>第2担当者を追加</button>
          </th>
          <td class="common-table-stripes__td d-flex justify-content-start">
            <input class="form-control common-table-stripes-column__input mr20" type="text" name="fc[staff]" value="{{ $fc->staff }}" placeholder="担当者名を入力" dusk='staff' {{isFc() ? 'readonly' : ''}}>
            <input class="form-control common-table-stripes-column__input mr20" type="text" name="fc[staff_ruby]" value="{{ $fc->staff_ruby }}" placeholder="フリガナを入力" dusk='staff_ruby' {{isFc() ? 'readonly' : ''}}>
            <input class="form-control common-table-stripes-column__input w40" type="text" name="fc[s_tel]" value="{{ $fc->s_tel }}" placeholder="担当者携帯電話番号を入力" dusk='s_tel' {{isFc() ? 'readonly' : ''}}>
          </td>
        </tr>
        <!-- 担当者名 -->
        <tr class='staff2' {{ isHidden( is_null($fc->staff2)) }} >
          <th class="common-table-stripes__th  justify-content-between">第2担当者名
            <button class='btn btn-primary mt10' id='add-staff3' type='button'>第3担当者を追加</button>
            <i class="fas fa-times-circle staff-hide js-staff-hide"></i>
          </th>
          <td class="common-table-stripes__td d-flex justify-content-start">
            <input class="form-control common-table-stripes-column__input mr20" type="text" name="fc[staff2]" value="{{ $fc->staff2 }}" placeholder="担当者名を入力" dusk='staff2'>
            <input class="form-control common-table-stripes-column__input mr20" type="text" name="fc[staff2_ruby]" value="{{ $fc->staff2_ruby }}" placeholder="フリガナを入力" dusk='staff2_ruby'>
            <input class="form-control common-table-stripes-column__input w40" type="text" name="fc[s2_tel]" value="{{ $fc->s2_tel }}" placeholder="担当者携帯電話番号を入力" dusk='s2_tel'>
          </td>
        </tr>
        <!-- 担当者名 -->
        <tr class='staff3' {{ isHidden( is_null($fc->staff3)) }}>
          <th class="common-table-stripes__th  justify-content-between">第3担当者名
            <i class="fas fa-times-circle staff-hide js-staff-hide"></i>
          </th>
          <td class="common-table-stripes__td d-flex justify-content-start">
            <input class="form-control common-table-stripes-column__input mr20" type="text" name="fc[staff3]" value="{{ $fc->staff3 }}" placeholder="担当者名を入力" dusk='staff3'>
            <input class="form-control common-table-stripes-column__input mr20" type="text" name="fc[staff3_ruby]" value="{{ $fc->staff3_ruby }}" placeholder="フリガナを入力" dusk='staff3_ruby'>
            <input class="form-control common-table-stripes-column__input w40" type="text" name="fc[s3_tel]" value="{{ $fc->s3_tel }}" placeholder="担当者携帯電話番号を入力" dusk='s3_tel'>
          </td>
        </tr>

        <!-- はんこ（笑） -->
        <tr class="common-table-stripes-column__tr">
          <th class="common-table-stripes-column__th">
            <p>角印画像（見積書・発注書に使われます）</p>
          </th>
          <td class="common-table-stripes-column__td d-flex">
            <div class="uploader js-uploader pointer">
              <img class='seal js-preview' src={{!empty($fc->seal) ? "/images/seals/$fc->seal" : ''}}>
              <p class="{{ empty($fc->seal) ? 'd-none' : ''}} js-image-remove image-remove"><i class="fas fa-times color-white fa-3x"></i></p>
              <p class='js-upload-description uploader__description'>クリックするかドラッグ&ドロップでファイルをアップロードできます。</p>
              <input type="hidden" name="fc[seal-state]" class='js-seal-state' value='nochange' />
              <input type="file" name="fc[seal]" class='js-file js-image1'/>
            </div>
          </td>
          <div>{{ $errors->first('fc.seal') }}</div>
        </tr> <!-- common-table-stripes-column__tr -->

        <tr>
          <th class="common-table-stripes__th  justify-content-between">
            見積もり書<br>デフォルト備考欄  
          </th>
          <td class="common-table-stripes__td d-flex justify-content-start">
            <textarea class="form-control common-table-stripes-column__input w100" type="text" name="fc[quotation_memo]" placeholder="設定した内容が自動で見積もり書の備考欄に入力されます" rows='3' dusk='quotation-memo'>{{ $fc->quotation_memo }}</textarea>
          </td>
        </tr>

        <tr>
          <th class="common-table-stripes__th  justify-content-between">
            口座情報
            <button class='btn btn-primary mt10' id='add-account-infomation2' type='button' {{ isHidden(!empty($fc->staff2)) }}>第2口座情報を追加</button>
          </th>
          <td class="common-table-stripes__td d-flex justify-content-start">
            <textarea class="form-control common-table-stripes-column__input w100" type="text" name="fc[account_infomation1]" placeholder="サンプル銀行 本店&#13;普通 0000000&#13;サンプル株式会社&#13;※振込手数料につきましてはお客様負担でお願いいたします&#13;【お支払い条件】前金にてご入金" rows='5' dusk='account_info1'>{{ $fc->account_infomation1 }}</textarea>
          </td>
        </tr>
        <!-- 講座情報２ -->
        <tr class='infomation2' {{ isHidden( is_null($fc->account_infomation2)) }}>
          <th class="common-table-stripes__th  justify-content-between">
            第2口座情報
            <button class='btn btn-warning mt10' id='add-account-infomation3' type='button'>第3口座情報を追加</button>
            <i class="fas fa-times-circle infomation-hide js-infomation-hide"></i>
          </th>
          <td class="common-table-stripes__td d-flex justify-content-start">
            <textarea class="form-control common-table-stripes-column__input w100" type="text" name="fc[account_infomation2]" placeholder="サンプル銀行 本店&#13;普通 0000000&#13;サンプル株式会社&#13;※振込手数料につきましてはお客様負担でお願いいたします&#13;【お支払い条件】前金にてご入金" rows='5' dusk='account_info2'>{{ $fc->account_infomation2 }}</textarea>
          </td>
        </tr>
        <!-- 講座情報３ -->
        <tr class='infomation3' {{ isHidden( is_null($fc->account_infomation3)) }}>
          <th class="common-table-stripes__th  justify-content-between">
            第3口座情報
            <i class="fas fa-times-circle infomation-hide js-infomation-hide"></i>
          </th>
          <td class="common-table-stripes__td d-flex justify-content-start">
            <textarea class="form-control common-table-stripes-column__input w100" type="text" name="fc[account_infomation3]" placeholder="サンプル銀行 本店&#13;普通 0000000&#13;サンプル株式会社&#13;※振込手数料につきましてはお客様負担でお願いいたします&#13;【お支払い条件】前金にてご入金" rows='5'>{{ $fc->account_infomation3 }}</textarea>
          </td>
        </tr>
        <tr class="common-table-stripes-column__tr">
          <th class="common-table-stripes-column__th"><p>適格事業者番号</p></th>
          <td class="common-table-stripes-column__td d-flex align-items-center">
            <span class='f10 bold mr-1'>T</span>
            <input type="text" name="fc[qualified_business_number]" class="form-control" value="{{ $fc->qualified_business_number }}" placeholder="T以降の13桁の数字" dusk="qualified-business-number">
          </td>
        </tr>
  @if( isAdmin() )
        <!-- メモ -->
        <tr class="common-table-stripes-column__tr">
          <th class="common-table-stripes-column__th">
            <p>メモ</p>
          </th>
          <td class="common-table-stripes-column__td d-flex justify-content-between">
            <textarea name="fc[memo]" class="form-control" placeholder='担当エリア' rows='3' dusk='memo'>{{ $fc->memo }}</textarea>
          </td>
        </tr> <!-- common-table-stripes-column__tr -->

        <tr class="common-table-stripes-column__tr">
          <th class="common-table-stripes-column__th">
            <p>システム利用料種別</p>
          </th>
          <td class="common-table-stripes-column__td">
            <div class="form-check">
              <input class="form-check-input pointer" type="radio" name="fc[invoice_payments_type]" id="payment-type1" value="0" {{ checked($fc->invoice_payments_type == 0) }}>
              <label class="form-check-label pointer" for="payment-type1">請求なし</label>
            </div>
            <div class="form-check">
              <input class="form-check-input pointer" type="radio" name="fc[invoice_payments_type]" id="payment-type2" value="1" {{ checked($fc->invoice_payments_type == 1) }}>
              <label class="form-check-label pointer" for="payment-type2">HP掲載料</label>
            </div>
            <div class="form-check">
              <input class="form-check-input pointer" type="radio" name="fc[invoice_payments_type]" id="payment-type3" value="2" {{ checked($fc->invoice_payments_type == 2) }}>
              <label class="form-check-label pointer" for="payment-type3">ブランド使用料</label>
            </div>
          </td>
        </tr> <!-- common-table-stripes-column__tr -->

        <tr class="common-table-stripes-column__tr">
          <th class="common-table-stripes-column__th">
            <p>発注制限</p>
          </th>
          <td class="common-table-stripes-column__td">
            <div class="form-group form-check">
              <input type="checkbox" class="form-check-input" name="fc[require_prepaid]" id="prepaid-only" value="1" {{checked($fc->require_prepaid)}}>
              <label class="form-check-label" for="prepaid-only">資材発注の際、全額前金のみ許可する場合はチェック</label>
            </div>
          </td>
        </tr> <!-- common-table-stripes-column__tr -->

        <tr class="common-table-stripes__tr">
          <th class="common-table-stripes__th  justify-content-between">FCステータス</th>
          <td class="common-table-stripes__td p10">
            <div class="form-group mb0">
              <select class="form-control" name="fc[status]">
                <option value="1" {{selected($fc->status =='1') }}>案件アサインOK</option>
                <option value="3" {{selected($fc->status =='3') }}>研修中</option>
                <option value="4" {{selected($fc->status =='4') }}>活動停止中</option>
                <option value="2" {{selected($fc->status =='2') }}>退会済み</option>
              </select>
            </div>
          </td>
        </tr>
  @endif
      </table>
      <h2 class='mt-3 mb-3'>荷物受け取り設定</h2>
      <table class="common-table-stripes-column">
        <!-- 資材置き場郵便番号 -->
        <tr>
          <td class='w5 f11 bold' rowspan="2" style="writing-mode: vertical-rl;">資材置き場</td>
          <th class="common-table-stripes-column__th"><p>郵便番号</p></th>
          <td class="common-table-stripes-column__td">
            <input type="text" name="fc[s_zipcode]" class="common-table-stripes-column__input form-control" size="8" maxlength="8" onKeyUp="AjaxZip3.zip2addr(this,'','fc[s_pref]','fc[s_city]','fc[s_street]');" value="{{ $fc->s_zipcode }}" {{isFc() ? 'readonly' : ''}}>
          </td>
          <th class="common-table-stripes-column__th"><p>荷受け人電話番号</p></th>
          <td class="common-table-stripes-column__td">
            <input type="text" name="fc[storage_tel]" class="form-control w20r" placeholder="000-000-000">
          </td>
        </tr>
        <!-- 住所 -->
        <tr class="common-table-stripes-column__tr">
          <th class="common-table-stripes-column__th"><p>住所</p></th>
          {{-- <td class="common-table-stripes-column__td d-flex justify-content-between w90"> --}}
          <td colspan="3" class="common-table-stripes-column__td">
            <p class="d-flex justify-content-between">
              <input name="fc[s_pref]" type="text" class="form-control w15" value="{{ $fc->s_pref }}" placeholder='都道府県' {{isFc() ? 'readonly' : ''}}>
              <input name="fc[s_city]" type="text" class="form-control w20" value="{{ $fc->s_city }}" placeholder='市町村' {{isFc() ? 'readonly' : ''}}>
              <input name="fc[s_street]" type="text" class="form-control w50" value="{{ $fc->s_street }}" placeholder='番地以下を入力' {{isFc() ? 'readonly' : ''}}>
            </p>
          </td>
        </tr> <!-- common-table-stripes-column__tr -->
        <tr>
          <td class='w5 f11 bold' rowspan="2" style="writing-mode: vertical-rl;">任意受け取り場所</td>
          <th class="common-table-stripes-column__th"><p>郵便番号</p></th>
          <td class="common-table-stripes-column__td">
            <input type="text" name="fc[optional_zipcode]" class="common-table-stripes-column__input form-control" size="8" maxlength="8" onKeyUp="AjaxZip3.zip2addr(this,'','fc[optional_pref]','fc[optional_city]','fc[optional_street]');" value="{{ $fc->optional_zipcode }}" placeholder="1010051" dusk="optional-zipcode">
          </td>
          <th class="common-table-stripes-column__th"><p>受け取り人名<br/>連絡先電話番号</p></th>
          <td class="common-table-stripes-column__td">
            <input type="text" name="fc[optional_staff]" class="form-control w20r mb-2" value="{{ $fc->optional_staff }}" placeholder="任意受取場所の荷受人様" dusk="optional-staff">
            <input type="text" name="fc[optional_tel]" class="form-control w20r" placeholder="任意受け取り場所時の電話番号" value="{{ $fc->optional_tel }}" dusk="optional-tel">
          </td>
        </tr>
        <!-- 住所 -->
        <tr class="common-table-stripes-column__tr">
          <th class="common-table-stripes-column__th"><p>住所</p></th>
          {{-- <td colspan="3" class="common-table-stripes-column__td d-flex justify-content-between"> --}}
          <td colspan="3" class="common-table-stripes-column__td">
            <p class='d-flex justify-content-between'>
              <input name="fc[optional_pref]" type="text" class="form-control w15" value="{{ $fc->optional_pref }}" placeholder='都道府県'>
              <input name="fc[optional_city]" type="text" class="form-control w20" value="{{ $fc->optional_city }}" placeholder='市町村'>
              <input name="fc[optional_street]" type="text" class="form-control w50" value="{{ $fc->optional_street }}" placeholder='番地以下を入力'>
            </p>
          </td>
        </tr> <!-- common-table-stripes-column__tr -->
      </table>
      <h2 class='mt-3 mb-3'>見積書消費税計算</h2>
      <table class="common-table-stripes-column">
        <!-- 見積もり消費税計算方法選択 -->
        <tr>
          <th class="common-table-stripes-column__th"><p>見積もり小消費税計算</p></th>
          <td class="common-table-stripes-column__td d-flex">
            <div class="form-group mb0">
              <select class="form-control" name="fc[quotation_tax_option]" dusk='quotation-tax'>
                <option value="0" {{selected($fc->quotation_tax_option =='0') }}>四捨五入</option>
                <option value="1" {{selected($fc->quotation_tax_option =='1') }}>切り上げ</option>
                <option value="2" {{selected($fc->quotation_tax_option =='2') }}>切り捨て</option>
              </select>
            </div>
          </td>
        </tr>
      </table>

      <h3 class='mt-3 mb-3'>案件登録設定</h3>
      <table class="common-table-stripes-column">
        <tr>
          <th>サンプル送付設定</th>
          <td class="common-table-stripes-column__td">
            <div class="form-group form-check mb0">
              <input type="checkbox" class="form-check-input" name="fc[admin_sample_send]" value="1" id="sample-send" {{checked($fc->admin_sample_send === 1)}} dusk='admin-sample-send'>
              <label class="form-check-label" for="sample-send">常にサンプルは本部から送付する</label>
            </div>
          </td>
        </tr>
      </table>

    </div> <!-- form-group -->
  </div> <!-- cardbody -->
  <div class="mt-4 d-flex justify-content-between mb20"> 
    <button type="button" class="px-xl-5 btn btn-light" href="#" onClick="history.back(); return false;">戻る</button>
    <a type="button" class="px-xl-5 btn btn-warning" href="{{ route('users.changepassword', ['id' => $fc->id]) }}" dusk='edit_pass'>パスワード変更</a>
    <button type="submit" class="px-xl-5 btn btn-primary" dusk='submit'>更新</button>
  </div>

  </form>
</div>

@endsection
