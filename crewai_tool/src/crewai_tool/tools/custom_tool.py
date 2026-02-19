import subprocess
from crewai.tools import BaseTool
from typing import Type
from pydantic import BaseModel, Field


# ─────────────────────────────────────────────
# Git: ステージング → コミット → プッシュ
# ブランチは @before_kickoff で作成済みのため、ここでは作らない
# ─────────────────────────────────────────────
class GitCommitAndPushInput(BaseModel):
    repo_path: str = Field(..., description="git リポジトリのルート絶対パス")
    commit_message: str = Field(..., description="コミットメッセージ")
    branch_name: str = Field(..., description="プッシュ先のブランチ名（チェックアウト済み）")


class GitCommitAndPushTool(BaseTool):
    name: str = "Git Commit And Push"
    description: str = (
        "変更ファイルをすべてステージングし、コミットして指定ブランチにプッシュする。"
        "ブランチはすでに作成・チェックアウト済みの前提で動作する。"
        "実装ファイルをすべて書き終えた後に使用すること。"
    )
    args_schema: Type[BaseModel] = GitCommitAndPushInput

    def _run(self, repo_path: str, commit_message: str, branch_name: str) -> str:
        try:
            subprocess.run(
                ["git", "add", "-A"],
                cwd=repo_path, capture_output=True, text=True, check=True
            )
            subprocess.run(
                ["git", "commit", "-m", commit_message],
                cwd=repo_path, capture_output=True, text=True, check=True
            )
            result = subprocess.run(
                ["git", "push", "-u", "origin", branch_name],
                cwd=repo_path, capture_output=True, text=True, check=True
            )
            return f"ブランチ '{branch_name}' へのコミット・プッシュが完了しました。\n{result.stdout}"
        except subprocess.CalledProcessError as e:
            return f"Git 操作に失敗しました: {e.stderr}"


# ─────────────────────────────────────────────
# GitHub: プルリクエスト作成
# ─────────────────────────────────────────────
class CreatePRInput(BaseModel):
    repo_path: str = Field(..., description="git リポジトリのルート絶対パス")
    title: str = Field(..., description="プルリクエストのタイトル")
    body: str = Field(..., description="プルリクエストの本文（変更内容・確認事項など）")
    base_branch: str = Field(default="develop", description="マージ先のブランチ名")


class CreatePRTool(BaseTool):
    name: str = "Create GitHub Pull Request"
    description: str = (
        "gh CLI を使って GitHub にプルリクエストを作成する。"
        "コミット・プッシュが完了した後に使用すること。"
    )
    args_schema: Type[BaseModel] = CreatePRInput

    def _run(self, repo_path: str, title: str, body: str, base_branch: str = "develop") -> str:
        try:
            result = subprocess.run(
                ["gh", "pr", "create", "--title", title, "--body", body, "--base", base_branch],
                cwd=repo_path, capture_output=True, text=True, check=True
            )
            return f"プルリクエストを作成しました！\n{result.stdout}"
        except subprocess.CalledProcessError as e:
            return f"PR 作成に失敗しました: {e.stderr}"
