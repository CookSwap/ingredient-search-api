# Contributing

Contributions of all kinds are welcome — spec refinements, example implementations, SDKs, and validators.

## What we're looking for

- **Spec extensions** — new optional fields, new error codes, versioning proposals. Open an issue first to discuss before sending a PR.
- **Reference implementations** — working examples in languages not yet covered (see `examples/`).
- **SDK clients** — thin wrappers that make calling the API easier from common recipe-app stacks.
- **Validators** — tooling that lets a retailer verify their implementation against the spec.
- **Bug reports** — ambiguities, contradictions, or omissions in the spec.

## Process

1. **Open an issue** before starting significant work so we can agree scope.
2. **Fork** the repo and create a branch: `git checkout -b feat/your-feature`.
3. **Keep changes focused** — one concern per PR.
4. **Update the spec version** in `openapi.yaml` if you're making a breaking or additive change (follow [semver](https://semver.org/)).
5. **Open a pull request** with a clear description of what changed and why.

## New retailer listing

To list your implementation on CookSwap, open an issue using the **New Retailer** template and include:

- Your retailer name and region
- Your `/health` endpoint URL (so we can verify it's live)
- Whether you want to appear in the default selector or be opt-in

## Spec versioning

| Change type | Version bump |
|-------------|-------------|
| New required field or endpoint | Major (2.0.0) |
| New optional field | Minor (1.1.0) |
| Clarification or example fix | Patch (1.0.1) |

## Code of conduct

Be constructive. Treat everyone with respect.
