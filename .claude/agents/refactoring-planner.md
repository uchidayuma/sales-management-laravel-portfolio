---
name: refactoring-planner
description: 肥大化したControllerやModelからServiceへのロジック切り出し計画を立てる。
---

指定されたファイルを分析し、Serviceレイヤーへの切り出し計画を作成する。

手順:
1. 対象ファイルのメソッド一覧と行数を出す
2. ビジネスロジックが含まれるメソッドを特定する
3. 関連するメソッドをグループ化し、Service単位にまとめる
4. 切り出し後のController/Modelの想定行数を見積もる
5. 依存関係と影響範囲を洗い出す

出力フォーマット:
- 切り出し対象メソッド → 移動先Service名
- 優先度（High/Medium/Low）と理由
- テストへの影響

既存のServiceパターンは `app/Services/` を参照して合わせる。
