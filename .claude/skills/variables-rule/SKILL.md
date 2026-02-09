---
name: variables-rule
description: コード生成や変数名提案時に、プロジェクトの命名規則に従ってください。
---

## 命名規則

このプロジェクトでは以下の命名規則に従ってください:

**変数名**: camelCase を使用

-   例: `userData`, `isActive`, `totalCount`

**定数**: UPPER_SNAKE_CASE を使用

-   例: `MAX_RETRY_COUNT`, `API_BASE_URL`

**クラス名**: PascalCase を使用

-   例: `UserService`, `PaymentController`

## 例

❌ 悪い: `user_data` (変数なのに snake_case)
✅ 良い: `userData`
