# プロジェクトコンテキスト

このファイルは、レビュー対象プロジェクトの固有のコンベンションとパターンを記載します。
コードレビュー時は必ずこれらと照合してください。

## 使用方法

1. このファイルをプロジェクトのルートディレクトリに配置
2. プロジェクト固有のルールを以下のセクションに記載
3. コードレビュー時にClaudeが自動的に参照

---

## プロジェクト構造

(例)
- `src/components/`: Reactコンポーネント
- `src/utils/`: ヘルパー関数
- `src/hooks/`: カスタムフック
- `src/services/`: API通信ロジック

## ファイル命名規則

(例)
- コンポーネント: PascalCase (例: `UserProfile.tsx`)
- ユーティリティ: camelCase (例: `formatDate.ts`)
- テスト: `*.test.ts` または `*.spec.ts`

## インポート順序

(例)
1. React関連
2. 外部ライブラリ
3. 内部コンポーネント
4. ユーティリティ
5. 型定義
6. スタイル

```typescript
// 良い例
import React from 'react';
import { useState } from 'react';
import axios from 'axios';
import { Button } from '@/components/common';
import { formatDate } from '@/utils';
import type { User } from '@/types';
import styles from './styles.module.css';
```

## エラーハンドリング

(例)
- すべての非同期関数は try-catch を使用
- エラーは `logger.error()` でログ出力
- ユーザー向けエラーは `toast.error()` で表示

```typescript
// 良い例
async function fetchUser(id: string) {
  try {
    const response = await api.get(`/users/${id}`);
    return response.data;
  } catch (error) {
    logger.error('Failed to fetch user', { id, error });
    toast.error('ユーザー情報の取得に失敗しました');
    throw error;
  }
}
```

## テスト要件

(例)
- 新規機能: 80%以上のカバレッジ必須
- ユーティリティ関数: 100%カバレッジ必須
- コンポーネント: 主要なユーザーフローをカバー

## 既存パターンの参照

コードレビュー時は以下のディレクトリを参考にする:

(例)
- `src/components/common/`: 共通コンポーネントのパターン
- `src/hooks/useAuth.ts`: 認証フックの実装パターン
- `tests/utils/`: テストのベストプラクティス

**重要**: レビュー時に「既存のパターンと異なる」場合は必ず指摘する。
