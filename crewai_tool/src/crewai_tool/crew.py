import os
from crewai import Agent, Crew, Process, Task, LLM
from crewai.project import CrewBase, agent, crew, task
from crewai.agents.agent_builder.base_agent import BaseAgent
from crewai_tools import FileWriterTool, FileReadTool
from typing import List

from crewai_tool.tools.custom_tool import ListProjectFilesTool, SearchCodeTool


@CrewBase
class CrewaiTool():
    """
    CrewaiTool crew - Hierarchical Development Team

    構造:
      Engineering Manager (manager_agent)
        ├── Backend Developer  → implementation_task（ファイル読み書き）
        └── QA Engineer        → qa_review_task（レビュー）

    GitHub 連携:
      - Issue の取得のみ GitHub CLI で行う（main.py の _load_issue）
      - ブランチ操作・コミット・PR 作成は行わない
    """

    agents: List[BaseAgent]
    tasks: List[Task]

    def _get_llm(self, model_env: str = "MODEL_LARGE") -> LLM:
        """
        環境変数からLLMを生成する。.env の MODEL_LARGE / MODEL_SMALL を書き換えるだけで
        プロバイダーを切り替えられる（コード変更不要）。

        対応プロバイダー:
          - Anthropic : anthropic/claude-sonnet-4-6          → ANTHROPIC_API_KEY
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
    # マネージャーエージェント
    # ※ @agent デコレータなし → self.agents に含まれず manager_agent として使う
    # -------------------------------------------------------------------------
    def engineering_manager(self) -> Agent:
        # Manager の役割は「どのワーカーに振るか判断するだけ」なので MODEL_SMALL で十分。
        # MODEL_LARGE (Sonnet) は 30K TPM 制限があり、大きな Issue で即座に rate limit に達する。
        return Agent(
            config=self.agents_config['engineering_manager'],  # type: ignore[index]
            llm=self._get_llm("MODEL_SMALL"),
            allow_delegation=True,
            verbose=True,
        )

    # -------------------------------------------------------------------------
    # ワーカーエージェント
    # -------------------------------------------------------------------------
    @agent
    def backend_developer(self) -> Agent:
        return Agent(
            config=self.agents_config['backend_developer'],  # type: ignore[index]
            llm=self._get_llm("MODEL_SMALL"),
            tools=[
                ListProjectFilesTool(),
                SearchCodeTool(),
                FileReadTool(),
                FileWriterTool(),
            ],
            allow_delegation=False,  # ループ防止：ワーカーは委譲しない
            verbose=True,
        )

    @agent
    def qa_engineer(self) -> Agent:
        return Agent(
            config=self.agents_config['qa_engineer'],  # type: ignore[index]
            llm=self._get_llm("MODEL_SMALL"),
            tools=[
                ListProjectFilesTool(),
                SearchCodeTool(),
                FileReadTool(),
            ],
            allow_delegation=False,  # ループ防止：ワーカーは委譲しない
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
            output_file='qa_result.md',
        )

    # -------------------------------------------------------------------------
    # クルー定義（ヒエラルキー型）
    # -------------------------------------------------------------------------
    @crew
    def crew(self) -> Crew:
        return Crew(
            agents=self.agents,                        # backend_developer, qa_engineer
            tasks=self.tasks,                          # implementation → qa_review
            process=Process.hierarchical,
            manager_agent=self.engineering_manager(),
            max_rpm=3,                                 # 30K TPM 制限対策: リクエストを分散
            verbose=True,
        )
