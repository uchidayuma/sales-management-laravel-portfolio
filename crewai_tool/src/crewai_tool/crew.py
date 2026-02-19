import os
import subprocess
from crewai import Agent, Crew, Process, Task, LLM
from crewai.project import CrewBase, agent, before_kickoff, crew, task
from crewai.agents.agent_builder.base_agent import BaseAgent
from crewai_tools import FileWriterTool, FileReadTool, DirectoryReadTool
from typing import List

from crewai_tool.tools.custom_tool import GitCommitAndPushTool, CreatePRTool


@CrewBase
class CrewaiTool():
    """
    CrewaiTool crew - Hierarchical Development Team

    構造:
      Engineering Manager (manager_agent)
        ├── Backend Developer  → implementation_task（ファイル読み書き）
        └── QA Engineer        → qa_review_task（レビュー） → pr_task（PR作成）
    """

    agents: List[BaseAgent]
    tasks: List[Task]

    def _get_llm(self, model_env: str = "MODEL_LARGE") -> LLM:
        """
        環境変数からLLMを生成する。.env の MODEL_LARGE / MODEL_SMALL を書き換えるだけで
        プロバイダーを切り替えられる（コード変更不要）。

        対応プロバイダー:
          - Anthropic : anthropic/claude-sonnet-4-5-20250929  → ANTHROPIC_API_KEY
          - OpenAI    : openai/gpt-4o                        → OPENAI_API_KEY
          - Gemini    : gemini/gemini-2.0-flash              → GOOGLE_API_KEY
          - Ollama    : ollama/qwen3:4b                      → APIキー不要（localhost）
        """
        model = os.environ.get(model_env, "openai/gpt-4o-mini")

        if model.startswith("ollama/"):
            model_name = model.removeprefix("ollama/")
            return LLM(
                model=model_name,
                provider="openai",
                base_url="http://localhost:11434/v1",
                api_key="ollama",
            )

        return LLM(model=model)

    # -------------------------------------------------------------------------
    # ブランチ準備（タスク開始前に自動実行）
    # - issue_number からブランチ名を決定する
    # - 既存ブランチがあればチェックアウトのみ、なければ base_branch から作成
    # -------------------------------------------------------------------------
    @before_kickoff
    def setup_branch(self, inputs):
        repo_root = inputs.get("repo_root", ".")
        issue_number = inputs.get("issue_number", "0")
        base_branch = inputs.get("base_branch", "develop")
        branch_name = f"feature/issue-{issue_number}"

        # ローカルブランチの存在確認
        local = subprocess.run(
            ["git", "branch", "--list", branch_name],
            cwd=repo_root, capture_output=True, text=True
        )

        if local.stdout.strip():
            # 既存ブランチをチェックアウト
            subprocess.run(["git", "checkout", branch_name], cwd=repo_root, check=True)
            print(f"[Branch] 既存ブランチ '{branch_name}' をチェックアウトしました")
        else:
            # base_branch から新規作成
            subprocess.run(["git", "checkout", base_branch], cwd=repo_root, check=True)
            subprocess.run(["git", "checkout", "-b", branch_name], cwd=repo_root, check=True)
            print(f"[Branch] '{base_branch}' から新しいブランチ '{branch_name}' を作成しました")

        # タスク内で {branch_name} として参照できるよう inputs に追加
        inputs["branch_name"] = branch_name
        return inputs

    # -------------------------------------------------------------------------
    # マネージャーエージェント
    # ※ @agent デコレータなし → self.agents に含まれず manager_agent として使う
    # -------------------------------------------------------------------------
    def engineering_manager(self) -> Agent:
        return Agent(
            config=self.agents_config['engineering_manager'],  # type: ignore[index]
            llm=self._get_llm("MODEL_LARGE"),
            allow_delegation=True,
            verbose=True,
        )

    # -------------------------------------------------------------------------
    # ワーカーエージェント
    # -------------------------------------------------------------------------
    @agent
    def backend_developer(self) -> Agent:
        repo_root = os.environ.get("REPO_ROOT", ".")
        return Agent(
            config=self.agents_config['backend_developer'],  # type: ignore[index]
            llm=self._get_llm("MODEL_SMALL"),
            tools=[
                DirectoryReadTool(directory=repo_root),
                FileReadTool(),
                FileWriterTool(),
            ],
            verbose=True,
        )

    @agent
    def qa_engineer(self) -> Agent:
        repo_root = os.environ.get("REPO_ROOT", ".")
        return Agent(
            config=self.agents_config['qa_engineer'],  # type: ignore[index]
            llm=self._get_llm("MODEL_SMALL"),
            tools=[
                DirectoryReadTool(directory=repo_root),
                FileReadTool(),
                GitCommitAndPushTool(),
                CreatePRTool(),
            ],
            verbose=True,
        )

    # -------------------------------------------------------------------------
    # タスク
    # -------------------------------------------------------------------------
    @task
    def implementation_task(self) -> Task:
        return Task(
            config=self.tasks_config['implementation_task'],  # type: ignore[index]
        )

    @task
    def qa_review_task(self) -> Task:
        return Task(
            config=self.tasks_config['qa_review_task'],  # type: ignore[index]
        )

    @task
    def pr_task(self) -> Task:
        return Task(
            config=self.tasks_config['pr_task'],  # type: ignore[index]
            output_file='pr_result.md',
        )

    # -------------------------------------------------------------------------
    # クルー定義（ヒエラルキー型）
    # -------------------------------------------------------------------------
    @crew
    def crew(self) -> Crew:
        return Crew(
            agents=self.agents,                        # backend_developer, qa_engineer
            tasks=self.tasks,                          # implementation → qa_review → pr
            process=Process.hierarchical,
            manager_agent=self.engineering_manager(),
            verbose=True,
        )
