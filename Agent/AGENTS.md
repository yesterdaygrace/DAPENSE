# AGENTS.md v2.0 --- Token-Optimized Autonomous Development System

## Philosophy

Design for **correctness, scalability, and minimal context usage**.

Agents should retrieve only the information required for the current
decision. Project context should grow **sub-linearly** through
summarization, indexing, and caching.

------------------------------------------------------------------------

# Repository Layout

``` text
.
├── AGENTS.md
├── context/
│   ├── architecture-summary.md
│   ├── current-task.md
│   ├── glossary.md
│   └── context-index.md
├── memory/
│   ├── progress.md
│   ├── decisions.md
│   ├── risks.md
│   └── changelog.md
├── specs/
├── architecture/
├── tasks/
├── cache/
│   ├── retrieval-index.json
│   └── context-cache.json
└── .opencode/agents/
```

------------------------------------------------------------------------

# Global Rules

-   Never skip phases.
-   Never invent requirements.
-   Every implementation maps to FR-\* or TASK-\*.
-   Never modify unrelated files.
-   Prefer retrieval over rereading.
-   Prefer summaries over raw history.
-   Never reload an entire document when a section reference exists.
-   Stop after five failed review cycles for the same task.

------------------------------------------------------------------------

# Context Retrieval Policy

  Agent       Reads
  ----------- ---------------------------------------------------
  Spec        User request
  Architect   Spec
  Tasks       Spec + Architecture
  Build       Current task + referenced architecture + progress
  Test        Current task + implementation
  Review      Current task + implementation + tests
  Refactor    Review findings + implementation

Forbidden:

-   Reading entire repository
-   Reloading all architecture files
-   Reloading completed tasks

------------------------------------------------------------------------

# Context Index

Maintain `context/context-index.md`.

Example:

  Reference   Location
  ----------- -------------------------
  FR-4        architecture/auth.md §2
  TASK-18     tasks/auth.md
  Login API   architecture/api.md §4

Agents resolve references before loading files.

------------------------------------------------------------------------

# Memory

## progress.md

Rolling summary only.

Contains:

-   completed work
-   blockers
-   current milestone

Maximum 500 tokens.

Older history moves to changelog.md.

------------------------------------------------------------------------

## decisions.md

Architecture decisions only.

Each entry:

-   Decision ID
-   Reason
-   Alternatives
-   Impact

------------------------------------------------------------------------

## risks.md

Maintain:

-   technical debt
-   performance risks
-   security risks
-   migration risks

------------------------------------------------------------------------

# Cache

Cache reusable summaries.

Never regenerate identical summaries twice.

Invalidate cache only when referenced files change.

------------------------------------------------------------------------

# Build Guardrails

Before coding:

1.  Read current-task.md.
2.  Resolve referenced FR/TASK IDs.
3.  Load only referenced architecture sections.
4.  Read progress summary.

If implementation exceeds one session, request task splitting.

------------------------------------------------------------------------

# Review

Every failure includes:

-   Severity
-   Evidence
-   Root Cause
-   Risk
-   Suggested Fix

------------------------------------------------------------------------

# Failure Recovery

After five failures:

1.  Stop.
2.  Detect architectural mismatch.
3.  Recommend affected modules.
4.  Preserve completed work.
5.  Resume from revised architecture only.

------------------------------------------------------------------------

# Documentation Policy

Update only changed sections.

Never regenerate entire documentation unless explicitly requested.

------------------------------------------------------------------------

# Token Budgets

  Artifact         Target
  -------------- --------
  Spec              2,000
  Architecture      2,000
  Tasks             1,200
  Progress            500
  Review              800

Compress when exceeded.

------------------------------------------------------------------------

# Scalability Rules

-   Archive completed milestones.
-   Replace detailed history with summaries.
-   Deduplicate repeated information.
-   Prefer references over copied text.

Goal:

Context growth should scale with active work, not total project size.
