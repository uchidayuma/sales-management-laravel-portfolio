
@extends('layouts.layout')

@section('css')
<link href="{{ asset('styles/articles/create.min.css') }}" rel="stylesheet" />
<link href="{{ asset('styles/dashboard.min.css') }}" rel="stylesheet" />
@endsection
@section('content')
    <h1>現在、当システムは閲覧のみに制限されています。</h1>
    <p>データの登録などは2024年9月1日から新システムに移行されます。</p>
    <a href="https://samplefc.local/new-system">新システムはこちらです。</a>
@endsection('content')
