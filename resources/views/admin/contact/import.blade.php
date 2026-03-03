@extends('layouts.layout')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">
                <i class="fas fa-upload"></i> 顧客データCSVインポート
            </h2>

            <!-- Info Alert -->
            <div class="alert alert-info" role="alert">
                <h4 class="alert-heading">CSVファイルのフォーマット</h4>
                <p>以下のヘッダーを含むCSVファイルをアップロードしてください。</p>
                <ul class="mb-0">
                    <li><strong>必須項目:</strong> surname（姓）, name（名）, company_name（会社名）, pref（都道府県）, city（市区町村）, street（住所）, tel（電話番号）</li>
                    <li><strong>オプション項目:</strong> email（メール）, zipcode（郵便番号）, surname_ruby（姓ルビ）, name_ruby（名ルビ）</li>
                </ul>
                <hr>
                <p class="mb-0">
                    <a href="{{ asset('samples/contacts_import_template.csv') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-download"></i> テンプレートをダウンロード
                    </a>
                </p>
            </div>

            <!-- File Upload Form -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">ファイルアップロード</h5>
                </div>
                <div class="card-body">
                    <form id="csvUploadForm" enctype="multipart/form-data" action="{{ route('contact.import.upload') }}" method="POST">
                        @csrf
                        
                        <!-- Dropzone Area -->
                        <div id="dropzoneArea" class="border-2 border-dashed rounded-lg p-8 text-center mb-4 bg-light" style="min-height: 200px; cursor: pointer; border-color: #dee2e6;">
                            <div class="py-5">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <h5>CSVファイルをドラッグ&ドロップ</h5>
                                <p class="text-muted">または下のボタンをクリック</p>
                                <input type="file" id="csvFileInput" name="csv_file" class="d-none" accept=".csv,.txt" />
                                <button type="button" id="selectFileBtn" class="btn btn-primary mt-3">
                                    <i class="fas fa-folder-open"></i> ファイルを選択
                                </button>
                            </div>
                        </div>

                        <!-- Selected File Info -->
                        <div id="fileInfo" class="alert alert-secondary" style="display: none;">
                            <strong>選択中のファイル:</strong> <span id="fileName"></span>
                            <span id="clearFile" class="float-right text-danger" style="cursor: pointer;">
                                <i class="fas fa-times-circle"></i> 削除
                            </span>
                        </div>

                        <!-- Validation Messages -->
                        @if ($errors->has('csv_file'))
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle"></i>
                                @foreach ($errors->get('csv_file') as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        @endif

                        <!-- Upload Button -->
                        <button type="submit" id="uploadBtn" class="btn btn-success btn-lg btn-block" style="display: none;">
                            <i class="fas fa-upload"></i> アップロード開始
                        </button>
                    </form>
                </div>
            </div>

            <!-- Progress Area -->
            <div id="progressArea" class="card mt-4" style="display: none;">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">処理中...</h5>
                </div>
                <div class="card-body">
                    <div class="progress mb-3">
                        <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" 
                             role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>
                    <p id="progressMessage" class="text-center text-muted">ファイルを受け付けました</p>
                </div>
            </div>

            <!-- Status Area -->
            <div id="statusArea" class="card mt-4" style="display: none;">
                <div class="card-header" id="statusHeader">
                    <h5 class="mb-0" id="statusTitle"></h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>処理結果:</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check-circle text-success"></i> 成功: <strong id="successCount">0</strong> 件</li>
                                <li><i class="fas fa-exclamation-circle text-danger"></i> エラー: <strong id="errorCount">0</strong> 件</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>ステータス:</h6>
                            <p id="statusMessage" class="text-muted"></p>
                        </div>
                    </div>
                    <hr>
                    <button type="button" id="resetBtn" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> もう一度アップロード
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS for Dropzone -->
<style>
    .border-2 {
        border-width: 2px !important;
    }
    
    .border-dashed {
        border-style: dashed !important;
    }
    
    #dropzoneArea.drag-over {
        background-color: #e3f2fd !important;
        border-color: #2196F3 !important;
    }
    
    .float-right {
        float: right;
    }
    
    @media (max-width: 768px) {
        .row > .col-md-6 {
            margin-bottom: 15px;
        }
    }
</style>

<!-- JavaScript - Import Script -->
<script src="{{ asset('js/contact/import.js') }}"></script>
@endsection
