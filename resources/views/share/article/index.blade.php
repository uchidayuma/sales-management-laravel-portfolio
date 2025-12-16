@extends('layouts.layout')

@section('css')
<link href="{{ asset('styles/articles/create.min.css') }}" rel="stylesheet" />
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
        <a href="#home" id="home-tab" class="nav-link active nav-item__public" role="tab" data-toggle="tab" aria-controls="home" aria-selected="true">公開済みお知らせ一覧</a>
      </li>
  @if(isAdmin())
      <li class="nav-item">
        <a href="#profile" id="profile-tab" class="nav-link nav-item__private" role="tab" data-toggle="tab" aria-controls="profile" aria-selected="false">公開前お知らせ一覧</a>
      </li>
      <li class="nav-item">
        <a href="#contact" id="contact-tab" class="nav-link nav-item__draft" role="tab" data-toggle="tab" aria-controls="contact" aria-selected="false">下書き一覧</a>
      </li>
  @endif
    </ul>

    <!-- パネル部分 -->
    <div id="myTabContent" class="tab-content mt-0 mb-4">
      <div id="home" class="tab-pane active" role="tabpanel" aria-labelledby="home-tab">
        <table class='blog-table'>
    @foreach($articles AS $a)
          <tr class="blog-table-tr">
            <td>{{ date('Y年m月d日', strtotime($a->published_at)) }}</td>
            <td class="w50">{{ $a->title }}</td>
            <td class="text-right"><a href="{{ route('articles.show', ['article' => $a->id]) }}" class='btn btn-info px-3'>詳細を見る</a></td>
            <td class="text-right"><a href="{{ route('articles.edit', ['article' => $a->id]) }}" class='btn btn-primary px-3' {{adminOnlyHidden()}}>編集</a></td>
            <td class="text-right"><a href="{{ route('articles.destroy', ['article' => $a->id]) }}" class='btn btn-danger px-3' onclick="return confirm('お知らせを削除します。よろしいですか？')" {{adminOnlyHidden()}}>削除</a></td>
          </tr>
    @endforeach
        </table>
      </div>
      <div id="profile" class="tab-pane" role="tabpanel" aria-labelledby="profile-tab">
        <table class='blog-table'>
    @foreach($privateArticles AS $a)
          <tr class="blog-table-tr">
            <td>{{ date('Y年m月d日', strtotime($a->published_at)) }}</td>
            <td class="w50">{{ $a->title }}</td>
            <td class="text-right"><a href="{{ route('articles.show', ['article' => $a->id]) }}" class='btn btn-info px-3'>詳細を見る</a></td>
            <td class="text-right"><a href="{{ route('articles.edit', ['article' => $a->id]) }}" class='btn btn-primary px-3' {{adminOnlyHidden()}}>編集</a></td>
            <td class="text-right"><a href="{{ route('articles.destroy', ['article' => $a->id]) }}" class='btn btn-danger px-3' onclick="return confirm('お知らせを削除します。よろしいですか？')" {{adminOnlyHidden()}}>削除</a></td>
          </tr>
    @endforeach
        </table>
      </div>
      <div id="contact" class="tab-pane" role="tabpanel" aria-labelledby="contact-tab">
        <table class='blog-table'>
    @foreach($draftArticles AS $a)
          <tr class="blog-table-tr">
            <td>{{ date('Y年m月h日', strtotime($a->published_at)) }}</td>
            <td class="w50">{{ $a->title }}</td>
            <td class="text-right"><a href="{{ route('articles.show', ['article' => $a->id]) }}" class='btn btn-info px-3'>詳細を見る</a></td>
            <td class="text-right"><a href="{{ route('articles.edit', ['article' => $a->id]) }}" class='btn btn-primary px-3' {{adminOnlyHidden()}}>編集</a></td>
            <td class="text-right"><a href="{{ route('articles.destroy', ['article' => $a->id]) }}" class='btn btn-danger px-3' onclick="return confirm('お知らせを削除します。よろしいですか？')" {{adminOnlyHidden()}}>削除</a></td>
          </tr>
    @endforeach
        </table>
      </div>
    </div>
{{ $articles->appends(request()->query())->links() }}
  </div>
</div>
@endsection
