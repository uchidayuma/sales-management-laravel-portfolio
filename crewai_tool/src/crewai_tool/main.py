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


def _load_issue(issue_number: str, repo_root: str) -> str:
    """
    GitHub CLI で Issue の内容を取得する。
    取得できない場合は例外を投げる。
    """
    result = subprocess.run(
        ["gh", "issue", "view", issue_number, "--json", "title,body,labels,assignees"],
        cwd=repo_root,
        capture_output=True,
        text=True,
    )
    if result.returncode != 0:
        raise RuntimeError(
            f"Issue #{issue_number} の取得に失敗しました。\n"
            f"gh CLI がログイン済みか、Issue 番号が正しいか確認してください。\n"
            f"エラー: {result.stderr.strip()}"
        )

    import json
    data = json.loads(result.stdout)
    title = data.get("title", "")
    body = data.get("body", "")
    labels = ", ".join(l["name"] for l in data.get("labels", []))
    assignees = ", ".join(a["login"] for a in data.get("assignees", []))

    parts = [f"## {title}"]
    if labels:
        parts.append(f"**Labels:** {labels}")
    if assignees:
        parts.append(f"**Assignees:** {assignees}")
    parts.append("")
    parts.append(body)

    return "\n".join(parts)


def _build_inputs() -> dict:
    """実行に必要な inputs を組み立てる"""
    repo_root = _get_repo_root()
    issue_number = os.environ.get("ISSUE_NUMBER", "")

    if not issue_number:
        raise ValueError(
            "ISSUE_NUMBER が設定されていません。\n"
            ".env または環境変数で ISSUE_NUMBER=<番号> を指定してください。"
        )

    issue = _load_issue(issue_number, repo_root)

    return {
        "issue": issue,
        "issue_number": issue_number,
        "repo_root": repo_root,
    }


def run():
    """
    Run the crew.
    Engineering Manager → Backend Developer → QA Engineer

    必要な設定:
      - ISSUE_NUMBER : 環境変数 or .env (例: 42)
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

    issue_number = trigger_payload.get("issue_number", os.environ.get("ISSUE_NUMBER", ""))
    # ペイロードに issue 本文が含まれない場合は GitHub CLI で取得
    issue = trigger_payload.get("issue") or _load_issue(issue_number, repo_root)

    inputs = {
        "crewai_trigger_payload": trigger_payload,
        "issue": issue,
        "issue_number": issue_number,
        "repo_root": repo_root,
    }

    try:
        result = CrewaiTool().crew().kickoff(inputs=inputs)
        return result
    except Exception as e:
        raise Exception(f"An error occurred while running the crew with trigger: {e}")
