---
name: code-review
description: |
  Expert code review with SOLID, security, performance, and error handling checks.
  GitHub issue/PRと連携し、要件充足も検証する。
  Use when user asks for "code review", "review this PR", "check my code", or "review changes"
---

# Code Review Expert

専門的なコードレビューを実施します。GitHub issue/PRと連携して要件充足を検証し、SOLID原則、セキュリティ、パフォーマンス、エラーハンドリング、境界条件をチェックし、プロジェクト固有のコンベンションにも準拠しているか確認します。

## 入力パターン

このスキルは以下の3パターンの入力に対応する:

### パターンA: issue URL + PR URL
```
/code-review
issue: https://github.com/owner/repo/issues/123
pr: https://github.com/owner/repo/pull/456
```

### パターンB: issue URL + ローカルdiff
```
/code-review
issue: https://github.com/owner/repo/issues/123
```
→ PR URLがない場合はローカルの `git diff` を使用

### パターンC: 要件テキスト直接入力 + PR URLまたはローカルdiff
```
/code-review
要件: ユーザーのプロフィール画像をアップロードできるようにする。最大5MB、jpg/png対応。
pr: https://github.com/owner/repo/pull/456
```
→ 要件テキストが直接渡された場合はそれを使用

**入力が何も指定されていない場合**: ユーザーに「issue URLまたは要件テキスト」と「PR URLまたはローカルdiff」のどちらを使うか確認する。

## ワークフロー

### Step 0: 入力の解析と要件の取得

**issue URLが渡された場合:**
1. `gh issue view <number> --repo <owner/repo>` でissueの内容を取得
2. issueのタイトル、本文、ラベル、コメントから要件を抽出
3. Acceptance Criteria（受け入れ条件）があれば特に注目する

**要件テキストが直接渡された場合:**
1. テキストをそのまま要件として使用

**取得した要件を以下の形式で整理:**
- 機能要件（何を実現すべきか）
- 非機能要件（パフォーマンス、セキュリティ等の制約）
- 受け入れ条件（具体的な完了基準）
- スコープ外（明示的に除外されているもの）

### Step 1: 変更内容の取得

**PR URLが渡された場合:**
1. `gh pr view <number> --repo <owner/repo>` でPRの概要を取得
2. `gh pr diff <number> --repo <owner/repo>` でdiffを取得
3. PRのdescription、コメント、レビューコメントも確認

**PR URLがない場合:**
1. `git diff --staged` でステージ済みの変更を確認
2. ステージ済みがなければ `git diff` で未ステージの変更を確認
3. 変更されたファイルをリストアップ

### Step 2: プロジェクトコンテキストの確認
1. `references/project-context.md` を読み込む
2. プロジェクト固有のコンベンションを把握する
3. 変更対象ファイルと類似した既存ファイルを2-3個探して読む
4. 既存のパターンを理解する

### Step 3: 要件充足チェック
issueまたは要件テキストから抽出した要件と、実際の変更内容を突き合わせる:

- **実装済み**: 要件を満たしているもの
- **未実装**: 要件に含まれるが実装されていないもの
- **部分実装**: 一部のみ実装されているもの
- **スコープ外の変更**: 要件にないが追加されているもの

各項目について、具体的にどのファイルのどの部分が該当するか示す。

### Step 4: プロジェクト適合性チェック
以下の観点で既存コードと比較:
- ファイル命名規則に従っているか
- インポート順序は既存と一致しているか
- エラーハンドリングパターンは既存と一致しているか
- 既存の類似ファイルと同じパターンを使っているか

具体的に「既存の○○.tsと同じパターンを使うべき」と指摘する。

### Step 5: SOLID + Architecture
`references/solid-checklist.md` を参照してチェック:
- Single Responsibility Principle違反
- Open/Closed Principle違反
- Liskov Substitution Principle違反
- Interface Segregation Principle違反
- Dependency Inversion Principle違反

### Step 6: Removal Candidates
`references/removal-plan.md` を参照:
- デッドコード・未使用コードを特定
- 安全な削除計画を提案

### Step 7: Security Scan
`references/security-checklist.md` を参照:
- XSS、インジェクション、SSRF、競合状態
- 認証・認可の抜け穴
- シークレット漏洩

### Step 8: Code Quality
`references/code-quality-checklist.md` を参照:
- エラーハンドリング（swallowed exceptions、async errors）
- パフォーマンス（N+1クエリ、キャッシュ欠落）
- 境界条件（null処理、空コレクション、off-by-one）

### Step 9: Output
以下の形式で結果を出力:

## コードレビュー結果

### 要件充足状況

| 要件 | ステータス | 備考 |
|------|-----------|------|
| [issueから抽出した要件1] | 実装済み / 未実装 / 部分実装 | [具体的な説明] |
| [issueから抽出した要件2] | 実装済み / 未実装 / 部分実装 | [具体的な説明] |

**スコープ外の変更**: [要件にないが追加されている変更があれば記載]

### プロジェクト適合性
[P0-P3の重要度で指摘]

### SOLID原則
[P0-P3の重要度で指摘]

### セキュリティ
[P0-P3の重要度で指摘]

### コード品質
[P0-P3の重要度で指摘]

### 削除候補
[P0-P3の重要度で指摘]

## 重要度レベル

| Level | Name | Action |
|-------|------|--------|
| P0 | Critical | マージをブロックすべき |
| P1 | High | マージ前に修正すべき |
| P2 | Medium | 修正するか、フォローアップを作成 |
| P3 | Low | 任意の改善 |

### Step 10: Confirmation
- 修正を実装する前にユーザーに確認を求める
- 「これらの問題を修正しますか?」と尋ねる
