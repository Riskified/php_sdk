## What

<!-- What this PR changes and why. Link the Jira ticket. -->

## Change type

<!-- Pick one. For API alignment work, also name the plan step (e.g. "Step 2.4"). -->

- [ ] **Additive**: new `$_fields` entries, models or constants only. Nothing on the wire changes for existing code.
- [ ] **Bridge**: changes what is sent, toward the documented form, while existing code keeps working.
- [ ] **Patch**: fixes the SDK's own behavior without changing its public API.
- [ ] **Other** (tooling, tests, docs).

Plan step:

## Public API

<!-- Nothing checks this automatically; reviewers, please verify. -->

- [ ] No public class, method or `$_fields` key was removed or renamed, and no public method signature changed.
- [ ] No existing `$_fields` spec became stricter (new specs are `<type> optional` with no regex).
- [ ] New code is valid PHP 7.0: no nullable types, `void`, constant visibility or typed properties in `src/`.

## Golden diff

<!-- Paste the diff of tests/Golden/snapshots, or write "None". Regenerate snapshots with
     GOLDEN_UPDATE=1 vendor/bin/phpunit tests/Golden
     Every changed line must be explained here and in the changelog. -->

```diff
```

## Schema known gaps

<!-- Entries added to or removed from knownGaps() in tests/Golden/GoldenSchemaTest.php, or "None". -->
