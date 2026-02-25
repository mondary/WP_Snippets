---
name: wp-code-snippets-conventions
description: Conventions and workflow for this WP_Snippets repo: canonical/archive organization, filename naming, version numbering, required snippet headers (including CLM-CREATED-AT), and Code Snippets WordPress import/export sync workflow. Use when editing, renaming, deduplicating, versioning, or preparing imports for this project.
---

# WP Code Snippets Conventions (This Repo)

Use this skill when working on the local snippet corpus and WordPress `Code Snippets` sync for this repository.

## Project map (current)

- `WP_Snippets_FINAL_CLEAN/`
  - `canonical/` = current snippets to keep/use/import
  - `archive/` = older versions / variants / inactive history
- `CODE_SNIPPETS_SYNC/`
  - `imports/IMPORT-WORDPRESS.json` = current import file for WordPress
  - `scripts/` = import/export/compare scripts
  - `exports/` = raw JSON exports from WordPress
  - `reports/` = compare reports
- `_ROOT_LEGACY/` folders = historical files/scripts/reports; do not treat as source of truth

## Source of truth rules

- Working source for active sync/import is `WP_Snippets_FINAL_CLEAN/canonical/`.
- `archive/` keeps version history and variants.
- `canonical` must contain the latest version for each family.
- Do not delete history by default; archive instead.

## Filename conventions

### Canonical filenames (visual/readable)

Canonical files use a visual prefix:

- `EMOJI + SPACE + UPPERCASE PREFIX - Human Name - vN.php`

Examples:

- `🧭 ADMIN MENUBAR - Futur Menubar - v3.php`
- `📅 SCHEDULER - Scheduled Posts Popup - v14.php`
- `📊 TRACKING - Umami PHP - v2.php`

Notes:

- The first segment (before first ` - `) is uppercased for readability.
- Emoji represents the primary feature cluster.
- Keep names short but explicit for humans.

### Archive filenames

Archive files keep human-readable versioned names, usually without emoji:

- `Family Name - vN.php`
- `Family Name - vN - Variant.php`
- `Family Name - vN - alt2.php` (collision/exact duplicate variant)

## Versioning conventions

- Version numbers are **per family** and form a **single timeline**.
- Avoid multiple `v1`/`v2` in the same family after family merges.
- If two files are same family but had different names, merge family names first (by features), then renumber.
- `canonical` should be the highest `vN` for that family.

### Family matching rule (important)

Do **not** rely only on names. Compare features:

- WordPress hooks
- functions (`Fonctions clefs`)
- shortcodes
- AJAX callbacks
- feature cluster (`Cluster principal`)
- actual behavior / UI purpose

Names can drift (`Futursite` vs `Futur Menubar`) while functionality remains same lineage.

## Required headers in canonical files

Canonical files should keep these blocks (in this order):

1. `/* CLM-CREATED-AT: YYYY-MM-DD */` (first line)
2. `/* FINAL-CANONICAL-META ... */`
3. `/* CLM-FEATURES-DESCRIPTION:START ... END */`
4. `/* CLM-FEATURE-CLASSIFICATION:START ... END */`
5. Snippet code

### CLM-CREATED-AT

- Present at the top of every `canonical` file.
- Format:
  - `/* CLM-CREATED-AT: 2026-02-25 */`
- This is used as a stable creation marker to simplify future version tracking.

When editing an existing file:

- Keep the original `CLM-CREATED-AT`.
- Do not duplicate the line.

When creating a new canonical file:

- Add `CLM-CREATED-AT` at top immediately.

## Metadata/feature headers: how to use them

These blocks are used for:

- deduping / family merges
- version normalization
- WordPress import description generation
- scope inference (`admin`, `front-end`, `global`)

Preserve these fields when possible:

- `Display name`
- `Scope`
- `Hooks WP`
- `Fonctions clefs`
- `Cluster principal`
- `Features detectees`
- `Version`

## WordPress Code Snippets sync workflow (current)

### Generate import JSON (from canonical)

Run from repo root:

```bash
php CODE_SNIPPETS_SYNC/scripts/build_code_snippets_import.php \
  --snippets-dir=WP_Snippets_FINAL_CLEAN/canonical \
  --out=CODE_SNIPPETS_SYNC/imports/IMPORT-WORDPRESS.json
```

The generator currently:

- reads canonical `.php` files
- infers scope from headers when available
- adds descriptions from `CLM-FEATURES-DESCRIPTION`
- formats import names with emoji + uppercase prefix

### Import into WordPress

- `wp-admin` -> `Code Snippets` -> `Import`
- select `CODE_SNIPPETS_SYNC/imports/IMPORT-WORDPRESS.json`

### Compare with a WordPress export (safe/read-only)

```bash
php CODE_SNIPPETS_SYNC/scripts/compare_code_snippets_export.php \
  --export=CODE_SNIPPETS_SYNC/exports/your-export.json \
  --snippets-dir=WP_Snippets_FINAL_CLEAN/canonical \
  --report=CODE_SNIPPETS_SYNC/reports/compare-report.json
```

### Extract WordPress export to files (for analysis/reconciliation)

```bash
php CODE_SNIPPETS_SYNC/scripts/extract_code_snippets_export_to_files.php \
  --export=CODE_SNIPPETS_SYNC/exports/your-export.json \
  --out-dir=CODE_SNIPPETS_SYNC/exports/extracted-online-current
```

## Safety rules for future agents

- Do not overwrite `canonical` blindly from WordPress export.
- Prefer `export -> compare -> inspect -> merge -> regenerate import`.
- Keep `archive` as version history, not trash.
- Be conservative with feature-based family merges for simple snippets (same hook alone can be misleading).
- After mass renames/merges, verify:
  - `canonical` has no duplicate family entries
  - `canonical` is latest version in each family

## If you need to update conventions

Update this skill first when changing:

- naming format
- header format/order
- versioning policy
- sync file paths / commands

