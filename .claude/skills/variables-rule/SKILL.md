name: codingVariable
description: コード生成や変数名提案時に、プロジェクトの命名規則に従ってください。

## 命名規則

このプロジェクトでは以下の命名規則に従ってください:

**変数名**: camelCaseを使用
- 例: `userData`, `isActive`, `totalCount`

**定数**: UPPER_SNAKE_CASEを使用
- 例: `MAX_RETRY_COUNT`, `API_BASE_URL`

**クラス名**: PascalCaseを使用
- 例: `UserService`, `PaymentController`

## 例

❌ 悪い: `user_data` (変数なのにsnake_case)
✅ 良い: `userData`
