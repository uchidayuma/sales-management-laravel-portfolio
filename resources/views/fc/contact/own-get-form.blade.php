@extends('layouts.layout')

@section('css')
<link rel="stylesheet" href="{{ asset('plugins/flatpickr/flatpickr.min.css') }}">
<link href="{{ asset('styles/contact/form.min.css') }}" rel="stylesheet" />
@endsection

@section('javascript')
<script src="{{ asset('plugins/ajaxzip3/ajaxzip3.js') }}" charset="UTF-8" defer></script>
<script src="{{ asset('plugins/yubinbango/yubinbango.js') }}" charset="UTF-8"></script>
<script src="{{ asset('plugins/flatpickr/flatpickr.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/ja.js') }}" defer></script>
<script src="{{ asset('js/contact/create.js') }}" defer></script>
@endsection

@section('content')
{!! $breadcrumbs->render() !!}
<div class="form-table">
  <form action="{{ route('fc.contact.post') }}" method="POST" enctype="multipart/form-data" class="h-adr">
    @csrf
  <input type="hidden" name="c[own_contact]" value="1">
  <input type="hidden" name="c[step_id]" value="2">
  <table class="common-table-stripes-column">
    <tbody>
      <tr>
        <th class="common-table-stripes-column__th">No</th>
        <td class="common-table-stripes-column__td">自動で登録されます</td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">登録日</th>
        <td class="common-table-stripes-column__td">自動で登録されます</td>
          <input type="hidden" name="c[created_at]" class="form-control-sm common-table-stripes-column__input" placeholder="登録日">
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">お問い合わせ種別</th>
        <td class="common-table-stripes-column__td">
          <select name="c[contact_type_id]" class="form-control-sm common-table-stripes-column__select" id="contactType">
            <option value="" >お問い合わせ種別を選択してください</option>
            @foreach($contactTypes as $t)
              <option value="{{ $t->id }}" >{{ $t->name }}</option>
            @endforeach
          </select>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">無料サンプル</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[free_sample]" class="form-control-sm common-table-stripes-column__input" placeholder="無料サンプル"></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">会社名・個人名</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[company_name]" class="form-control-sm common-table-stripes-column__input" placeholder="会社名"></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">会社名・個人名フリガナ</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[company_ruby]" class="form-control-sm common-table-stripes-column__input" placeholder="会社名フリガナ"></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">担当者名</th>
        <td class="common-table-stripes-column__td">
          <input type="text" name="c[surname]" class="form-control-sm common-table-stripes-column__input" placeholder="姓">
          <input type="text" name="c[name]" class="form-control-sm common-table-stripes-column__input" placeholder="名">
      </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">業種</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[industry]" class="form-control-sm common-table-stripes-column__input" placeholder="業種"></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">郵便番号</th>
        <td class="common-table-stripes-column__td"><input name="c[zipcode]" type="text" class="form-control-sm common-table-stripes-column__input" onKeyUp="AjaxZip3.zip2addr(this,'','c[pref]','c[city]','c[street]');" value="{{ old('c.zipcode') }}" required placeholder="郵便番号"></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">訪問住所</th>
        <td class="common-table-stripes-column__td">
          <input type="text" name="c[pref]"   class="form-control-sm common-table-stripes-column__input" value="{{ old('c.pref') }}" placeholder="県">
          <input type="text" name="c[city]"   class="form-control-sm common-table-stripes-column__input" value="{{ old('c.city') }}" placeholder="市">
          <input type="text" name="c[street]" class="form-control-sm common-table-stripes-column__input" value="{{ old('c.street') }}" placeholder="それ以外の住所">
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">TEL</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[tel]" class="form-control-sm common-table-stripes-column__input" placeholder="電話番号"></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">FAX</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[fax]" class="form-control-sm common-table-stripes-column__input" placeholder="FAX"></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">MAIL</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[email]" class="form-control-sm common-table-stripes-column__input" placeholder="メールアドレス"></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">年代</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[age]" class="form-control-sm common-table-stripes-column__input" placeholder="年代"></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">お見積もり内容</th>
        <td class="common-table-stripes-column__td">
          <select name="c[quote_details]" class="form-control-sm common-table-stripes-column__select" id="quoteDetails">
            <option value="" >お見積もり内容を選択してください</option>
            <option value="施工希望" >施工希望</option>
            <option value="材料のみ">材料のみ</option>
          </select>
        </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">下地状況</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[ground_condition]" class="form-control-sm common-table-stripes-column__input" placeholder="下地状況"></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">施工場所面積</th>
          <td class="common-table-stripes-column__td">
            <input type="text" name="c[vertical_size]" class="form-control-sm" placeholder="縦">cm
            <input type="text" name="c[horizontal_size]" class="form-control-sm" placeholder="横">cm
          </td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">希望商品</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[desired_product]" class="form-control-sm common-table-stripes-column__input" placeholder="希望商品"></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">希望日</th>
        <td class="common-table-stripes-column__td">
          <input type="datetime" name="c[desired_datetime1]" class="datepicker" data-provide="datepicker" placeholder="希望日時1" />
          <input type="datetime" name="c[desired_datetime2]" class="datepicker" data-provide="datepicker" placeholder="希望日時2" />
        </td>
      <tr>
        <th class="common-table-stripes-column__th">住所</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[visit_address]" class="form-control-sm common-table-stripes-column__input" placeholder="訪問住所"></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">人工芝の使用用途</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[use_application]" class="form-control-sm common-table-stripes-column__input" placeholder="使用用途"></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">認知経路</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[where_find]" class="form-control-sm common-table-stripes-column__input" placeholder="認知経路"></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">SNS</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[sns]" class="form-control-sm common-table-stripes-column__input" placeholder="SNS"></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">メモ</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[comment]" class="form-control-sm common-table-stripes-column__input" placeholder="メモ"></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">必要事項</th>
        <td class="common-table-stripes-column__td"><input type="text" name="c[requirement]" class="form-control-sm common-table-stripes-column__input" placeholder="必要事項"></td>
      </tr>
    </tbody>
  </table>
  <div class="mt-4 d-flex justify-content-between">
    <p><button type="submit" class="px-xl-5 btn btn-primary">登録</button></p>
  </div>
  </form>
</div>

@endsection
