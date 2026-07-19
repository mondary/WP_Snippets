# WP_Snippets

[🇬🇧 EN](README_en.md) · [🇫🇷 FR](README.md)

✨ WordPress snippet collection focused on admin productivity, publishing, and editorial workflows.

## ✅ Features
- Ready-to-use snippet base in `snippets/canonical/`.
- History and variants in `snippets/archive/`.
- WordPress sync workflow via `CODE_SNIPPETS_SYNC/`.
- New RAG export snippet: one Markdown file per post (ZIP).
- **Schedule Calendar V26** with featured images, drag & drop, draft reallocation starting from today, and scheduled-post verification (slots 10am, 2pm, 11am, 12pm, 1pm). Reallocation now starts from **today** and respects shared publish+future+draft capacity (max `articles_per_day` per day). Past slots are automatically filtered.
- **"Scheduled Posts" submenu** in the left sidebar, under the Posts menu, with a badge showing the scheduled post count.
- **Missing Featured Image Detection** — filter in the post list, "No Image" submenu with counter, and dedicated page listing published posts without a featured image.

## 🧠 Usage
1. Open and edit snippets in `snippets/canonical/`.
2. Import a snippet into WordPress (Code Snippets / WPCode plugin).
3. Activate the snippet and test it in WordPress admin.

### RAG Export (new)
- File: `snippets/canonical/🧰 UTILITIES - Admin Export Posts Markdown RAG - v1.php`
- UI: `Export Markdown (RAG)` button in `wp-admin > Posts`.
- Output: `wp-posts-rag-YYYY-MM-DD.zip`
- ZIP content:
  - 1 `.md` file per post (`YYYY-MM-DD__slug__id-123.md`)
  - `INDEX.md` (global files index)
- Included metadata: date, author, categories, tags, keywords, excerpt, URL, status, etc.

### Schedule Calendar V27
- File: `snippets/canonical/ADMIN 📅 SCHEDULER - Calendar - v27.php`
- UI: "Calendar" menu bar entry in WordPress admin + version badge in the page title.
- **Featured images** as thumbnails in day cards (red border + 🖼️ when missing).
- **Stable month view**: prev/next navigation, `+1 month` / `Full year` options.
- **Drag & Drop**: reschedule posts via drag, automatic day rebalance.
- **Priority slots `10h, 14h, 11h, 12h, 13h`**: 1st post → 10am, 2nd → 2pm, then 11am/12pm/1pm.
- **Draft reallocation**: dedicated button + posts-per-day selector (1 to 5). Default: **Scheduled + drafts** with **5 posts/day**, compacting from today. Posts with featured images are processed before posts without one. Taken and past slots are automatically filtered.
- **Shared capacity** — total publish + future + draft never exceeds `articles_per_day` per day, **including today**. A day with 3 published posts at `2/day` accepts zero drafts. A day with 0 published at `2/day` accepts 2 drafts (10am, 2pm).
- **Detailed result dialog** with diagnostic sections: draft placement (ID + target date) and 6-day occupancy overview starting from today.
- **Status bar** below the header, full width.
- **Filters**: title search, category filter, month/year selection, duplicate detection.

### Missing Featured Image Detection
- File: `snippets/canonical/ADMIN 🧰 DETECT - Missing Featured Images - v1.php`
- UI: "No Image" submenu in the left sidebar, under the Posts menu (red badge = count of posts without featured image).
- "With/Without featured image" filter in the post list (`edit.php`).
- Dedicated page listing all published posts without a featured image, with Edit/View links.

### Scheduled Posts Submenu
- File: `snippets/canonical/🧭 ADMIN MENUBAR - Scheduled Posts Submenu - v1.php`
- UI: "Scheduled Posts" submenu in the left sidebar, under the Posts menu.
- Shows a badge with the number of scheduled posts.
- Clean redirect to `edit.php?post_status=future&post_type=post&orderby=date&order=asc`.

## ⚙️ Settings
- No mandatory settings for most snippets.
- For RAG export, PHP `ZipArchive` extension is required.

## 🧾 Commands
```bash
# Check PHP syntax for a snippet
php -l "snippets/canonical/🧰 UTILITIES - Admin Export Posts Markdown RAG - v1.php"
```

## 📦 Build & Package
- JSON import generation via `CODE_SNIPPETS_SYNC/`.
- Recommended WordPress import: `CODE_SNIPPETS_SYNC/imports/IMPORT-WORDPRESS.json`.

## 🧪 Install
1. Install/activate `Code Snippets` (or WPCode) on WordPress.
2. Paste/import the target snippet.
3. Activate and verify in admin UI.

## 🧾 Changelog
- 2026-07-16: **Missing Featured Image Detection `v1`** — new snippet `ADMIN 🧰 DETECT - Missing Featured Images - v1.php`: filter in the post list (With/Without image), "No Image" submenu with counter, dedicated page listing published posts without a featured image.
- 2026-07-16: **Media Orphans `v3`** — merged media size snippet (no more duplicate), streamlined UI (filters in dropdown only, removed button wall), reliable Used In column links (fallback `get_permalink` + plain title), analysis from newest to oldest (DESC). Unified token for Analyze Usage and Recalculate size. `v2` + `Admin Size v2` archived.
- 2026-07-11: Schedule Calendar `v26` — draft reallocation now starts from **today** instead of D+1. Shared publish+future+draft capacity is enforced for all days **including today**. Past slots are filtered. Priority order `[10,14,11,12,13]` unified for all days. Debug occupancy extended to 6 days from today. `v24` and `v25` archived.
- 2026-06-16: Schedule Calendar `v23` — reallocation now accounts for **published** posts when calculating daily capacity (`clm_normalize_future_posts_schedule` and `clm_compact_future_posts` now query the DB to exclude published slots). Default mode changed to **Scheduled + drafts**. Diagnostics added to result dialog (detailed placement + occupancy). `v22` archived.
- 2026-06-16: New snippet **"Scheduled Posts"** — left sidebar submenu under Posts, with count badge and clean redirect to scheduled posts sorted by ascending date.
- 2026-06-16: Schedule Calendar `v21` — fix empty result popup (reconstructed already-built sections). Status bar now sticky below header (stays visible on scroll). Dialog body scroll fixed (flex: 1 + min-height: 0).
- 2026-06-15: Schedule Calendar `v21` — new slot order `10h, 14h, 11h, 12h, 13h`, scheduled-post verification (D+1 cascade when >5/day), detailed result dialog (replaces `alert()`), status bar below header, version badge in title. File renamed to `Schedule Calendar - v21`. `v19` archived.
- 2026-04-29: added `Admin Export Posts Markdown RAG - v1` snippet.
- 2026-04-29: README restructured (FR/EN) and RAG export workflow documented.

## 🔗 Links
- FR README: `README.md`
- Active snippets: `snippets/canonical/`
