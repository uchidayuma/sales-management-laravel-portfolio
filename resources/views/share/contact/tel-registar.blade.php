@extends('layouts.layout')

@section('css')
<link href="{{ asset('plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet"/>
<link href="{{ asset('styles/contact/form.min.css') }}" rel="stylesheet" />
@endsection

@section('javascript')
<script src="{{ asset('plugins/flatpickr/flatpickr.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/ja.js') }}" defer></script>
{{-- <script src="{{ asset('js/share/drag-and-drop.js') }}" type="text/javascript" defer></script> --}}
<script src="{{ asset('js/contact/create.js?20220613') }}" defer></script>
<script src="{{ asset('js/contact/edit.js?20220130') }}" defer></script>
<script>
  window.copyData = @json($copyData);
</script>
<script>
  window.emailValidateEndpoint = "{{ config('app.email_validate_endpoint') }}";
  window.emailValidateApiKey = "{{ config('app.email_validate_api_key') }}";
</script>
<script src="{{ asset('js/contact/email-validate.js') }}" defer></script>
@endsection

@section('footer')
    <script src="https://yubinbango.github.io/yubinbango/yubinbango.js" charset="UTF-8" defer></script>
@endsection

@section('content')
{!! $breadcrumbs->render() !!}
@if ($errors->any())
<div class="alert alert-danger">
  <ul>
    @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>
@endif

<div class="form-table pb30">
  <table class="common-table-stripes-column">
    <tbody>
      <tr>
        <th class="common-table-stripes-column__th">No</th>
        <td class="common-table-stripes-column__td"><p class="form-control--p">自動で登録されます</p></td>
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">登録日</th>
        <td class="common-table-stripes-column__td"><p class="form-control--p">自動で登録されます</p></td>
          <input type="hidden" name="c[created_at]" class="form-control common-table-stripes-column__input" placeholder="登録日">
        </td>      
      </tr>
      <tr>
        <th class="common-table-stripes-column__th">お問い合わせ種別<span class="ml10 common-table-stripes-column__span">必須</span></th>
        <td class="common-table-stripes-column__td" id='contactType'>
          <div class="alert alert-warning">サンプル請求で案件登録を行うと、見積もり作成まで進むことができません。<br/>見積もり書の作成まで進むためには、<span class='f12 bold'>図面見積もり</span>か<span class='f12 bold'>訪問見積もり</span>を選択してください。</div>
          <group class="inline-radio mb20">
            <p class='mr20 p-2 bold f11'>個人</p>
            <input type="radio" name="contactType" value="0" checked hidden>
            <div class='w20 mr20 pointer'><input class='p-2 h-100 pointer' type="radio" name="contactType" value="1" dusk='type1'><label>サンプル請求</label></div>
            <div class='w20 mr20 pointer'><input class='p-2 h-100 pointer' type="radio" name="contactType" value="2" dusk='type2'><label>図面見積もり</label></div>
            <div class='w20 mr20 pointer'><input class='p-2 h-100 pointer' type="radio" name="contactType" value="3" dusk='type3'><label>訪問見積もり</label></div>
            <div class='w20 mr0 pointer'><input class='p-2 h-100 pointer' type="radio" name="contactType" value="4" dusk='type4'><label>その他問合せ</label></div>
          </group>
          <group class="inline-radio mb10">
            <p class='mr20 p-2 bold f11'>法人</p>
            <div class='w20 p-2 mr20 pointer'><input class='p-2 h-100 pointer' type="radio" name="contactType" value="5" dusk='type5'><label>サンプル請求</label></div>
            <div class='w20 p-2 mr20 pointer'><input class='p-2 h-100 pointer' type="radio" name="contactType" value="6" dusk='type6'><label>図面見積もり</label></div>
            <div class='w20 p-2 mr20 pointer'><input class='p-2 h-100 pointer' type="radio" name="contactType" value="7" dusk='type7'><label>訪問見積もり</label></div>
            <div class='w20 p-2 mr0 pointer'><input class='p-2 h-100 pointer' type="radio" name="contactType" value="8" dusk='type8'><label>その他問合せ</label></div>
          </group>
        </td>
      </tr>
    </tbody>
  </table>
  @include('share.contact.tel-contact-forms')
</div>

@endsection
