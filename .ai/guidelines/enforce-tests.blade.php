@php
/** @var \Laravel\Boost\Install\GuidelineAssist $assist */
@endphp
# Test Enforcement

- Not every change requires a test.
- Tests are behavior focused and mostly feature tests with HTTP entrypoints, unit tests are reserved for complex behaviors
- Do not write tests just for coverage, tests are behavior oriented
- Before writing a test give a short description on the test and prompt the user for permission (Except if prompted to write a test explicitly)
- Run the minimum number of tests needed to ensure code quality and speed. Use `{{ $assist->artisanCommand('test --compact') }}` with a specific filename or filter.

## Test critic — mandatory

Whenever you add or change a test file, the **last step before reporting the work as done** is an independent review by this project's `test-critic` subagent. It is not optional and not conditional on the change looking simple.

- Invoke it with the Agent/Task tool using **`subagent_type: "test-critic"`** — that exact string. The agent is defined in `.claude/agents/test-critic.md` (source: `.ai/agents/test-critic.md`) and already carries its own review process, output format and `STRONG`/`ACCEPTABLE`/`WEAK` verdict.
- **Do not write your own reviewer prompt, do not spawn a `general-purpose`/`claude` agent to "act as" a critic, and do not review your own tests inline.** Any of those is a violation of this rule, not a substitute for it.
- Give it only context, never instructions on how to review: the test files you touched, the production code under test, and the behavior that changed.
- Then act on the findings and report its verdict verbatim in your reply.
- If you are yourself a subagent and have no Agent/Task tool available, you cannot satisfy this rule — say so explicitly in your final report and list the test files still awaiting the `test-critic` pass so the main agent runs it.
