@extends('layouts.layout') 
@section('css')
<link rel="stylesheet" href="{{ asset('plugins/trumbowyg/trum.css') }}" />
<link href="{{ asset('plugins/flatpickr/flatpickr.min.css') }}" rel="stylesheet"/>
<style>
#trumbowyg{
  background-color: #FFF;
}
</style>  
@endsection 

@section('javascript')
<script src="{{ asset('plugins/trumbowyg/trumbowyg.js') }}" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.19.1/langs/ja.min.js" defer></script>
<script src="{{ asset('plugins/trumbowyg/plugins/upload/trumbowyg.upload.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/flatpickr.js') }}" defer></script>
<script src="{{ asset('plugins/flatpickr/ja.js') }}" defer></script>
<script src="{{ asset('js/article/create.js') }}" defer></script>
@endsection

@section('content')
<div class="flex-center position-ref full-height p20">
    <div class="content">
        <div class="title m-b-md">
            <h1>お知らせ{{ !empty($id) ? "編集" : "作成" }}ページ</h1>
        </div>
      @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
      @endif
      @if(!empty($id))
        <form method="POST" action="{{ route('articles.update', ['article' => $id ]) }}" id="js-article-form">
            <input type="hidden" name="id" value="{{ $id }}" />
            <input type="hidden" id="status" name="a[status]" value="{{ $article->status }}"/>
      @else
        <form method="POST" action="{{ route('articles.store') }}" id="js-article-form">
            <input type="hidden" id="status" name="a[status]" value="1" />
      @endif
      @csrf 
            <div class="form-group">
                <label for="title">お知らせタイトル</label>
                <input type="text" class="form-control" name="a[title]" id="title" value="{{ !empty($id) ? $article->title : '' }}" placeholder="決起集会開催"/>
            </div>
            <label>お知らせ本文</label>
            <div id="trumbowyg">{!! !empty($id) ? $article->body : '' !!}</div>
            <div class="form-group">
                <label class="control-label h4">公開日時</label>
                <div class="row js-dates">
                    <div class="col-md-4">
                      <input data-provide="datepicker" class="form-control datepicker js-start-date" type="datetime" name="publish_date" value="{{ !empty($id) ? date('Y-m-d', strtotime($article->published_at)) : '' }}"/>
                    </div>
                    <div class="col-md-4">
                        <select name="publish_time" id="publish_time" class="form-control">
                            <option value="{{ date('H') }}">今すぐ投稿</option>
                        @for($i=0;$i < 24;$i++)
                            <option value="{{ $i }}">{{ $i }}時</option>
                        @endfor
                        </select>
                    </div>
                </div>
            </div>
            <button class='btn btn-secondary mr20' id="draft-submit">{{ strstr(url()->current(),'edit')==true ? '下書きに戻す' : '下書き' }}</button>
            <input class='btn btn-danger' type="submit" id="js-submit" value="{{ strstr(url()->current(),'edit')==true ? '編集を確定' : '今すぐ投稿' }}" />
        </form>
    </div>
</div>
@endsection
