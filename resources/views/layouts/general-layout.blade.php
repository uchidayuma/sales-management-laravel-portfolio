<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ !empty($pagetitle) ? $pagetitle : config('app.name', 'サンプルFC管理アプリ') }}</title>

  <!-- Styles -->
  <link href="{{ asset('styles/app.min.css') }}" rel="stylesheet">
  <link href="{{ asset('styles/layout.min.css') }}" rel="stylesheet">
  @yield('css')
  
  <!-- Scripts -->
  <script src="{{ asset('js/app.js') }}" defer></script>
  @yield('javascript')
</head>
<body>
  <div id="app">
    <header class="">
    </header>

    <main class="py-4">
      <div class='container'>
        @include('layouts.flash-messages')
        @yield('content')
      </div>
    </main>
  </div>
    
</body>
</html>
