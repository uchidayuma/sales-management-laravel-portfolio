import os
import subprocess
from crewai.tools import BaseTool
from typing import Type
from pydantic import BaseModel, Field


# ─────────────────────────────────────────────
# プロジェクト構造の一覧取得（重いディレクトリを除外）
# DirectoryReadTool の代替。ファイルの中身は返さない。
# ─────────────────────────────────────────────
class ListProjectFilesInput(BaseModel):
    repo_path: str = Field(..., description="リポジトリのルート絶対パス")


class ListProjectFilesTool(BaseTool):
    name: str = "List Project Files"
    description: str = (
        "リポジトリのファイル一覧（相対パス）を返す。"
        "node_modules / vendor / .venv / .git / storage / __pycache__ などの重いディレクトリは除外する。"
        "ファイルの中身は含まない。まず最初にこのツールでプロジェクト構造を把握してから、"
        "FileReadTool で必要なファイルの内容を読むこと。"
    )
    args_schema: Type[BaseModel] = ListProjectFilesInput

    _EXCLUDE_DIRS = {
        "node_modules", "vendor", ".venv", ".git",
        "storage", ".idea", "__pycache__", "dist", "build",
        ".terraform", "coverage",
    }
    _EXCLUDE_EXTENSIONS = {".log", ".lock", ".map", ".min.js", ".min.css"}

    def _run(self, repo_path: str) -> str:
        lines = []
        for root, dirs, files in os.walk(repo_path):
            dirs[:] = sorted(d for d in dirs if d not in self._EXCLUDE_DIRS)
            for f in sorted(files):
                if any(f.endswith(ext) for ext in self._EXCLUDE_EXTENSIONS):
                    continue
                full_path = os.path.join(root, f)
                rel_path = os.path.relpath(full_path, repo_path)
                lines.append(rel_path)
        if not lines:
            return "ファイルが見つかりませんでした。"
        return "\n".join(lines)


# ─────────────────────────────────────────────
# コード検索（grep 相当）
# ファイルを全部読む前に関連ファイルを絞り込むために使う
# ─────────────────────────────────────────────
class SearchCodeInput(BaseModel):
    repo_path: str = Field(..., description="リポジトリのルート絶対パス")
    query: str = Field(..., description="検索するキーワードまたは正規表現")
    file_pattern: str = Field(
        default="*",
        description="対象ファイルのパターン（例: *.php, *.blade.php, *.js）。省略時は全ファイル対象",
    )


class SearchCodeTool(BaseTool):
    name: str = "Search Code"
    description: str = (
        "リポジトリ内のファイルをキーワードで検索し、マッチした行とファイルパスを返す。"
        "FileReadTool でファイルを読む前に必ずこのツールで関連ファイルを絞り込むこと。"
        "例: Issue に 'Product' が含まれるなら query='Product' で検索して関連ファイルを特定する。"
        "node_modules / vendor / .venv / .git などの重いディレクトリは自動除外される。"
    )
    args_schema: Type[BaseModel] = SearchCodeInput

    _EXCLUDE_DIRS = [
        "node_modules", "vendor", ".venv", ".git",
        "storage", ".idea", "__pycache__", "dist", "build",
        ".terraform", "coverage",
    ]

    def _run(self, repo_path: str, query: str, file_pattern: str = "*") -> str:
        cmd = ["grep", "-r", "-n", "-i", "--include", file_pattern, query, repo_path]
        for d in self._EXCLUDE_DIRS:
            cmd.extend(["--exclude-dir", d])

        result = subprocess.run(cmd, capture_output=True, text=True)

        if not result.stdout.strip():
            return f"'{query}' に一致するコードが見つかりませんでした。"

        lines = result.stdout.strip().split("\n")
        # トークン節約のため上限を設ける
        if len(lines) > 80:
            truncated = len(lines) - 80
            lines = lines[:80]
            lines.append(f"... (残り {truncated} 件省略。file_pattern を絞るか query を変えて再検索してください)")

        return "\n".join(lines)


