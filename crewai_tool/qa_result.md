QA レビューレポート - Issue #12「大量顧客データの一括CSVインポート機能（非同期処理基盤）」

---

## 📊 総合評価：**合格 + 改善推奨**

実装はおおむね要件を満たしていますが、いくつかの重要な改善項目があります。
本番環境での安定運用のため、下記の指摘事項への対応を強く推奨します。

---

## ✅ 良かった点

### 1. **包括的なインフラストラクチャ設計**
- ✅ SQS メインキュー + DLQ（Dead Letter Queue）の分離
- ✅ 再試行ロジック（3回失敗で DLQ へ）
- ✅ CloudWatch アラーム・ログ設定による監視体制
- ✅ IAM 最小権限ポリシー（SendMessage、ReceiveMessage のみ）

### 2. **フロントエンド実装の完成度**
- ✅ ドラッグ&ドロップUI実装
- ✅ ポーリング機能で進捗リアルタイム表示
- ✅ ファイルサイズ・形式バリデーション（クライアント側）
- ✅ エラーハンドリング・アラート表示
- ✅ IIFE パターンで名前空間汚染を防止

### 3. **非同期処理フロー**
- ✅ Laravel Job による非同期化
- ✅ Cache による進捗トラッキング（24時間 TTL）
- ✅ Docker Compose にキューワーカーコンテナ追加

### 4. **セキュリティ対策**
- ✅ CSRF トークン検証
- ✅ メールアドレスバリデーション（filter_var）
- ✅ 一時ファイル自動削除
- ✅ IAM ユーザーに限定的な権限

---

## ⚠️ 問題点（重大度別）

### 【重大】1. ポーリング間隔が頻繁すぎる（JavaScript）

**ファイル:** `/public/js/contact/import.js` (Line 262)

```javascript
// ❌ 問題: 1秒ごとにステータスチェック
statusCheckInterval = setInterval(() => {
    checkStatus();
}, 1000);  // ← 頻繁すぎる
```

**影響:**
- 1000件のユーザーが同時アップロード → 1秒あたり1000リクエスト
- API サーバーへの過剰負荷
- AWS CloudFront キャッシュミス率上昇

**改善提案:**
```javascript
// ✅ 改善: 適応的なポーリング間隔
let pollInterval = 2000; // 初期: 2秒
const maxInterval = 10000; // 最大: 10秒

function startStatusPolling() {
    checkStatus();
    statusCheckInterval = setInterval(() => {
        checkStatus();
        // 処理中は短く、完了に近づくと長くする
        pollInterval = Math.min(pollInterval + 1000, maxInterval);
    }, pollInterval);
}
```

---

### 【重大】2. メモリリーク：キャッシュが24時間保持される

**ファイル:** `app/Http/Controllers/ContactController.php` (Line 61)

```php
// ❌ 問題: 24時間キャッシュが溜まり続ける
Cache::put(
    "contact_import_{$fileKey}",
    [...],
    now()->addHours(24)
);
```

**影響:**
- 1日1000件アップロード → 24000 キャッシュレコード蓄積
- Redis/Memcached メモリ枯渇リスク
- パフォーマンス低下

**改善提案:**
```php
// ✅ 改善: 完了後に自動削除、またはTTL短縮
if ($status === 'completed' || $status === 'failed') {
    $ttl = now()->addHours(1); // 1時間に短縮
} else {
    $ttl = now()->addMinutes(30); // 処理中は30分
}

Cache::put("contact_import_{$fileKey}", [...], $ttl);
```

---

### 【重大】3. JavaScript エラー時のリソースリーク

**ファイル:** `/public/js/contact/import.js` (Line 311-325)

```javascript
// ❌ 問題: エラー時にポーリングが停止しない
.catch(error => {
    console.error('Status check error:', error);
    // ← stopStatusPolling() が呼ばれていない
});
```

**影響:**
- ネットワークエラーでポーリングが永遠に続く
- ブラウザ無駄なリソース消費
- 複数回ファイル送信時にポーリングが重複

**改善提案:**
```javascript
// ✅ 改善: エラー時もポーリング停止＋リトライ上限
let errorCount = 0;
const maxErrors = 3;

.catch(error => {
    console.error('Status check error:', error);
    errorCount++;
    
    if (errorCount >= maxErrors) {
        stopStatusPolling();
        showAlert('danger', 'エラー', 'ステータス取得に失敗しました');
    }
});
```

---

### 【中度】4. CSV ヘッダーの大文字小文字区別

**ファイル:** `app/Jobs/ProcessContactImport.php` (Line 79)

```php
// ❌ 問題: ヘッダーが大文字小文字で一致する必要がある
$header = array_map('trim', $header);
$headerMap = array_flip($header);

// → "Surname" vs "surname" でマッチしない
```

**影響:**
- ユーザーが大文字でヘッダーを作成すると全件エラー
- UX 悪化

**改善提案:**
```php
// ✅ 改善: 大文字小文字を統一
$header = array_map(function($h) {
    return strtolower(trim($h));
}, $header);
```

---

### 【中度】5. ファイルバリデーションが不足（Backend）

**ファイル:** `app/Http/Requests/ContactImportRequest.php` の内容確認不可

現在の `ContactController::uploadImport()` では以下が不足：

```php
// ❌ ファイルが実際にCSV形式か検証していない
// ❌ エンコーディング確認がない（UTF-8以外のCSVで失敗）
```

**改善提案:**
```php
public function uploadImport(ContactImportRequest $request)
{
    $file = $request->file('csv_file');
    
    // ✅ MIME タイプ確認
    if (!in_array($file->getClientMimeType(), ['text/csv', 'text/plain'])) {
        return response()->json(['success' => false, 'message' => 'CSV形式ではありません']);
    }
    
    // ✅ ファイルが読み込み可能か確認
    if (!is_readable($file->getPathname())) {
        return response()->json(['success' => false, 'message' => 'ファイルが読み込めません']);
    }
}
```

---

### 【中度】6. Docker Compose キューワーカーの可用性

**ファイル:** `/compose.yaml` (Line 28-44)

```yaml
laravel.queue:
    # ❌ 問題: no restart policy
    # コンテナがクラッシュするとジョブが処理されない
    command: 'php artisan queue:work --sleep=3 --tries=3'
```

**影響:**
- キューワーカーが落ちても自動再起動されない
- インポートジョブが永遠に処理待ち状態

**改善提案:**
```yaml
laravel.queue:
    # ✅ 改善: restart policy 追加
    restart_policy:
        condition: unless-stopped
        max_retries: 5
    # ✅ ヘルスチェック追加
    healthcheck:
        test: ["CMD", "ps", "aux"]
        interval: 30s
        timeout: 10s
        retries: 3
```

---

### 【中度】7. Terraform outputs に sensitive データが表示される

**ファイル:** `/terraform/sqs.tf` (Line 139-147)

```hcl
output "iam_access_key_id" {
    description = "IAM Access Key ID (Save this securely)"
    value       = aws_iam_access_key.contact_import_sqs_key.id
    sensitive   = true  # ✓ OK
}

output "iam_secret_access_key" {
    description = "IAM Secret Access Key (Save this securely)"
    value       = aws_iam_access_key.contact_import_sqs_key.secret
    sensitive   = true  # ✓ OK
}
```

**改善提案:**
```hcl
# ✅ より厳格: 秘密情報は terraform.tfstate に保存するだけ、出力しない
output "iam_user_name" {
    description = "IAM User name for SQS access"
    value       = aws_iam_user.contact_import_sqs_user.name
    # sensitive は不要
}

# ⚠️ Access Key は AWS Secrets Manager に保存すべき
resource "aws_secretsmanager_secret" "sqs_credentials" {
    name = "${local.name_prefix}/sqs-credentials"
}

resource "aws_secretsmanager_secret_version" "sqs_credentials" {
    secret_id = aws_secretsmanager_secret.sqs_credentials.id
    secret_string = jsonencode({
        access_key_id     = aws_iam_access_key.contact_import_sqs_key.id
        secret_access_key = aws_iam_access_key.contact_import_sqs_key.secret
    })
}
```

---

### 【軽度】8. ファイル削除時のエラーハンドリング

**ファイル:** `app/Jobs/ProcessContactImport.php` (Line 205)

```php
// ❌ 削除失敗時の処理がない
if (file_exists($this->filePath)) {
    unlink($this->filePath);  // ← 失敗しても無視
}
```

**改善提案:**
```php
// ✅ 改善: 削除失敗をログに記録
if (file_exists($this->filePath)) {
    try {
        unlink($this->filePath);
    } catch (\Exception $e) {
        Log::warning('Failed to delete import file', [
            'file' => $this->filePath,
            'error' => $e->getMessage(),
        ]);
    }
}
```

---

### 【軽度】9. JavaScript の CSRF トークン取得が脆弱

**ファイル:** `/public/js/contact/import.js` (Line 266)

```javascript
// ❌ meta タグが存在しない場合を想定していない
formData.append('_token', 
    document.querySelector('meta[name="csrf-token"]').getAttribute('content')
);
```

**改善提案:**
```javascript
// ✅ 改善: null チェック
const csrfToken = document.querySelector('meta[name="csrf-token"]');
if (!csrfToken) {
    showAlert('danger', 'エラー', 'CSRF トークンが見つかりません');
    return;
}
formData.append('_token', csrfToken.getAttribute('content'));
```

---

### 【軽度】10. .env.example の SQS_PREFIX が曖昧

**ファイル:** `/.env.example`

```bash
# ❌ テンプレートが曖昧
SQS_PREFIX=https://sqs.ap-northeast-1.amazonaws.com/YOUR_ACCOUNT_ID
SQS_QUEUE=sales-management-dev-contact-import-queue
```

**改善提案:**
```bash
# ✅ 改善: コメントで説明を追加
# AWS Account ID を確認: aws sts get-caller-identity --query Account
SQS_PREFIX=https://sqs.${AWS_DEFAULT_REGION}.amazonaws.com/${AWS_ACCOUNT_ID}
# SQS Queue 名は terraform apply 後に出力される値を使用
SQS_QUEUE=${AWS_RESOURCE_PREFIX}-contact-import-queue
```

---

## 📋 テスト実施チェックリスト

| テスト項目 | ステータス | 備考 |
|-----------|----------|------|
| 正常系CSV（必須項目全て） | 未実施 | 5件～100件でテスト |
| 空のCSV | 未実施 | ヘッダーのみでエラー確認 |
| 不正フォーマット | 未実施 | ヘッダー不足でバリデーション確認 |
| 大量データ（1000件以上） | 未実施 | バッチ処理・メモリ使用量確認 |
| ポーリング停止確認 | 未実施 | ブラウザのネットワークタブで確認 |
| エラー時の復旧 | 未実施 | ネットワーク切断→復帰の動作確認 |
| SQS メッセージ確認 | 未実施 | AWS SQS コンソールで確認 |
| DLQ メッセージ | 未実施 | 意図的に失敗させて DLQ 確認 |
| キャッシュ消費量 | 未実施 | Redis `INFO memory` で監視 |
| ファイルディスク残量 | 未実施 | 削除漏れがないか確認 |

---

## 🔧 改善アクション（優先度別）

### **P1（Critical）- 本番化前に必須**
1. ❌ ポーリング間隔を 1秒 → 2-10秒に変更
2. ❌ キャッシュ TTL を 24時間 → 1-2時間に短縮
3. ❌ JavaScript エラー時の例外処理を追加
4. ❌ Docker Compose キューワーカーの restart policy 追加

### **P2（High）- 近日対応**
5. ❌ CSV ヘッダーを小文字統一
6. ❌ Backend ファイルバリデーション強化
7. ❌ ファ