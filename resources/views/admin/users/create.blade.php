@extends('layouts.layout')

@section('css')
<link href="{{ asset('plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet"/>
<link href="{{ asset('styles/account/registration.min.css') }}" rel="stylesheet">
@endsection

@section('javascript')
<script>
  window.appEnv = "{{ \App::environment() }}";
</script>
<script src="https://ajaxzip3.github.io/ajaxzip3.js" charset="UTF-8" defer></script>
<script src="{{ asset('plugins/flatpickr/flatpickr.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/ja.js') }}" defer></script>
<script src="{{ asset('js/users/create.js') }}" defer></script>
@endsection

@section('content')

<!--FC登録ページ-->
<div class="user-create">
  <form id='js-form' method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">

    <p class="user-create-pankuzu"><i class="fas fa-building user-create-pankuzu__icon"></i>FC管理<span class="user-create-pankuzu__span"> > </span> 新規登録</p>

    <div class="user-create__alert-warning">
      @if($errors->any())
      <div class="error alert alert-warning">
        <ul class="validation-ul">
          @foreach($errors->all() as $message)
            <li>{{ $message }}</li>
          @endforeach
        </ul>
      </div><!-- error alert alert-warning -->
      @endif

      @csrf
      <!-- No -->
      <table class="common-table-stripes-column">
        <tr>
          <th class="common-table-stripes-column__th  justify-content-between">No</th>
          <td class="common-table-stripes-column__td"><p class='form-control--p'>自動で登録されます。</p></td>
        </tr>
        <!-- 登録日 -->
        <tr>
          <th class="common-table-stripes-column__th  justify-content-between">登録日</th>
          <td class="common-table-stripes-column__td"><p class='form-control--p'>自動で登録されます。</p></td>
        </tr>
        <tr>
          <th class="common-table-stripes-column__th  justify-content-between">契約日</th>
          <td class="common-table-stripes-column__td"><input class="form-control common-table-stripes-column__input datepicker" type="text" name="fc[contract_date]" placeholder="契約日" dusk='contract-date' value="{{ old('fc.contract_date') }}" autocomplete="off" required/></td>
        </tr>
        <!-- FC名 -->
        <tr>
          <th class="common-table-stripes-column__th  justify-content-between">FC名<span class="common-table-stripes-column__span">必須</span></th>
          <td class="common-table-stripes-column__td"><input class="form-control common-table-stripes-column__input" type="text" name="fc[name]" value="{{ old('fc.name') }}" placeholder="FC名を入力" dusk='name' required></td>
        </tr>
        <!-- 法人か個人かチェック -->
        <tr>
          <th class="common-table-stripes-column__th  justify-content-between">個人の場合チェック<span class="common-table-stripes-column__span"></span></th>
          <td class="common-table-stripes-column__td"><input class="form-checkbox" type="checkbox" id="company_type" name="fc[is_personal]" value="{{ old('fc.is_personal') }}" dusk='is_personal'></td>
        </tr>
        <!-- 会社名 -->
        <tr>
          <th class="common-table-stripes-column__th  justify-content-between">会社名<span class="common-table-stripes-column__span">必須</span></th>
          <td class="common-table-stripes-column__td"><input class="form-control common-table-stripes-column__input" type="text" name="fc[company_name]" value="{{ old('fc.company_name') }}" placeholder="会社名を入力" dusk='company_name' required></td>
        </tr>
        <!-- 会社名フリガナ -->
        <tr>
          <th class="common-table-stripes-column__th  justify-content-between">会社名フリガナ</th>
          <td class="common-table-stripes-column__td"><input class="form-control common-table-stripes-column__input" type="text" name="fc[company_ruby]" value="{{ old('fc.company_ruby') }}" placeholder="会社名をひらがなで入力" dusk='company_ruby'></td>
        </tr>
        <tr>
          <th class="common-table-stripes-column__th">
            <p>所在都道府県</p>
          </th>
          <td class="common-table-stripes-column__td d-flex justify-content-between">
              <select class="form-control w-25" name="fc[prefecture_id]" dusk='prefecture'>
                <option value="0" >---</option>
            @foreach($prefectures as $p)
                <option value="{{$p['id']}}">{{ $p['name'] }}</option>
            @endforeach
              </select>
          </td>
        </tr> <!-- common-table-stripes-column__tr -->
        <!-- 郵便番号 -->
        <tr>
          <th class="common-table-stripes-column__th  justify-content-between">郵便番号（ハイフンなし）<span class="common-table-stripes-column__span">必須</span></th>
          <td><input type="text" name="fc[zipcode]" class="form-control common-table-stripes-column__input" size="7" maxlength="7" onKeyUp="AjaxZip3.zip2addr(this,'','fc[pref]','fc[city]','fc[street]');" value="{{ old('fc.zipcode') }}" placeholder="郵便番号" dusk='zipcode' required></td>
        </tr>
        <!-- 住所 -->
        <tr>
          <th class="common-table-stripes-column__th  justify-content-between">住所（事業所）<span class="common-table-stripes-column__span">必須</span></th>
          <td class="common-table-stripes-column__td d-flex justify-content-between">
            <input name="fc[pref]" type="text" class="form-control w15" value="{{ old('fc.pref') }}" placeholder="都道府県" required>
            <input name="fc[city]" type="text" class="form-control w20 ommon-table-stripes-column__input" value="{{ old('fc.city') }}" placeholder="市町村" required>
            <input name="fc[street]" type="text" class="form-control w50 ommon-table-stripes-column__input" value="{{ old('fc.street') }}" placeholder="それ以外の住所" dusk='street' required>
          </td>
        </tr>
        <!-- 担当エリア -->
        <tr>
          <th class="common-table-stripes-column__th  justify-content-between">担当エリア（新）</th>
          <td class="common-table-stripes-column__td d-flex justify-content-between">
            <div class="form-group">
              <select class="form-control" name="fc[fc_apply_area_id]" dusk='fc-apply-area'>
                <option value=''>---</option>
          @foreach($fc_apply_areas AS $area)
                <option value={{$area['id']}}><span class='bold'>{{$area['name']}}</span> {{ $area['content']}}</option>
          @endforeach
              </select>
            </div>
          </td>
        </tr>
        <!-- TEL -->
        <tr>
          <th class="common-table-stripes-column__th  justify-content-between">TEL<span class="common-table-stripes-column__span">必須</span></th>
          <td class="common-table-stripes-column__td"><input class="form-control common-table-stripes-column__input" type="text" name="fc[tel]" value="{{ old('fc.tel') }}" placeholder="電話番号を入力" dusk='tel'></td>
        </tr>
        <!-- FAX -->
        <tr>
          <th class="common-table-stripes__th  justify-content-between">FAX</th>
          <td class="common-table-stripes__td"><input type='text' class="form-control common-table-stripes-column__input" name="fc[fax]" value="{{ old('fc.fax') }}" dusk='fax' placeholder="FAX番号を入力"></td>
        </tr>
        <!-- E-mail -->
        <tr>
          <th class="common-table-stripes__th  justify-content-between">メールアドレス<span class="common-table-stripes-column__span">必須</span></th>
          <td class="common-table-stripes__td"><input class="form-control common-table-stripes-column__input" type="email" name="fc[email]" value="{{ old('fc.email') }}" placeholder="メールアドレスを入力" dusk='email'></td>
        </tr>
        <tr>
          <th class="common-table-stripes__th  justify-content-between">メールアドレス2（連絡用）</th>
          <td class="common-table-stripes__td"><input class="form-control common-table-stripes-column__input" type="email" name="fc[email2]" value="{{ old('fc.email') }}" placeholder="メールアドレスを入力"></td>
        </tr>
        <tr>
          <th class="common-table-stripes__th  justify-content-between">メールアドレス3（連絡用）</th>
          <td class="common-table-stripes__td"><input class="form-control common-table-stripes-column__input" type="email" name="fc[email3]" value="{{ old('fc.email') }}" placeholder="メールアドレスを入力"></td>
        </tr>
        <!-- 担当者名 -->
        <tr>
          <th class="common-table-stripes__th  justify-content-between">担当者名<span class="common-table-stripes-column__span ml10">名前のみ必須</span>
            <button class='btn btn-primary mt10' id='add-staff2' type='button'>第2担当者を追加</button>
          </th>
          <td class="common-table-stripes__td d-flex justify-content-start">
            <input class="form-control common-table-stripes-column__input mr20" type="text" name="fc[staff]" value="{{ old('fc.staff') }}" placeholder="担当者名を入力" dusk='staff'>
            <input class="form-control common-table-stripes-column__input mr20" type="text" name="fc[staff_ruby]" value="{{ old('fc.staff_ruby') }}" placeholder="フリガナを入力" dusk='staff_ruby'>
            <input class="form-control common-table-stripes-column__input w40" type="text" name="fc[s_tel]" value="{{ old('fc.s_tel') }}" placeholder="担当者携帯電話番号を入力" dusk='s_tel'>
          </td>
        </tr>
        <!-- 担当者2名 -->
        <tr class='staff2'>
          <th class="common-table-stripes__th  justify-content-between">
            第2担当者名
            <button class='btn btn-warning mt10' id='add-staff3' type='button'>第3担当者を追加</button>
            <i class="fas fa-times-circle staff-hide js-staff-hide"></i>
          </th>
          <td class="common-table-stripes__td d-flex justify-content-start">
            <input class="form-control common-table-stripes-column__input mr20" type="text" name="fc[staff2]" value="{{ old('fc.staff2') }}" placeholder="第2担当者名を入力" dusk='staff2'>
            <input class="form-control common-table-stripes-column__input mr20" type="text" name="fc[staff2_ruby]" value="{{ old('fc.staff2_ruby') }}" placeholder="フリガナを入力" dusk='staff2_ruby'>
            <input class="form-control common-table-stripes-column__input w40" type="text" name="fc[s2_tel]" value="{{ old('fc.s2_tel') }}" placeholder="携帯電話番号を入力" dusk='s2_tel'>
          </td>
        </tr>
        <!-- 担当者3 -->
        <tr class='staff3'>
          <th class="common-table-stripes__th  justify-content-between">
            第3担当者名
            <i class="fas fa-times-circle staff-hide js-staff-hide"></i>
          </th>
          <td class="common-table-stripes__td d-flex justify-content-start">
            <input class="form-control common-table-stripes-column__input mr20" type="text" name="fc[staff3]" value="{{ old('fc.staff3') }}" placeholder="第3担当者名を入力" dusk='staff3'>
            <input class="form-control common-table-stripes-column__input mr20" type="text" name="fc[staff3_ruby]" value="{{ old('fc.staff3_ruby') }}" placeholder="フリガナを入力" dusk='staff3_ruby'>
            <input class="form-control common-table-stripes-column__input w40" type="text" name="fc[s3_tel]" value="{{ old('fc.s3_tel') }}" placeholder="携帯電話番号を入力" dusk='s3_tel'>
          </td>
        </tr>
        <!-- 資材置き場郵便番号 -->
        <tr>
          <th class="common-table-stripes-column__th  justify-content-between">郵便番号（資材置き場）</th>
          <td class="common-table-stripes__td"><input type="text" name="fc[s_zipcode]" class="form-control common-table-stripes-column__input" size="8" maxlength="8" onKeyUp="AjaxZip3.zip2addr(this,'','fc[s_pref]','fc[s_city]','fc[s_street]');" value="{{ old('fc.s_zipcode') }}" placeholder="郵便番号" dusk='s_zipcode' ></td>
        </tr>
        <!-- 住所 -->
        <tr>
          <th class="common-table-stripes-column__th  justify-content-between">住所（資材置き場）</th>
          <td class="common-table-stripes-column__td d-flex justify-content-between">
            <input name="fc[s_pref]" type="text" class="form-control w15" value="{{ old('fc.s_pref') }}" placeholder="都道府県"  >
            <input name="fc[s_city]" type="text" class="form-control w20 ommon-table-stripes-column__input" value="{{ old('fc.s_city') }}" placeholder="市町村">
            <input name="fc[s_street]" type="text" class="form-control w50 ommon-table-stripes-column__input" value="{{ old('fc.s_street') }}" placeholder="それ以外の住所" dusk='s_street'>
          </td>
        </tr>
        <!-- 講座情報１ -->
        <tr>
          <th class="common-table-stripes__th  justify-content-between">
            口座情報
            <button class='btn btn-primary mt10' id='add-account-infomation2' type='button'>第2口座情報を追加</button>
          </th>
          <td class="common-table-stripes__td d-flex justify-content-start">
            <textarea class="form-control common-table-stripes-column__input w50" type="text" name="fc[account_infomation1]" value="{{ old('fc.account_infomation1') }}" placeholder="第1口座情報を入力" rows='4' dusk='account_info1'></textarea>
          </td>
        </tr>
        <!-- 講座情報２ -->
        <tr class='infomation2'>
          <th class="common-table-stripes__th  justify-content-between">
            第2口座情報
            <button class='btn btn-warning mt10' id='add-account-infomation3' type='button'>第3口座情報を追加</button>
            <i class="fas fa-times-circle infomation-hide js-infomation-hide"></i>
          </th>
          <td class="common-table-stripes__td d-flex justify-content-start">
            <textarea class="form-control common-table-stripes-column__input w50" type="text" name="fc[account_infomation2]" value="{{ old('fc.account_infomation2') }}" placeholder="第2口座情報を入力" rows='4' dusk='account_info2'></textarea>
          </td>
        </tr>
        <!-- 講座情報３ -->
        <tr class='infomation3'>
          <th class="common-table-stripes__th  justify-content-between">
            第3口座情報
            <i class="fas fa-times-circle infomation-hide js-infomation-hide"></i>
          </th>
          <td class="common-table-stripes__td d-flex justify-content-start">
            <textarea class="form-control common-table-stripes-column__input w50" type="text" name="fc[account_infomation3]" value="{{ old('fc.account_infomation3') }}" placeholder="第3口座情報を入力" rows='4'></textarea>
          </td>
        </tr>
        <!-- メモ -->
        <tr>
          <th class="common-table-stripes-column__th  justify-content-between">メモ（本部だけに表示されます）</th>
          <td class="common-table-stripes-column__td">
            <textarea class='form-control fc-memo' name='fc[memo]' rows='3' dusk='memo'>{{old('fc.memo')}}</textarea>
          </td>
        </tr>
        <tr>
          <th class="common-table-stripes-column__th">
            <p>システム利用料種別</p>
          </th>
          <td class="common-table-stripes-column__td">
            <div class="form-check">
              <input class="form-check-input pointer" type="radio" name="fc[invoice_payments_type]" id="payment-type1" value="0" checked>
              <label class="form-check-label pointer" for="payment-type1">請求なし</label>
            </div>
            <div class="form-check">
              <input class="form-check-input pointer" type="radio" name="fc[invoice_payments_type]" id="payment-type2" value="1">
              <label class="form-check-label pointer" for="payment-type2">HP掲載料</label>
            </div>
            <div class="form-check">
              <input class="form-check-input pointer" type="radio" name="fc[invoice_payments_type]" id="payment-type3" value="2">
              <label class="form-check-label pointer" for="payment-type3">ブランド使用料</label>
            </div>
          </td>
        </tr>

      </table>
      <!-- 下にあるボタン -->
      <div class="buttons">
        <input type="submit" class="btn btn-primary" value='FCを登録' dusk='submit'>
      </div>

    </div> <!-- card -->
  </form>
</div> <!-- fc-management__user-create -->

@endsection
