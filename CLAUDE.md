# Sales Management Laravel

フランチャイズ営業管理システム。顧客・見積・受注・請求・分析を一元管理。

## 非標準の規約

- 変数名は camelCase（Laravel標準の snake_case ではない）→ `/variables-rule` 参照
- テストメソッド名は日本語（`test_見積作成で合計金額が正しく計算される`）
- コミットメッセージは日本語OK

## 既知の技術的負債

- `ContactController.php`（71KB/1500行超）と `Contact.php`（107KB）が肥大化。変更時は影響範囲を必ず確認し、新規ロジックはServiceに切り出す
- Facadeの直接使用（`Auth::`, `Mail::`）が残存。新規コードではDI優先

## 守るべきルール

- ビジネスロジックはControllerに書かない → Serviceレイヤーに分離
- N+1禁止。`with()` でEager Loading
- ユーザー入力は必ずFormRequestでバリデーション。Controller内のinlineバリデーション禁止
- 生SQL禁止。Eloquent/クエリビルダのバインディングを使う
- Bladeで `{!! !!}` は原則使わない（XSS防止）

## 開発環境

```bash
docker compose up -d && docker compose exec app bash
```

## MCP

MySQL MCPサーバー接続済み（`.mcp.json`）。DBスキーマやデータを直接参照可能。
