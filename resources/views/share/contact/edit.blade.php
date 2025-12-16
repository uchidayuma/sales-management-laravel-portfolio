@extends('layouts.layout')

@section('css')
<link href="{{ asset('plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet"/>
<link href="{{ asset('styles/contact/edit.min.css?20220818') }}" rel="stylesheet" />
@endsection

@section('javascript')
<script src="{{ asset('plugins/flatpickr/flatpickr.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/ja.js') }}" defer></script>
<script src="{{ asset('js/contact/edit.js?20240302') }}" type="text/javascript" defer></script>
<script>
  window.emailValidateEndpoint = "{{ config('app.email_validate_endpoint') }}";
  window.emailValidateApiKey = "{{ config('app.email_validate_api_key') }}";
</script>
<script src="{{ asset('js/contact/email-validate.js') }}" defer></script>
@endsection

@section('content')

@if ($errors->any())
<div class="alert alert-danger">
  <ul>
    @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif
<div class="edit-table-wrapper">
  <div class="fc-management-user-create-header">
  <form action="{{ route('contact.update', ['id' => $contact->id]) }}" method="post" enctype="multipart/form-data" id="contact-update">
    @csrf
    @method('PUT')
  <div class="breadcrumbs-notop d-flex justify-content-between">
    <div>{!! $breadcrumbs->render() !!}</div>
    <div>
      <button type="button" class="px-xl-5 btn btn-light mr-3" href="#" onClick="history.back(); return false;">更新せずに戻る</button>
      <button type="submit" class="px-xl-5 common__update btn btn-primary" dusk='update-button'>更新</button>
    </div>
  </div>
  <table class="common-table-stripes-column">
    <tbody>
      <tr>
        <th>No</th>
        <td class="common-table-stripes-column__td flex p-3 h6 m-0">
          {{ displayContactId($contact) }}
          <input type="hidden" name="c[id]" class="common-table-stripes-column__input" value="{{ $contact->id }}">
        </td>
      </tr>
      <tr>
        <th>登録日</th>
        <td class="p-3 h6">{{ date('Y年m月d日', strtotime($contact->created_at)) }}</td>
      </tr>
      <tr>
        <th>お問い合わせ種別</th>
        <td class="common-table-stripes-column__td flex">
          <select id='contact_type' name="c[contact_type_id]" class="common-table-stripes-column__input-select custom-select form-control w50">
            @foreach($contactTypes as $t)
              <option value="{{ $t->id }}" {{selected($t->id == $contact->contact_type_id)}}>{{ $t->name }}</option>
            @endforeach
          </select>
          <div class="form-check form-check-inline">
            <label class="form-check-label" for="sample_checkbox" dusk='from-sample'>
              <input name="c[free_sample_required]" type="hidden" value="0" />
              <input class="form-check-input ml-3" type="checkbox" id="sample_checkbox" name="c[free_sample_required]" value="1" {{checked($contact->free_sample_required === 1)}} />サンプル請求から移行したらチェック</label>
          </div>
        </td>
      </tr>
  @if (isAdmin())
      <tr id='same-customers'>
        <th>同一顧客ID <a href="{{route('contact.customers')}}" target='blankW'><i class="fas fa-search"></i>検索</a><br>*案件ID</th>
        <td class='p10'>
          <input name='sc[ids]' class='form-control w100' type="tel" placeholder='複数入力する場合はカンマで区切る（ 1,2,113 )' value="{{ !empty($same_customer_contacts_id) ? $same_customer_contacts_id : '' }}" dusk='same-input'/>
        </td>
      </tr>
  @endif
      <tr>
        <th>無料サンプル</th>
        <td class="common-table-stripes-column__td flex"><input type="text" name="c[free_sample]" class="form-control common-table-stripes-column__input" value="{{ $contact->free_sample }}"></td>
      </tr>
  @if( isCompany($contact))
      <tr>
        <th>会社名</th>
        <td class="common-table-stripes-column__td flex"><input type="text" name="c[company_name]" class="form-control w100" value="{{ $contact->company_name }}" id="company_name"></td>
      </tr>
      <tr>
        <th>会社名フリガナ</th>
        <td class="common-table-stripes-column__td flex"><input type="text" name="c[company_ruby]" class="form-control w100" value="{{ $contact->company_ruby }}" id="companyruby"></td>
      </tr>
      <tr>
        <th>業種</th>
        <td class="common-table-stripes-column__td flex"><input type="text" name="c[industry]" class="form-control w50" value="{{ $contact->industry }}"></td>
      </tr>
  @endif
      <tr>
        <th>{{ isCompany($contact) ? '担当者名' : '名前' }}</th>
        <td class="common-table-stripes-column__td flex">
        <input type="text" name="c[surname]" class="{{ isCompany($contact) ? 'form-control mr-4 w30' : 'form-control mr-4'}}" placeholder="{{ isCompany($contact) ? '担当者名' : '苗字'}}" value="{{ !is_null($contact->surname) ? $contact->surname : $contact->name }}" style='width: 12rem;' dusk="surname">
        <input type="text" name="c[name]" class="{{ isCompany($contact) ? 'd-none' : 'form-control'}}" placeholder="名前" value="{{ $contact->name }}" style='width: 12rem;' dusk="name">
      </tr>
      <tr>
        <th>{{ isCompany($contact) ? '担当者名フリガナ' : '名前フリガナ' }}</th>
        <td class="common-table-stripes-column__td flex">
        <input type="text" name="c[surname_ruby]" class="{{ isCompany($contact) ? 'form-control mr-4 w30' : 'form-control mr-4'}}" placeholder="{{ isCompany($contact) ? 'タントウシシャメイ' : 'ミョウジ'}}" value="{{ !is_null($contact->surname_ruby) ? $contact->surname_ruby : $contact->name_ruby }}" style='width: 12rem;' dusk="surname-ruby">
        <input type="text" name="c[name_ruby]" class="{{ isCompany($contact) ? 'd-none' : 'form-control'}}" placeholder="ナマエ" value="{{ $contact->name_ruby }}" style='width: 12rem;' dusk="name-ruby">
      </tr>
      <tr>
        <th>担当FC</th>
        <td class="common-table-stripes-column__td flex">
  @if (isAdmin())
          <select class='form-control' name='c[user_id]' dusk="select-fc">
            <option value="" {{selected($contact->user_id == null) }}>担当FCなし</option>
    @foreach($fcs as $fc)
            <option value={{$fc->id}} {{selected($contact->user_id == $fc->id)}}>{{$fc->name}}</option>
    @endforeach
          </select>
  @else
          <p class=''>{{ $contact['user_name'] }}</p>
          <input type='hidden' name='c[user_id]' value="{{ $contact['user_id']}}"/> 
  @endif
        </td>
      </tr>
      <tr>
        <th>郵便番号</th>
        <td class="common-table-stripes-column__td flex"><input type="text" name="c[zipcode]" class="form-control common-table-stripes-column__input" value="{{ $contact->zipcode }}" required></td>
      </tr>
      <tr>
        <th>住所</th>
        <td class="common-table-stripes-column__td flex">
          <input type="text" name="c[pref]" class="form-control w20 mr-4" value="{{ $contact->pref }}">
          <input type="text" name="c[city]" class="form-control w30 mr-4" value="{{ $contact->city }}">
          <input type="text" name="c[street]" class="form-control w50" value="{{ $contact->street }}">
        </td>
      </tr>
      <tr>
        <th>TEL</th>
        <td class="common-table-stripes-column__td flex">
          <input type="text" name="c[tel]" class="form-control w50 mr-4" placeholder="メイン電話" value="{{ $contact->tel }}">
          <input type="text" name="c[tel2]" class="form-control w50" placeholder="携帯電話" value="{{ $contact->tel2 }}">
        </td>
      </tr>
      <tr>
        <th>FAX</th>
        <td class="common-table-stripes-column__td flex"><input type="text" name="c[fax]" class="form-control w50" value="{{ $contact->fax }}"></td>
      </tr>
      <tr>
        <th>MAIL</th>
        <td class="common-table-stripes-column__td flex"><input type="text" name="c[email]" class="form-control w50" value="{{ $contact->email }}"></td>
      </tr>
      <tr>
        <th>年代</th>
        <td class="common-table-stripes-column__td flex"><input type="text" name="c[age]" class="form-control common-table-stripes-column__input" placeholder='1980年代' value="{{ $contact->age }}"></td>
      </tr>
    @if(isContactType($contact->contact_type_id, [2,3,6,7]))
      <tr>
        <th>お見積もり内容</th>
        <td class="common-table-stripes-column__td flex">
          <select  name="c[quote_details]" class="form-control w50">
            <option value=''> --- </option>
            <option value='材料のみ' {{ $contact->quote_details === '材料のみ' ? 'selected' : '' }}>材料のみ</option>
            <option value='施工希望' {{ $contact->quote_details === '施工希望' ? 'selected' : '' }}>施工希望</option>
            <option value='施工希望、材料のみ' {{ $contact->quote_details === '施工希望、材料のみ' || $contact->quote_details === '両方' ? 'selected' : '' }}>施工希望、材料のみ</option>
          </select>
        </td>
      </tr>
    @endif
      <tr>
        <th>下地状況</th>
        <td class="common-table-stripes-column__td flex"><input type="text" name="c[ground_condition]" class="form-control w100" value="{{ $contact->ground_condition }}"></td>
      </tr>
      <tr>
        <th>施工場所面積</th>
        <td class="common-table-stripes-column__td flex">
          <span class="mt-auto mb-auto mr0 h5">縦</span><input type="text" name="c[vertical_size]" class="form-control" value="{{ $contact->vertical_size }}" placeholder="50" style='width: 12rem;'>
          <span class="mt-auto mb-auto mr10 ml10 h5">×</span>
          <span class="mt-auto mb-auto mr0 h5">横</span><input type="text" name="c[horizontal_size]" class="form-control" value="{{ $contact->horizontal_size }}" placeholder="50" style='width: 12rem;'><span class="mt-auto ml-10 color-red h6">*単位を指定しない場合、mになります</span>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">平米数</th>
        <td class="common-table-stripes-column__td d-flex align-items-center"><input type="number" name="c[square_meter]" class="form-control common-table-stripes-column__input w15" value="{{ $contact->square_meter }}" placeholder="44" dusk='p3-square_meter'><span class='f11'>平米</span></td>
      </tr>
      <tr>
        <th>希望商品</th>
        <td class="common-table-stripes-column__td flex"><input type="text" name="c[desired_product]" class="form-control w100" value="{{ $contact->desired_product }}"></td>
      </tr>
      <tr>
        <th>希望日</th>
        <td class="common-table-stripes-column__td flex">
          <input type="datetime" data-provide="datepicker" name="c[desired_datetime1]" class="form-control w100 datetimepicker mr-4" value="{{ $contact->desired_datetime1 }}" autocomplete="off">
          <input type="text" data-provide="datepicker" name="c[desired_datetime2]" class="form-control w100 datetimepicker" value="{{ $contact->desired_datetime2 }}" autocomplete="off">
        </td>
      </tr>
      <tr>
        <th>住所</th>
        <td class="common-table-stripes-column__td flex"><input type="text" name="c[visit_address]" class="form-control w100" value="{{ $contact->visit_address }}"></td>
      </tr>
      <tr>
        <th>人工芝の使用用途</th>
        <td class="common-table-stripes-column__td flex"><input type="text" name="c[use_application]" class="form-control w100" value="{{ $contact->use_application }}"></td>
      </tr>
  @if(isAdmin() || (isFc() && $contact->step_id > 3))
      <tr>
        <th>施工前画像</th>
        <td class='d-flex justify-content-between'>
          <div class="image-uploader js-image-uploader js-uploader">
              <p class="image-uploader__label">施工前画像①</p>
            @if(isset($contact->before_image1))
              <p class="js-image-remove image-remove" dusk='before-image-remove1'><i class="fas fa-times color-white fa-3x"></i></p>
              <img class='image-uploader__image js-upload-image' src="{{s3Url()}}images/before/{{$contact->id}}/{{$contact->before_image1}}" />
              <p class='js-upload-description uploader__description mt10'></p>
            @else
              <img class='image-uploader__image js-upload-image' src="" />
              <p class='js-upload-description uploader__description mt10'>クリックするかドラッグ&ドロップでファイルをアップロードできます。</p>
            @endif
              <input type="hidden" name="c[before-image1-state]" class='js-file-state' value='' />
              <input type="file" id='js-before-image1' name="c[before_image1]" class='js-image-file js-file'/>
          </div>
          <div class="image-uploader js-image-uploader js-uploader">
              <p class="image-uploader__label">施工前画像②</p>
            @if(isset($contact->before_image2))
              <p class="js-image-remove image-remove"><i class="fas fa-times color-white fa-3x"></i></p>
              <img class='image-uploader__image js-upload-image' src="{{s3Url()}}images/before/{{$contact->id}}/{{$contact->before_image2}}" />
              <p class='js-upload-description uploader__description mt10'></p>
            @else
              <img class='image-uploader__image js-upload-image' src="" />
              <p class='js-upload-description uploader__description mt10'>クリックするかドラッグ&ドロップでファイルをアップロードできます。</p>
            @endif
              <input type="hidden" name="c[before-image2-state]" class='js-file-state' value='' />
              <input type="file" id='js-before-image2' name="c[before_image2]" class='js-image-file js-file'/>
          </div>
          <div class="image-uploader js-image-uploader js-uploader">
              <p class="image-uploader__label">施工前画像③</p>
            @if(isset($contact->before_image3))
              <p class="js-image-remove image-remove"><i class="fas fa-times color-white fa-3x"></i></p>
              <img class='image-uploader__image js-upload-image' src="{{s3Url()}}images/before/{{$contact->id}}/{{$contact->before_image3}}" />
              <p class='js-upload-description uploader__description mt10'></p>
            @else
              <img class='image-uploader__image js-upload-image' src="" />
              <p class='js-upload-description uploader__description mt10'>クリックするかドラッグ&ドロップでファイルをアップロードできます。</p>
            @endif
              <input type="hidden" name="c[before-image3-state]" class='js-file-state' value='' />
              <input type="file" id='js-before-image3' name="c[before_image3]" class='js-image-file js-file'/>
          </div>
        </td>
      </tr>
  @endif
  @if(isAdmin() || (isFc() && $contact->step_id === 11))
      <tr>
        <th>施工後画像</th>
        <td class='d-flex justify-content-between'>
          <div class="image-uploader js-image-uploader js-uploader">
              <p class="image-uploader__label">施工後画像①</p>
            @if(isset($contact->after_image1))
              <p class="js-image-remove image-remove"><i class="fas fa-times color-white fa-3x"></i></p>
              <img class='image-uploader__image js-upload-image' src="{{s3Url()}}images/after/{{$contact->id}}/{{$contact->after_image1}}" />
              <p class='js-upload-description uploader__description mt10'></p>
            @else
              <img class='image-uploader__image js-upload-image' src="" />
              <p class='js-upload-description uploader__description mt10'>クリックするかドラッグ&ドロップでファイルをアップロードできます。</p>
            @endif
              <input type="hidden" name="c[after-image1-state]" class='js-file-state' value='' />
              <input type="file" id='js-after-image1' name="c[after_image1]" class='js-image-file js-file'/>
          </div>
          <div class="image-uploader js-image-uploader js-uploader">
              <p class="image-uploader__label">施工後画像②</p>
            @if(isset($contact->after_image2))
              <p class="js-image-remove image-remove" dusk='after-image-remove2'><i class="fas fa-times color-white fa-3x"></i></p>
              <img class='image-uploader__image js-upload-image' src="{{s3Url()}}images/after/{{$contact->id}}/{{$contact->after_image2}}" />
              <p class='js-upload-description uploader__description mt10'></p>
            @else
              <img class='image-uploader__image js-upload-image' src="" />
              <p class='js-upload-description uploader__description mt10'>クリックするかドラッグ&ドロップでファイルをアップロードできます。</p>
            @endif
              <input type="hidden" name="c[after-image2-state]" class='js-file-state' value='' />
              <input type="file" id='js-after-image2' name="c[after_image2]" class='js-image-file js-file'/>
          </div>
          <div class="image-uploader js-image-uploader js-uploader">
              <p class="image-uploader__label">施工後画像③</p>
            @if(isset($contact->after_image3))
              <p class="js-image-remove image-remove"><i class="fas fa-times color-white fa-3x"></i></p>
              <img class='image-uploader__image js-upload-image' src="{{s3Url()}}images/after/{{$contact->id}}/{{$contact->after_image3}}" />
              <p class='js-upload-description uploader__description mt10'></p>
            @else
              <img class='image-uploader__image js-upload-image' src="" />
              <p class='js-upload-description uploader__description mt10'>クリックするかドラッグ&ドロップでファイルをアップロードできます。</p>
            @endif
              <input type="hidden" name="c[after-image3-state]" class='js-file-state' value='' />
              <input type="file" id='js-after-image3' name="c[after_image3]" class='js-image-file js-file'/>
          </div>
        </td>
      </tr>
  @endif
      <tr>
        <th>添付資料<span class='f09' style="color: red">既にファイルが登録されている箇所も変更できます。</span></th>
        <td class='d-flex flex-wrap justify-content-between'>
            <div class="uploader js-uploader">
              @if(isset($contact->document1))
                <p class="js-image-remove image-remove"><i class="fas fa-times color-white fa-3x"></i></p>
                <p class='js-upload-description uploader__description'>{{ $contact->document1_original_name }}</p>
              @else
                <p class='js-upload-description uploader__description'>クリックするかドラッグ&ドロップでファイルをアップロードできます。</p>
              @endif
                <input type="hidden" name="c[document1-state]" class='js-file-state' value='' />
                <input type="file" name="c[document1]" class='js-file js-image1'/>
            </div>
            <div class="uploader js-uploader">
              @if(isset($contact->document2))
                <p class="js-image-remove image-remove"><i class="fas fa-times color-white fa-3x"></i></p>
                <p class='js-upload-description uploader__description'>{{ $contact->document2_original_name }}</p>
              @else
                <p class='js-upload-description uploader__description'>クリックするかドラッグ&ドロップでファイルをアップロードできます。</p>
              @endif
                <input type="hidden" name="c[document2-state]" class='js-file-state' value='' />
                <input type="file" name="c[document2]" class='js-file js-image2' />
            </div>
            <div class="uploader js-uploader">
              @if(isset($contact->document3))
                <p class="js-image-remove image-remove"><i class="fas fa-times color-white fa-3x"></i></p>
                <p class='js-upload-description uploader__description'>{{ $contact->document3_original_name }}</p>
              @else
                <p class='js-upload-description uploader__description'>クリックするかドラッグ&ドロップでファイルをアップロードできます。</p>
              @endif
                <input type="hidden" name="c[document3-state]" class='js-file-state' value='' />
                <input type="file" name="c[document3]" class='js-file js-image3' />
            </div>
            <div class="uploader js-uploader">
              @if(isset($contact->document4))
                <p class="js-image-remove image-remove"><i class="fas fa-times color-white fa-3x"></i></p>
                <p class='js-upload-description uploader__description'>{{ $contact->document4_original_name }}</p>
              @else
                <p class='js-upload-description uploader__description'>クリックするかドラッグ&ドロップでファイルをアップロードできます。</p>
              @endif
                <input type="hidden" name="c[document4-state]" class='js-file-state' value='' />
                <input type="file" name="c[document4]" class='js-file js-image4' />
            </div>
            <div class="uploader js-uploader">
              @if(isset($contact->document5))
                <p class="js-image-remove image-remove"><i class="fas fa-times color-white fa-3x"></i></p>
                <p class='js-upload-description uploader__description'>{{ $contact->document5_original_name }}</p>
              @else
                <p class='js-upload-description uploader__description'>クリックするかドラッグ&ドロップでファイルをアップロードできます。</p>
              @endif
                <input type="hidden" name="c[document5-state]" class='js-file-state' value='' />
                <input type="file" name="c[document5]" class='js-file js-image5' />
            </div>
        </td>
      </tr>
      <tr>
        <th>認知経路</th>
        <td class="common-table-stripes-column__td flex"><input type="text" name="c[where_find]" class="form-control w100" value="{{ $contact->where_find }}"></td>
      </tr>
      <tr>
        <th>SNS</th>
        <td class="common-table-stripes-column__td flex"><input type="text" name="c[sns]" class="form-control w100" value="{{ $contact->sns }}"></td>
      </tr>
      <tr>
        <th>コメント</th>
        <td class="common-table-stripes-column__td flex"><textarea name="c[comment]" rows="5" class="form-control w100">{{ $contact->comment }}</textarea> </td>
      </tr>
      <tr>
        <th>必要事項</th>
        <td class="common-table-stripes-column__td flex"><input type="text" name="c[requirement]" class="form-control w100" value="{{ $contact->requirement }}"></td>
      </tr>
  @if(isAdmin() && !empty($contact->cancel_step))
      <tr>
        <th>キャンセルタイミング</th>
        <td class="common-table-stripes-column__td flex">
          <select name="c[cancel_step]" class="common-table-stripes-column__input-select custom-select form-control">
          @foreach ($steps as $s)
            <option value="{{ $s->id }}" {{selected($s->id == $contact->cancel_step)}}>{{ $s->name }}</option>
          @endforeach
            <option value="">キャンセルを取りやめ</option>
          </select>
        </td>
      </tr>
  @endif
  @if(isAdmin() || ( empty($contact['cancel_step']) && $contact['step_id'] < 4))
      <tr>
        <th>ステップ<span class='f08'>（ステータス）</span></th>
        <td class="common-table-stripes-column__td flex">
          <select name="c[step_id]" class="common-table-stripes-column__input-select custom-select form-control">
      @if(isAdmin())
          @foreach ($steps as $s)
            <option value="{{ $s->id }}" {{selected($s->id == $contact->step_id)}}>{{ $s->name }}</option>
          @endforeach
      @else
      <!-- FCは見積もり作成以降のステップには変更できない -->
          @for ($i=0;$i<3;$i++)
            <option value="{{ $steps[$i]->id }}" {{selected($steps[$i]->id == $contact->step_id)}}>{{ $steps[$i]->name }}</option>
          @endfor
      @endif
          </select>
        </td>
      </tr>
  @endif
      <tr>
        <th>サンプル送付</th>
  @if(isset($contact->sample_send_at) && $contact->sample_send_at !== '1970-01-01')
        <td class="common-table-stripes-column__td flex">送付済み</td>
  @elseif(isFc() && $contact->own_contact == 1 && (1 <= $contact->step_id && $contact->step_id <= 4) && ($contact->status == 1 || $contact->status == 3))
        <td class="common-table-stripes-column__td flex mr-3">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="c[sample_send_at]" id="exampleRadios1" value=""  {{ is_null($contact->sample_send_at) ? 'checked' : ''}}>
                <label class="form-check-label" for="exampleRadios1" dusk="admin">{{ isFc() ? '本部に依頼する' : '本部が送付する'}}</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="c[sample_send_at]" id="exampleRadios2" value="1970-01-01" {{ $contact->sample_send_at === '1970-01-01' ? 'checked' : ''}}>
                <label class="form-check-label" for="exampleRadios2" dusk="myself">{{ isFc() ? '自社で送付する' : 'FCが送付する'}}</label>
            </div>
        </td>
  @elseif(isAdmin() && isset($contact->sample_send_at) && $contact->sample_send_at === '1970-01-01')
        <td class="common-table-stripes-column__td flex">FCが送付</td>
  @elseif(isAdmin() && is_null($contact->sample_send_at))
        <td class="common-table-stripes-column__td flex">未送付</td>
  @endif
      </tr>
  @if(isAdmin())
      <tr>
        <th>サンプル送付日</th>
        <td class="common-table-stripes-column__td">
        @if($contact->sample_send_at === '1970-01-01')
            FCが送付
        @elseif(isset($contact->sample_send_at))
            <p class="mb-1">未送付にしたい場合は送付日を消して保存してください</p>
            <input type="text" data-provide="datepicker" name="c[sample_send_at]" dusk="sample-send-at-input" class="form-control common-table-stripes-column__input datepicker" value="{{ $contact->sample_send_at }}">
        @else
            <p class="mb-1">未送付(送付日を入力したい場合はご入力ください)</p>
            <div class="d-flex items-center">
              <input type="text" data-provide="datepicker" name="c[sample_send_at]" dusk="sample-send-at-input" class="form-control common-table-stripes-column__input datepicker" value="{{ $contact->sample_send_at }}">
              <label id="for-myself" class="pointer ml-3"><i class="far fa-hand-point-left"></i>FCに依頼したい場合はクリック（1970-01-01のまま登録してください）</label>
            </div>
        @endif
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">メモ</th>
        <td class="common-table-stripes-column__td">
          <textarea name="c[memo]" class="form-control w100" rows="10" placeholder="メモ" dusk='h4-memo'>{{ $contact->memo }}</textarea>
        </td>
      </tr>
  @endif
      <tr>
        <th>最終更新</th>
        <td class="common-table-stripes-column__td flex p-3 m-0 h6">{{ date('Y年m月d日',strtotime($contact->updated_at)) }}</td>
      </tr>
    </tbody>
  </table>
  <div class="mt-4 d-flex justify-content-between">
    <button type="button" class="px-xl-5 btn btn-light" href="#" onClick="history.back(); return false;">更新せずに戻る</button>
    <button type="submit" class="px-xl-5 common__update btn btn-primary" dusk='update-button'>更新</button>
  </div>

</form>

@if(!empty($contact->cancel_step) && ($contact->user_id === $contact->main_user_id || is_null($contact->main_user_id)))
  <div class="float-xl-right mt-1">
    <form action="{{ route('contact.cancel.submit' , ['id' => $contact->id]) }}" method="post">
      @csrf
      <input type="submit" class="px-xl-5 btn btn-secondary justify-content-xl-end" onclick="return confirm('この案件をキャンセル扱いにします。よろしいですか？')" value="キャンセル案件に変更" />
    </form>
  </div>
@endif
</div>

@endsection