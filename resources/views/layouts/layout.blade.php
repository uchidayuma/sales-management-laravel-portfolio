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
  <link href="{{ asset('styles/layout.min.css') }}" rel="stylesheet">
  <link href="{{ asset('styles/common.min.css?20230418') }}" rel="stylesheet">

  @yield('css')

  <!-- jQuery -->
  <script src="{{ asset('js/jquery/jquery-3.4.1.min.js') }}" defer></script>

  <script src="{{ asset('js/app.js') }}" defer></script>
  <script src="{{ asset('js/common.js') }}" defer></script>
  <script src="{{ asset('js/jquery/jquery-ui.min.js') }}" defer></script>
  @if(config('app.user_notice'))
  <script src="{{ asset('js/notification.js') }}" defer></script>
  @endif
  <script src="{{ asset('js/sidebar.js') }}" defer></script>
  <script src="{{ asset('plugins/chartjs/chart.js') }}"></script>

  @yield('javascript')
</head>

<body>
  <div id="app">
    <header class="header flex align-items-center">
      <div id="nav-drawer" class="mr-5 pl-3 pb-2 block-sidebar-hide block-sidebar">
        <input id="nav-input" type="checkbox" class="nav-unshown">
        <label id="nav-open" for="nav-input"><span></span></label>
        <label class="nav-unshown" id="nav-close" for="nav-input"></label>
        <div id="nav-content">
          <aside class="aside">

            @if(isAdmin())
            @include('layouts.admin-sidebar')
            @else
            @include('layouts.fc-sidebar')
            @endif
          </aside>

        </div>
      </div>
      <div class="d-none d-sm-block">
        <a href="{{ route('dashboard') }}" class="header-left flex">
          <!-- <img class="header-left__shintou-logo" src="{{ asset('images/logo.jpg') }}" alt="logo" /> -->
          <h1 class="header-left__title shintou-logo">サンプルFC管理システム</h1>
        </a>
      </div>
      <div class="header-right flex justify-content-around align-items-center w35 d-none d-sm-flex">
        <p class="header-right__company bold">{{ $user->company_name }}</p>
        <a href="{{ isAdmin() ? 'https://docs.google.com/presentation/d/1G-oL51yqUegTRm3Vh2ndtS7mGfB3hnqPwsOUevFDTjk/edit?usp=sharing' : 'https://docs.google.com/presentation/d/1BtGmhSfNIZEaQQOuQW3_lBEiPLzzT9XHCdl7uFfuwVs/edit?usp=sharing' }}" target='blank' class='header-right__icon-wrapper d-flex justify-content-center align-items-center'>
          <i class="header-right__icon fas fa-question-circle"></i>
        </a>
        <div class='header-right__icon-wrapper d-flex justify-content-center align-items-center header-right__circle js-info-circle' dusk='notification-btn'>
          <i class="header-right__icon fas fa-info-circle"></i>
          <div class='info-circle-contents js-info-circle-contents'>
            <a class='info-circle-contents__item js-info-circle-contents__item js-no-notification'>新着通知はありません。</a>
            <a href="{{ route('notifications.index') }}" class='info-circle-contents__item js-info-circle-contents__item'>通知一覧へ</a>
          </div>
        </div>
        <a href="https://test.com/1ypr1iIAaIGDmHO64RNCKC4fd5KMf-9sl" target='blank' class='header-right__icon-wrapper d-flex justify-content-center align-items-center'>
          <i class="header-right__icon fas fa-file-download"></i>
        </a>
        <!--ユーザーアイコン(モーダル内にプロフィール変更・ログアウトリンクを表示する)-->
        <button type="button" id="icon_button" class="header-right__icon-wrapper header-right__icon-wrapper--square d-flex justify-content-center align-items-center" data-toggle="modal" data-target="#userInfoModal">
          <i class="header-right__icon fas fa-user"></i>
        </button>
      </div><!-- header-right -->
      <!-- スマホサイズ header ここから -->
      <div class="w50 ml30 d-block d-sm-none">
        <a class="text-dark" href="{{ route('dashboard') }}">
          <p class="header-right__company bold">{{ $user->company_name }}</p>
        </a>
      </div>
      <div class="header-right d-block d-sm-none mr10">
        <!--ユーザーアイコン(モーダル内にプロフィール変更・ログアウトリンクを表示する)-->
        <button type="button" id="icon_button" class="header-right__icon-wrapper header-right__icon-wrapper--square d-flex justify-content-center align-items-center" data-toggle="modal" data-target="#userInfoModal">
          <i class="header-right__icon fas fa-user"></i>
        </button>
      </div>
      <!-- スマホサイズ header ここまで -->

      <!-- Modal -->
      <div class="modal fade" id="userInfoModal" tabindex="-1" role="dialog" aria-labelledby="userInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="userInfoModalLabel">ユーザーID: {{ $user->id }}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              @if($user->role == 1)
              ユーザー名：{{ $user->company_name }}
              @else($user->role == 2)
              FC名：{{ $user->company_name }}
              @endif

            </div>
            <div class="modal-footer">
              <a href=" {{ route('users.edit', ['id' => $user->id ] ) }} "><button type="button" class="btn btn-secondary">プロフィールを変更</button>
                <a href=" {{ route('users.logout') }} "><button type="button" class="btn btn-primary" id="logout">ログアウト</button></a>
            </div>
          </div>
        </div>
      </div>

    </header>

    <main class="main">
      <aside class="aside none-sidebar">

        @if(isAdmin())
        @include('layouts.admin-sidebar')
        @else
        @include('layouts.fc-sidebar')
        @endif
      </aside>
      <article id='article' class="article">
        @include('layouts.flash-messages')
        @include('layouts.js-alerts')
        @yield('content')
      </article>
    </main>

    <footer class="footer flex flex-center align-items-center">
      <p class="footer__copyright">{{ date('Y') }} サンプルFC.All Rights Reserved.</p>
      @yield('footer')
    </footer>

  </div>

</body>

</html>