@extends('layouts.layout')

@section('css')
<link href="{{ asset('styles/articles/detail.min.css') }}" rel="stylesheet" />
@endsection
@section('javascript')
@endsection
@section('content')

{!! $breadcrumbs->render() !!}

<!-- nav部分 -->
<div class="flex-center position-ref full-height">
  <div class="content">
    <ul id="myTab" class="nav nav-tabs mb-0" role="tablist">
      <li class="nav-item">
        <a href="#home" id="home-tab" class="nav-link nav-item__public active" role="tab" data-toggle="tab" aria-controls="home" aria-selected="true">詳細</a>
      </li>
  @if(isAdmin())
      <li class="nav-item">
        <a href="#profile" id="profile-tab" class="nav-link nav-item__private" role="tab" data-toggle="tab" aria-controls="profile" aria-selected="false">既読FCリスト</a>
      </li>
      <li class="nav-item">
        <a href="#contact" id="contact-tab" class="nav-link nav-item__draft" role="tab" data-toggle="tab" aria-controls="contact" aria-selected="false">未読FCリスト</a>
      </li>
  @endif
    </ul>

    <div id="myTabContent" class="tab-content">

      <!-- 本文部分 -->
      <div id="home" class="tab-pane active main-content" role="tabpanel" aria-labelledby="home-tab">
        <!-- <main class="main-content d-flex flex-column"> -->
          <h1 class='mb30'>{{ $article->title }}</h1>
          <!-- <div class="p-4 mt-3 main-content__body"> -->
            {!! $article->body !!}
          <!-- </div> -->
          <p class='mb50'></p>
          <a type="button" class="btn btn-outline-primary mt-auto mx-auto btn-lg text-center" href="{{ route('articles.index') }}"><i class="fas fa-chevron-circle-left"></i>お知らせ一覧へ戻る</a>
        <!-- </main> -->
      </div>

      <!-- 既読FCリスト -->
      <div id="profile" class="tab-pane" role="tabpanel" aria-labelledby="profile-tab" {{adminOnlyHidden()}}>
        <main class="main-content d-flex flex-column">
          <h1 class='common'>既読FCリスト</h1>
          <ul class="main-content__ul main-content__body mt-3">
    @foreach($readFcs AS $fc)
            <li class="flex justify-content-between">
              <p><i class="fas fa-chevron-circle-right mr-3"></i>{{ $fc->company_name }}</p>
              <a href="{{ route('users.show', ['id' => $fc->id]) }}" class="btn btn-info px-5 my-1 mr-5" target="blank" dusk='detail-button'>FC詳細<i class="fas fa-external-link-alt ml-2 color-white"></i></a>
            </li>
    @endforeach
          </ul>
          <a type="button" class="btn btn-outline-primary mt-auto mx-auto btn-lg text-center" href="{{ route('articles.index') }}"><i class="fas fa-chevron-circle-left"></i>お知らせ一覧へ戻る</a>
        </main>
      </div>

      <!-- 未読FCリスト -->
      <div id="contact" class="tab-pane" role="tabpanel" aria-labelledby="contact-tab" {{adminOnlyHidden()}}>
        <main class="main-content d-flex flex-column">
          <h1 class='common'>未読FCリスト</h1>
          <ul class="main-content__ul main-content__body mt-3">
    @foreach($noReadFcs AS $fc)
            <li class="flex justify-content-between">
              <p><i class="fas fa-chevron-circle-right mr-3"></i>{{ $fc->company_name }}</p>
              <a href="{{ route('users.show', ['id' => $fc->id]) }}" class="btn btn-info px-5 my-1 mr-5" target="blank" dusk='detail-button'>FC詳細<i class="fas fa-external-link-alt ml-2 color-white"></i></a>
            </li>
    @endforeach
          </ul>
          <a class="btn btn-outline-primary mt-auto mx-auto btn-lg text-center d-block" href="{{ route('articles.index') }}"><i class="fas fa-chevron-circle-left"></i>お知らせ一覧へ戻る</a>
        </main>
      </div>

    </div>

  </div><!-- content -->
</div>
@endsection
