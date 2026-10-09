# WP_Snippets

[🇬🇧 EN](README_en.md) · [🇫🇷 FR](README.md)

✨ WordPress snippet collection focused on admin productivity, publishing, and editorial workflows — version **2026.10.17**.

[Support the project on Ko-fi](https://ko-fi.com/pouark)

## ✅ Features
- Ready-to-use snippet base in `snippets/canonical/`.
- History and variants in `snippets/archive/`.
- WordPress sync workflow via `CODE_SNIPPETS_SYNC/`.
- New RAG export snippet: one Markdown file per post (ZIP).
- **Schedule Calendar V26** with featured images, drag & drop, draft reallocation starting from today, and scheduled-post verification (slots 10am, 2pm, 11am, 12pm, 1pm). Reallocation now starts from **today** and respects shared publish+future+draft capacity (max `articles_per_day` per day). Past slots are automatically filtered.
- **"Scheduled Posts" submenu** in the left sidebar, under the Posts menu, with a badge showing the scheduled post count.
- **Missing Featured Image Detection** — filter in the post list, "No Image" submenu with counter, and dedicated page listing published posts without a featured image.
- **Media Library usage audit** — compact view filters and small “Analyze usage” / “Recalculate size” buttons. See `snippets/canonical/MEDIA 🖼️ IMAGES - Orphans - v5.php`.
- **Site statistics page** — aggregated views and visitors, yearly charts, and CSV import under Tools. See `snippets/canonical/FRONTEND 📊 STATS - Site Stats Page - 2026.10.1.php`.
- **Jump-to-page pagination** — every ellipsis group in the Kadence pagination (home, archives, search) becomes a clickable "⋯" pill: a popover with a "Page __ / 298" input (Enter or "Go") and −50 / −10 / +10 / +50 shortcuts. One click for both targeted jumps and exploration, cache-friendly. See `snippets/canonical/FRONTEND 📄 PAGINATION - Jump Select - v3.php`.
- **Duplicate Posts Detection** — "Doublons 🔍" page under Posts: compares titles (normalized keywords) and/or shared external URLs, groups pairs/triples (smallest groups first) with 72px thumbnails, status badge and date gaps; clicking a title opens the article in a dedicated window (face-to-face); AJAX trashing without reload + checkboxes and bulk "Trash" action. Defaults: Titles only, 70%, 12 months. A “🔍 Doublons” button is also available directly in the Posts list, alongside the annual calendar button. See `snippets/canonical/ADMIN 🧰 DETECT - Duplicates Posts - v6.php`.
- **Search restored** — typing a character on the public site opens Kadence search; the admin bar also has a field to search posts. Clean v5 snippets support LiteSpeed delayed scripts. See `snippets/canonical/POST 🔎 SEARCH - Auto - v5.php` and `snippets/canonical/ADMIN ⚙️ MENUBAR - Search - v5.php`.
- **Action bar `v4`** — a discreet sticky bar gathers Google News, RSS, Newsletter, fullscreen Diaporama, Scheduled posts, Stats, Ko-fi and Back to top. It stays readable on desktop and scrolls horizontally on mobile. See `snippets/canonical/FRONTEND 🌸 FAB - Hub Flottant - v4.php`.

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

### Automatic editor slot
- File: `snippets/canonical/ADMIN 📅 SCHEDULER - Editor Next Free Slot - v3.php`
- When opening a new post, draft, or scheduled post in Gutenberg, replaces the “immediately” date with the next free calendar slot.
- Slots and occupancy: `10am, 2pm, 11am, 12pm, 1pm`; published, scheduled, and draft posts are considered. Past slots are ignored.
- The date picker shows dots below occupied days: red for drafts, green for published posts, and blue for scheduled posts.

### Missing Featured Image Detection
- File: `snippets/canonical/ADMIN 🧰 DETECT - Missing Featured Images - v1.php`
- UI: "No Image" submenu in the left sidebar, under the Posts menu (red badge = count of posts without featured image).
- "With/Without featured image" filter in the post list (`edit.php`).
- Dedicated page listing all published posts without a featured image, with Edit/View links.

### Duplicate Posts Detection
- File: `snippets/canonical/ADMIN 🧰 DETECT - Duplicates Posts - v6.php`
- UI: "Doublons 🔍" submenu under Posts.
- Detects articles covering the same subject: title similarity (normalized keywords — accents, plurals and stop words removed) and/or shared external URLs in the content (query strings and `www.` normalized, internal links excluded).
- Groups pairs and triples, shows the date gap between each article and the oldest one in its group (gaps ≤ 31 days highlighted in red).
- Groups pairs and triples, shows the date gap between each article and the oldest one in its group (gaps ≤ 31 days highlighted in red). Groups sorted smallest first, then by score: precise pairs first, big (often false-positive) groups last.
- Dates in `YYYY/MM/DD HH:MM` format, gap in calendar days (two same-day articles = 0 day).
- Standard 72px featured-image thumbnail in front of each title; columns: Title > Status > Date > Gap > Why > Actions.
- Clicking a title opens the article in a dedicated window (1050×950) to compare two articles side by side.
- AJAX trashing: no reload and no scroll jump — the row disappears in place and the group collapses when a single article remains.
- Bulk actions: checkbox per article, per-group "select all", "⚡ Pre-check duplicates" (checks everything except the reference), "Uncheck all" and "🗑 Trash (n)" with progress.
- Filters: method (Titles / URLs / Titles + URLs, default: Titles), similarity threshold from 70% to 100% in steps of 5 (default: 70%), period (default: 12 months), max gap with the reference (Same day / 7 days / 1 month / 3 months / 6 months), statuses (published, scheduled, drafts, pending).
- Per-article actions: Edit, Trash (redirects back to the Doublons page).
- No automatic deletion: the tool lists, you decide.

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
