#!/usr/bin/env python
import os
import sys
import subprocess
import warnings
from pathlib import Path

from crewai_tool.crew import CrewaiTool

warnings.filterwarnings("ignore", category=SyntaxWarning, module="pysbd")


def _get_repo_root() -> str:
    """git リポジトリのルートパスを取得して環境変数にセットする"""
    result = subprocess.run(
        ["git", "rev-parse", "--show-toplevel"],
        capture_output=True, text=True, check=True
    )
    repo_root = result.stdout.strip()
    os.environ["REPO_ROOT"] = repo_root
    return repo_root


def _load_issue() -> str:
    """
    Issue 内容を以下の優先順位で取得する:
      1. 環境変数 ISSUE
      2. crewai_tool/issue.md ファイル
    """
    issue = os.environ.get("ISSUE")
    if issue:
        return issue

    issue_file = Path(__file__).parent.parent.parent.parent / "issue.md"
    if issue_file.exists():
        return issue_file.read_text(encoding="utf-8").strip()

    raise FileNotFoundError(
        "Issue が見つかりません。以下のいずれかで指定してください:\n"
        "  1. 環境変数: export ISSUE='## 機能追加: ...'\n"
        "  2. ファイル: crewai_tool/issue.md を作成する\n"
        f"     (探したパス: {issue_file})"
    )


def _build_inputs() -> dict:
    """実行に必要な inputs を組み立てる"""
    repo_root = _get_repo_root()
    issue = _load_issue()
    issue_number = os.environ.get("ISSUE_NUMBER", "0")
    base_branch = os.environ.get("BASE_BRANCH", "develop")

    return {
        "issue": issue,
        "issue_number": issue_number,
        "base_branch": base_branch,
        "repo_root": repo_root,
        # branch_name は @before_kickoff で自動設定される
    }


def run():
    """
    Run the crew.
    Engineering Manager → Backend Developer → QA Engineer → PR 作成

    必要な設定:
      - ISSUE_NUMBER : 環境変数 or .env (例: 42)
      - BASE_BRANCH  : 環境変数 or .env (デフォルト: develop)
      - Issue 内容   : 環境変数 ISSUE または crewai_tool/issue.md
    """
    try:
        inputs = _build_inputs()
        CrewaiTool().crew().kickoff(inputs=inputs)
    except Exception as e:
        raise Exception(f"An error occurred while running the crew: {e}")


def train():
    try:
        inputs = _build_inputs()
        CrewaiTool().crew().train(
            n_iterations=int(sys.argv[1]),
            filename=sys.argv[2],
            inputs=inputs,
        )
    except Exception as e:
        raise Exception(f"An error occurred while training the crew: {e}")


def replay():
    try:
        CrewaiTool().crew().replay(task_id=sys.argv[1])
    except Exception as e:
        raise Exception(f"An error occurred while replaying the crew: {e}")


def test():
    try:
        inputs = _build_inputs()
        CrewaiTool().crew().test(
            n_iterations=int(sys.argv[1]),
            eval_llm=sys.argv[2],
            inputs=inputs,
        )
    except Exception as e:
        raise Exception(f"An error occurred while testing the crew: {e}")


def run_with_trigger():
    """JSON ペイロードから実行する（外部トリガー用）"""
    import json

    if len(sys.argv) < 2:
        raise Exception("No trigger payload provided. Please provide JSON payload as argument.")

    try:
        trigger_payload = json.loads(sys.argv[1])
    except json.JSONDecodeError:
        raise Exception("Invalid JSON payload provided as argument")

    repo_root = _get_repo_root()

    # ペイロードに issue が含まれない場合は issue.md から読む
    issue = trigger_payload.get("issue") or _load_issue()

    inputs = {
        "crewai_trigger_payload": trigger_payload,
        "issue": issue,
        "issue_number": trigger_payload.get("issue_number", os.environ.get("ISSUE_NUMBER", "0")),
        "base_branch": trigger_payload.get("base_branch", os.environ.get("BASE_BRANCH", "develop")),
        "repo_root": repo_root,
    }

    try:
        result = CrewaiTool().crew().kickoff(inputs=inputs)
        return result
    except Exception as e:
        raise Exception(f"An error occurred while running the crew with trigger: {e}")
