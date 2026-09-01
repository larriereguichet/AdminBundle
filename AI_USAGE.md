# AI usage

This project is developed with the help of AI coding assistants — mainly
[Claude Code](https://claude.com/claude-code). This document states plainly how they are used
and where the responsibility lies.

## The short version

**AI assistants write and review code and documentation in this repository. Every change is
read, tested and approved by the maintainer before it is merged. The maintainer is responsible
for all of it — no exception is made for AI-assisted contributions.**

## What AI is used for

* Drafting and maintaining the documentation under `docs/`.
* Writing and refactoring code, under review.
* Reviewing changes: spotting bugs, checking consistency, proposing tests.
* Routine work: renames, test scaffolding, keeping the docs in sync with the code.

The assistants work under the rules committed in [`CLAUDE.md`](CLAUDE.md) and
[`.claude/rules/`](.claude/rules/): coding standards, commit conventions, and the requirement
that the quality suite passes before a change is proposed.

## What this does not change

* **Review.** Nothing is merged without the maintainer reading it. An AI-assisted change gets
  the same scrutiny as any other contribution — arguably more, because a plausible-looking
  wrong answer is the failure mode to watch for.
* **Quality gates.** PHPUnit, PHPStan level 6, PHP-CS-Fixer, Rector and the backward
  compatibility check run on every change, whoever wrote it. See
  [`CLAUDE.md`](CLAUDE.md#quality-gates).
* **Ownership.** The maintainer takes responsibility for every line in this repository. Bugs
  are the project's bugs, not the tool's.
* **Licensing.** The project remains under the [MIT license](LICENSE), and contributions are
  accepted under the same terms.

## Why there is no per-commit AI marker

Commits carry no `Co-Authored-By` or "generated with" trailer. Two reasons: the assistants are
tools used by the maintainer rather than co-authors, and a trailer on some commits but not
others would suggest the unmarked ones went through a different review — which is not the case.
Disclosure is made here, at project level, and it applies to the whole 2.0 line.

## Contributions

You are welcome to use AI assistance in your own contributions. If you do, please say so in the
pull request description, and make sure you understand and can defend the change you are
submitting — that is the actual bar, for everyone.

## Feedback

If something in the code or in the documentation looks wrong, misleading, or machine-generated
in a way that hurts, please
[open an issue](https://github.com/larriereguichet/AdminBundle/issues). Reports on the
documentation are as welcome as reports on the code.
