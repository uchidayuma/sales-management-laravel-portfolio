@extends('layouts.layout')

@section('css')
<link href="{{ asset('styles/contact/customers-list.min.css') }}" rel="stylesheet" />
<link href="{{ asset('plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet" />
<link href="{{ asset('plugins/remodal/remodal.css') }}" rel="stylesheet" />
<link href="{{ asset('plugins/remodal/remodal-default-theme.css') }}" rel="stylesheet" />
<link href="{{ asset('plugins/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
<link href="{{ asset('plugins/bootstrap-multiselect/bootstrap-multiselect.min.css') }}" rel="stylesheet" />
@endsection

@section('javascript')
<script src="{{ asset('js/contact/unassigned-list.js?20210812') }}" defer></script>
<script src="{{ asset('plugins/remodal/remodal.min.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/flatpickr.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/ja.js') }}" defer></script>
<script src="{{ asset('js/contact/customers-list.js?20231122') }}" defer></script>
<script src="{{ asset('plugins/select2/dist/js/select2.min.js') }}" defer></script>
<script src="{{ asset('plugins/popper/popper.min.js') }}"></script>
<script src="/plugins/bootstrap-multiselect/bootstrap-multiselect.min.js" defer></script>
<script>
    window.fc = `{{ array_key_exists('fc', $inputs) ? $inputs['fc'] : null }}`;
    window.type = @json(array_key_exists('type', $inputs) ? $inputs['type'] : null);
    window.prefectures = `{{ array_key_exists('prefectures', $inputs) ? $inputs['prefectures'] : null }}`;
    window.createdAt = `{{ array_key_exists('created_at', $inputs) ? $inputs['created_at'] : null }}`;
    window.sentAt = `{{ array_key_exists('sent_at', $inputs) ? $inputs['sent_at'] : null }}`;
</script>
@endsection

@section('content')
<form class="common-form d-flex" action="{{ route('customers.search') }}">
    <input type="tel" name="number" class="form-control w15 mr5" placeholder="案件No" value={{ !empty($inputs['number']) ? $inputs['number'] : '' }}>
    <input type="text" name="name" class="form-control w30 mr5" placeholder="顧客名（会社名・個人名）" value={{ !empty($inputs['name']) ? $inputs['name'] : '' }}>
    <input type="text" name="address" class="form-control w25 mr5" placeholder="メールアドレス" value={{ !empty($inputs['address']) ? $inputs['address'] : '' }}>
    <input type="tel" name="tell" class="form-control w20 mr5" placeholder="電話番号" value={{ !empty($inputs['tell']) ? $inputs['tell'] : '' }}>
    <input type="submit" name="customers" value="検索" class="btn btn-primary w10">
</form>

<div class="d-flex align-items-center justify-content-between pankuzu mb10">
    {!! $breadcrumbs->render() !!}
    @if(isAdmin())
    <div class='d-flex'>
        <a href="#custom-modal" class='btn btn-primary csv-modal mr10' dusk='custom-csv-export'>選択式CSVエクスポート</a>
        <a href="#modal" class='btn btn-info csv-modal mr10' dusk='csv-export'>フォローメールCSVエクスポート</a>
        <!-- 案件フィルター -->
        <form id="submit_form" action="{{ route('contact.customers') }}">
            <select class="custom-select" id="submit_select" name="selectData">
                <option value="all" {{ !empty($select) && $select ==  'all' ? 'selected' : '' }}>全ての案件を表示</option>
                <option value="admin" {{ !empty($select) &&  $select == 'admin' ? 'selected' : '' }}>本部獲得案件</option>
            </select>
        </form>
    </div>
    @endif
</div>

@if(isAdmin())
<p class="mb5 font-weight-bold h6">案件絞り込み条件</p>
<form class="common-form mb10 mt0" id="filtering-form" style="visibility: hidden" action="{{ route('contact.customers') }}">
    <div class=" mb5">
        <div class="d-flex mb-2">
            <select name='fc' class="select-fc form-control w35 mr-2" aria-placeholder='FC' dusk='select-fc'>
                <option value=0>FC指定なし</option>
                @foreach($users AS $u)
                <option value="{{ $u->id }}" isremove="{{ $u->status == 2 ? true : false }}">{{ $u['name'] }}</option>
                @endforeach
            </select>
            <select name="type[]" class="select-type form-control w35 mr-2" multiple="multiple" placeholder="選択" dusk='select-type'>
                @foreach($contact_types AS $t)
                <option value="{{ $t->id }}">{{ $t['name'] }}</option>
                @endforeach
            </select>
            <select name='prefectures' class="select-prefectures form-control w35 mr-2" aria-placeholder='prefectures' dusk='select-prefectures'>
                <option value=0>都道府県選択なし</option>
                @foreach($prefectures AS $p)
                <option value="{{ $p->name }}">{{ $p['name'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="d-flex mb-2">
            <input type="text" name="created_at" class="select-created-at form-control w45" placeholder="問い合わせ日" autocomplete="off" dusk='created-at'>
            <input type="button" class='mr5 border-0 rounded' id="clearCreatedAt" value="選択解除">
            <input type="text" name="sent_at" class="select-sent-at form-control w45" placeholder="サンプル送付日" autocomplete="off" dusk='sent-at'>
            <input type="button" class='mr5 border-0 rounded' id="clearSentAt" value="選択解除">
        </div>
    </div>
    <div class="text-center mb0">
        <input type="submit" value="検索" class="btn btn-primary w20" dusk='filter-contact'>
    </div>
</form>
@endif

<div class="d-flex justify-content-center">
    <!-- 調整用div -->
    {{ $customers->appends(request()->query())->links() }}
</div>

<!-- 顧客一覧ページのテーブルは、customers-list.scss -->
<table class="table-stripes-row">
    <thead class="table-stripes-row-thead">
        <tr>
            <th scope="col">案件No</th>
            <th scope="col" class="w6r keep-all text-noworp">問い合わせ日</th>
            <th scope="col">依頼種別</th>
            <th scope="col">名前</th>
            <th scope="col">住所</th>
            <th scope="col">状態</th>
            <th scope="col" {{ adminOnlyHidden()}}>同一顧客</th>
            <th scope="col" class="p0">案件編集</th>
            <th scope="col">コピー</th>
        </tr>
    </thead>
    <tbody class="table-stripes-row-tbody">
        @foreach($customers as $c)
        <tr class='js-contact' id={{$c->id}} stepid={{$c->step_id}}>
            <td class="table-stripes-row-tbody__td w5r d-flex td_adjustment align-items-center flex-nowrap"><a href="{{ route('contact.show', ['id' => $c->id]) }}" dusk="contact-detail">{{ displayContactId($c) }}</a><i class="ml10 color-link pointer fas fa-external-link-alt" onclick="window.open(`{{ route('contact.show', ['id' => $c->id]) }}`,'subwindow','width=700','height=700');"></i></td>
            <td class="table-stripes-row-tbody__td w6r keep-all">{!! nl2br(date("Y年 \n m月d日", strtotime($c->created_at))) !!}</td>
            <td class="table-stripes-row-tbody__td"><img class="contact-type__label" src="/images/icons/contact-types/{{$c->contact_type_id}}.png"></td>
            <td class="table-stripes-row-tbody__td keep-all">{{ customerName($c) }}</td>
            <td class="table-stripes-row-tbody__td keep-all">{{$c->pref}}<br>{{$c->city}}{{$c->street}}</td>
            <td class="table-stripes-row-tbody__td">
                <div class="step_labels d-flex align-items-center flex-wrap">
                    {!! !empty($c->sample_send_at) ? sampleSend($c->sample_send_at) : '<img src="/images/icons/steps/space.png" class="step__label mb5 mr5">'!!}
                    {!! !empty($c->user_id) && $c->step_id == 1 ? '<img src="/images/icons/steps/fc.png" class="step__label mb5 mr5">' : '' !!}
                    {!! returnStepLabel($c->step_id, $c->cancel_step) !!}
                </div>
            </td>
            @if(isAdmin())
            <td class="table-stripes-row-tbody__td"><a href="#modal-same-customer" class="btn same-customer js-submit-modal-open btn-secondary not-load" dusk="same-customer-modal-{{ $c->id }}" contactId="{{ $c->id }}">同一顧客</a></td>
            @endif
            <td class="table-stripes-row-tbody__td"><a type="button" href="{{ route('assigned.edit', ['id' => $c->id ]) }}" class="edit-buttn btn btn-warning mr5" dusk="contact-edit">編集</a></td>
            <td class="table-stripes-row-tbody__td"><a class="btn btn-success f08" dusk="contact-copy{{ $c->id }}" href="{{ route('contact.form', ['copy' => $c->id ]) }}">コピー</a></td>
        </tr>
        @endforeach
    </tbody>
</table>
{{ $customers->appends(request()->query())->links() }}

@if(isAdmin())
<div class="remodal w80" data-remodal-id="modal">
    <button data-remodal-action="close" class="remodal-close"></button>
    <h2 class='h2 mb30'>テレアポ業者用CSVエクスポート</h2>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form id='csvform' action="{{ route('contact.csv.export') }}" method="POST">
        @csrf
        <div class='row mb10'>
            <div class="col-md-5">
                <label class='h4'>エクスポートの開始日</label>
            </div>
            <div class="col-md-2 h2 d-flex justify-content-center align-items-center"></div>
            <div class="col-md-5">
                <label class='h4'>エクスポートの終端日</label>
            </div>
        </div> <!-- row -->
        <div class='row mb30'>
            <!--datepicker-->
            <div class="col-md-5">
                <input data-provide="datepicker" class="form-control datepicker js-start-date" type="datetime" placeholder="出力開始日" name="start_date" value="" dusk='datepicker_first'>
            </div>
            <div class="col-md-2 h2 d-flex justify-content-center align-items-center">
                〜
            </div>
            <div class="col-md-5">
                <input data-provide="datepicker" class="form-control datepicker js-end-date" type="datetime" placeholder="出力終了日" name="end_date" value="" dusk='datepicker_last'>
            </div>
            <label class='h4 px-3 pt-3 pb-0'>出力する際のフォーム名選択</label>
            <select class="form-control m-3" name="form_name">
                <option value="">フォーム名を選択</option>
                @foreach ($csv_options as $csv)
                <option value="{{ $csv->id }}">{{ $csv->form_name }}</option>
                @endforeach
            </select>
        </div> <!-- row -->

        <button data-remodal-action="cancel" class="btn btn-secondary">キャンセル</button>
        <button type='submit' class="btn btn-info">ダウンロード</button>
    </form>
</div>

<div class="remodal" data-remodal-id="custom-modal">
    <button data-remodal-action="close" class="remodal-close"></button>
    <h2 class='h2 mb30'>選択式CSVエクスポート</h2>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class='text-left'>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <h3 class="h3 text-left mb-2">■絞り込み条件</h3>
    <form id='csvform2' action="{{ route('contact.csv.custom-export') }}" method="POST">
        @csrf
        <div class='d-flex align-items-center p-3'>
            <h5 class='mr-3'>案件種別</h5>
            <select id="contact-types" multiple="multiple" class='multiple' name="contact_types[]" dusk="">
                @foreach($contact_types AS $ct)
                <option value="{{$ct['id']}}">{{$ct['name']}}</option>
                @endforeach
            </select>
        </div> <!-- row -->
        <div class='p-3'>
            <h5 class='text-left mb-2 mr-3'>期間（案件登録日）</h5>
            <div class='row mb-3'>
                <!--datepicker-->
                <div class="col-md-5">
                    <input data-provide="datepicker" class="form-control datepicker js-custom-start-date" type="datetime" placeholder="出力開始日" name="start_date" value="" dusk='datepicker_first'>
                </div>
                <div class="col-md-2 h5 d-flex justify-content-center align-items-center">
                    〜
                </div>
                <div class="col-md-5">
                    <input data-provide="datepicker" class="form-control datepicker js-custom-end-date" type="datetime" placeholder="出力終了日" name="end_date" value="" dusk='datepicker_last'>
                </div>
            </div> <!-- row -->
        </div>
        <div class='d-flex align-items-center p-3'>
            <h5 class='text-left mb-2 mr-3'>ステップ</h5>
            <select id="steps" multiple="multiple" class='multiple' name="steps[]" dusk="">
                <option value="fcapply">FC対応中</option>
                @foreach($steps AS $s)
                <option value="{{$s['id']}}">{{$s['name']}}</option>
                @endforeach
                <option value="notransaction">発注なし</option>
                <option value="99">失注キャンセル</option>
            </select>
        </div> <!-- row -->
        <div class='d-flex align-items-center p-3'>
            <h5 class='mb-2 mr-3'>都道府県</h5>
            <select id="prefectures" multiple="multiple" class='multiple' name="prefectures[]" dusk="">
                @foreach($prefectures AS $p)
                <option value="{{$p['id']}}">{{$p['name']}}</option>
                @endforeach
            </select>
        </div> <!-- row -->

        <div class='d-flex align-items-start my-2'>
            <h3 class="h3 text-left">■出力するデータを選択</h3>
            <button type='button' class="ml-4 btn btn-sm btn-info" id="custom-all-check">全て選択</button>
            <button type='button' class="ml-2 btn btn-sm btn-secondary" id="custom-all-out">全て解除</button>
        </div>
        <section class="d-flex flex-wrap mb-5 form-control h-auto">
            <div class="form-check form-check-inline mb-3">
                <input class="form-check-input" type="checkbox" id="custom1" value="id" name="export[]" checked>
                <label class="form-check-label f10" for="custom1">案件No</label>
            </div>
            <div class="form-check form-check-inline mb-3">
                <input class="form-check-input" type="checkbox" id="custom2" value="created_at" name="export[]" checked>
                <label class="form-check-label f10" for="custom2">案件登録日</label>
            </div>
            <div class="form-check form-check-inline mb-3">
                <input class="form-check-input" type="checkbox" id="custom3" value="contact_type_id" name="export[]" checked>
                <label class="form-check-label f10" for="custom3">問い合わせ種別</label>
            </div>
            <div class="form-check form-check-inline mb-3">
                <input class="form-check-input" type="checkbox" id="custom4" value="company_name" name="export[]" checked>
                <label class="form-check-label f10" for="custom4">会社名</label>
            </div>
            <div class="form-check form-check-inline mb-3">
                <input class="form-check-input" type="checkbox" id="custom5" value="name" name="export[]" checked>
                <label class="form-check-label f10" for="custom5">名前</label>
            </div>
            <div class="form-check form-check-inline mb-3">
                <input class="form-check-input" type="checkbox" id="custom6" value="ruby" name="export[]" checked>
                <label class="form-check-label f10" for="custom6">フリガナ</label>
            </div>
            <div class="form-check form-check-inline mb-3">
                <input class="form-check-input" type="checkbox" id="custom7" value="zipcode" name="export[]" checked>
                <label class="form-check-label f10" for="custom7">郵便番号</label>
            </div>
            <div class="form-check form-check-inline mb-3">
                <input class="form-check-input" type="checkbox" id="custom8" value="pref" name="export[]" checked>
                <label class="form-check-label f10" for="custom8">住所（都道府県）</label>
            </div>
            <div class="form-check form-check-inline mb-3">
                <input class="form-check-input" type="checkbox" id="custom21" value="city" name="export[]" checked>
                <label class="form-check-label f10" for="custom21">住所（市町村）</label>
            </div>
            <div class="form-check form-check-inline mb-3">
                <input class="form-check-input" type="checkbox" id="custom22" value="street" name="export[]" checked>
                <label class="form-check-label f10" for="custom22">住所（市町村以降）</label>
            </div>
            <div class="form-check form-check-inline mb-3">
                <input class="form-check-input" type="checkbox" id="custom9" value="tel" name="export[]" checked>
                <label class="form-check-label f10" for="custom9">電話番号</label>
            </div>
            <div class="form-check form-check-inline mb-3">
                <input class="form-check-input" type="checkbox" id="custom10" value="email" name="export[]" checked>
                <label class="form-check-label f10" for="custom10">メールアドレス</label>
            </div>
            <div class="form-check form-check-inline mb-3">
                <input class="form-check-input" type="checkbox" id="custom11" value="same_customer" name="export[]">
                <label class="form-check-label f10" for="custom11">同一顧客</label>
            </div>
            <div class="form-check form-check-inline mb-3">
                <input class="form-check-input" type="checkbox" id="custom12" value="age" name="export[]">
                <label class="form-check-label f10" for="custom12">年代</label>
            </div>
            <div class="form-check form-check-inline mb-3">
                <input class="form-check-input" type="checkbox" id="custom13" value="ground_condition" name="export[]">
                <label class="form-check-label f10" for="custom13">下地状況</label>
            </div>
            <div class="form-check form-check-inline mb-3">
                <input class="form-check-input" type="checkbox" id="custom14" value="vertical_horizontal" name="export[]">
                <label class="form-check-label f10" for="custom14">施工場所面積</label>
            </div>
            <div class="form-check form-check-inline mb-3">
                <input class="form-check-input" type="checkbox" id="custom15" value="square_meter" name="export[]">
                <label class="form-check-label f10" for="custom15">平米数</label>
            </div>
            <div class="form-check form-check-inline mb-3">
                <input class="form-check-input" type="checkbox" id="custom16" value="desired_product" name="export[]">
                <label class="form-check-label f10" for="custom16">希望商品</label>
            </div>
            <div class="form-check form-check-inline mb-3">
                <input class="form-check-input" type="checkbox" id="custom17" value="where_find" name="export[]">
                <label class="form-check-label f10" for="custom17">認知経路</label>
            </div>
            <div class="form-check form-check-inline mb-3">
                <input class="form-check-input" type="checkbox" id="custom18" value="fc_id" name="export[]">
                <label class="form-check-label f10" for="custom18">担当FC加盟店名</label>
            </div>
            <div class="form-check form-check-inline mb-3">
                <input class="form-check-input" type="checkbox" id="custom19" value="completed_at" name="export[]">
                <label class="form-check-label f10" for="custom19">施工完了日</label>
            </div>
            <div class="form-check form-check-inline mb-3">
                <input class="form-check-input" type="checkbox" id="custom20" value="quotation_id" name="export[]">
                <label class="form-check-label f10" for="custom20">決定した見積書URL</label>
            </div>
        </section>
        <button data-remodal-action="cancel" class="btn btn-secondary">キャンセル</button>
        <button type='submit' class="btn btn-info">ダウンロード</button>
    </form>
</div>

@endif

<!-- SameCustomerAjax -->
<div class="remodal w80" data-remodal-id="modal-same-customer" data-remodal-options="closeOnOutsideClick: false">
    <button data-remodal-action="close" class="remodal-close" dusk="remodal-close"></button>
    <h2 class='h2 mb30'>同一顧客一覧</h2>
    <div>
        <!-- ここにAjaxで持ってきたデータを表示 -->
        <div class="js-same-customer modal-body p30 overflow-auto"></div>
    </div>

    <div class="input-group mb-3 w-70">
        <input type="hidden" id="addSameContactId" value="">
        <input type="text" id="addSameId" class="form-control" placeholder="案件IDを入力してください" aria-label="Recipient's username" aria-describedby="same-add-button" dusk="addSameId">
        <div class="input-group-append">
            <button class="btn btn-outline-secondary" type="button" id="same-add-button">追加</button>
        </div>
    </div>
</div>


@endsection