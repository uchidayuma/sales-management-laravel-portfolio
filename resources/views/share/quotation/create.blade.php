@extends('layouts.layout')

@section('before-bootstrap-css')
<link href="{{ asset('plugins/remodal/remodal.css') }}" rel="stylesheet" />
<link href="{{ asset('plugins/remodal/remodal-default-theme.css') }}" rel="stylesheet" />
@endsection

@section('css')
<link href="{{ asset('plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet"/>
<link href="{{ asset('plugins/dropzone/basic.css') }}" rel="stylesheet" />
<link href="{{ asset('plugins/dropzone/dropzone.css') }}" rel="stylesheet" />
<link href="{{ asset('styles/quotation/create.min.css?20230125') }}" rel="stylesheet" />
<link href="{{ asset('styles/quotation/material-create.min.css?20230125') }}" rel="stylesheet" />
@endsection

@section('javascript')
<script src="{{ asset('plugins/flatpickr/flatpickr.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/ja.js') }}" defer></script>
<script src="{{ asset('js/jquery/jquery-ui.min.js') }}" defer></script>
<script src="{{ asset('js/jquery/touch-punch.js') }}" defer></script>
<script src="{{ asset('plugins/remodal/remodal.min.js') }}" defer></script>
<script src="{{ asset('plugins/dropzone/dropzone.js') }}" defer></script>
  <script src="{{ asset('js/quotation/create.js?20230223') }}" defer></script>
  <script src="{{ asset('js/quotation/material-create.js?20230223') }}" defer></script>
<script src="{{ asset('js/quotation/helper.js?20210730') }}" defer></script>
<script>
  window.products = @json($products);
  window.allProducts = @json($allProducts);
  window.pq =@json(old('pq') ? old('pq') : []);
  window.subTotal = 0;
  window.quotations = @json($base_quotation);
  // ここから材料見積もり用のデータ
  window.turfs = @json($turfs);
  window.subItems = @json($subItems);
  window.cutItems = @json($cutItems);
  window.pt =@json(old('pt') ? old('pt') : []);
  window.discount = 0;
  window.quotationType = {{ $quotation_type }};
  window.isCopy = {{ $is_copy}};
  window.user = @json(\Auth::user());
  window.appEnv = "{{ \App::environment() }}";
  window.quotationTaxOption = @json($quotation_tax_option);
</script>
@if($is_copy)
<script>
  window.quotations = @json($base_quotation)
</script>
@endif
@endsection

@section('content')
{!! $breadcrumbs->render() !!}
<ul class="nav nav-tabs nav-justified" id="quotation-tabs" role="tablist">
  <li class="nav-item">
    <a class="nav-link active f10" id="nomal-tab" data-toggle="pill" href="#nomal" role="tab" aria-controls="nomal" aria-selected="true" dusk='constract-tab'>施工見積もり</a>
  </li>
  <li class="nav-item">
    <a class="nav-link f10" id="material-tab" data-toggle="pill" href="#material" role="tab" aria-controls="material" aria-selected="false" dusk='material-tab'>材料販売見積もり</a>
  </li>
</ul>

<!-- 見積もり項目テーブル -->
<div class="card bg-white">
@if ($errors->any())
  <div class="alert alert-danger">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <ul>
  @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
  @endforeach
    </ul>
  </div>
@endif
  <!-- 施工見積もり -->
  <div class="tab-content" id="tab-content">
    <div class="tab-pane fade show active" id="nomal" role="tabpanel" aria-labelledby="nomal-tab">
    <button id="upload-modal-open" class="btn btn-secondary" dusk="">見積書アップロード</button>
      @include('share.quotation.nomal-create')
    </div><!-- tab-pane -->
    <div class="tab-pane fade" id="material" role="tabpanel" aria-labelledby="material-tab">
      @include('share.quotation.material-create')
    </div><!-- tab-pane -->
  </div><!-- tab-content -->
</div><!-- card -->

<!-- 見積書アップモーダル -->
<div class="remodal w80" data-remodal-id="upload-modal" data-remodal-options="closeOnOutsideClick: false">
  <button data-remodal-action="close" class="remodal-close" dusk="remodal-close"></button>
  <h2 class='h2 mb30'>見積書アップロード</h2>
  <form action="{{ route('quotations.ajax.parse') }}" method="POST" class="dropzone dz-clickable" id="dropzone" enctype="multipart/form-data">
    @csrf
    <input type="hidden" class="js-contact-id-dropzone" name="contact_id" value="">
    <input type="file" name="file" hidden="">
    <div class="dz-default dz-message"><button class="dz-button" type="button">ここをクリックか、ドラッグ＆ドロップしてください</button></div>
  </form>
</div>



@endsection
