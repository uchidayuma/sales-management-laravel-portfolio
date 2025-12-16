@extends('layouts.layout')

@section('css')
<link href="{{ asset('') }}" rel="stylesheet" />
@endsection

@section('javascript')
<script src="{{ asset('js/setting/csv-export-option.js?20210908') }}" defer></script>
@endsection

@section('content')

<p class="h2 my-3">CSVフォーム名オプション</p>

<div class="input-group mb-3 w-50">
    <input type="text" class="form-control" placeholder="追加したいフォーム名を入力してください" id="addScvFormData" dusk="form-input">
    <div class="input-group-append">
        <button class="btn btn-outline-secondary" type="button" id="addCsvForm" dusk="form-submit">追加</button>
    </div>
</div>
  
<table class="common-table-stripes-row">
    <thead class="common-table-stripes-row-thead">
            <tr>
                <th scope="col">フォーム名</th>
                <th scope="col" class="text-right pr-5">削除</th>
            </tr>
    </thead>
    <tbody>
        @foreach($csv_options as $c)
        <tr>
            <td scope="row" class="py-3">{{ $c->form_name }}</td>
            <td scope="row" class="text-right px-3"><button  class="btn btn-danger px-4" type="button" id="deleteCsvForm" csv_option_id="{{ $c->id }}" dusk="form-delete">削除</button></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
