<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex">
    <title>{{ !empty($pagetitle) ? $pagetitle : config('app.name', 'サンプルFC管理アプリ') }}</title>

    @yield('before-bootstrap-css')
    <!-- Styles -->
    <link href="{{ asset('styles/fontawesome/fontawesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('styles/app.min.css') }}" rel="stylesheet">
    <link href="{{ asset('styles/common.min.css') }}" rel="stylesheet">
    @yield('css')

    <!-- jQuery -->
    <script src="{{ asset('js/jquery/jquery-3.4.1.min.js') }}" defer></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/jquery/jquery-ui.min.js') }}" defer></script>

    {{-- javascript --}}
    <script src="{{ asset('js/simulator/create.js') }}" defer></script>

    @yield('javascript')
</head>
<body>

<div>
    <h2 class='ml10 mt10'>Layout Simulator</h2>
    <div class='d-flex mt20'>
        <div class='w15 mr10 ml10'>
            <h4 class='mb10'>サイズを入力</h3>
            <div class='d-flex'>
              <input type="text" class='w50 mr10' id="img-width" placeholder="横">
              <input type="text" class='w50' id="img-height" placeholder="縦">
            </div>
            <h4 class='mb10 mt10'>芝を選択</h3>
            <input type="button" class="btn-primary w80 mb5" value="サンプル芝30mm" onclick="setTurfSample(0)"/>
            <input type="button" class="btn-primary w80 mb5" value="サンプル芝40mm" onclick="setTurfSample(1)"/>
        </div>
        <div>
            <canvas id="canvas" width="1000px" height="1000px" style="border:1px solid #000"></canvas>
            <div>
                <button id="start_btn" onclick="drowLayout()">スタート</button>
                <button id="end_btn">完了</button>
                {{-- スタート押されてなかったらクリア押せないようにしたい --}}
                <button id="clear_btn" onclick="clearLayout()">クリア</button>
            </div>
        </div>
    </div>
</div>

</body>
</html>
