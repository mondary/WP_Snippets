# WP_Snippets

[🇬🇧 EN](README_en.md) · [🇫🇷 FR](README.md)

✨ WordPress snippet collection focused on admin productivity, publishing, and editorial workflows.

## ✅ Features
- Ready-to-use snippet base in `snippets/canonical/`.
- History and variants in `snippets/archive/`.
- WordPress sync workflow via `CODE_SNIPPETS_SYNC/`.
- New RAG export snippet: one Markdown file per post (ZIP).
- **Schedule Calendar V21** with featured images, drag & drop, draft reallocation, and scheduled-post verification (slots 10am, 2pm, 11am, 12pm, 1pm).

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

### Schedule Calendar V21
- File: `snippets/canonical/🧭 ADMIN MENUBAR - Schedule Calendar - v21.php`
- UI: "Calendar" menu bar entry in WordPress admin + version badge in the page title.
- **Featured images** as thumbnails in day cards (red border + 🖼️ when missing).
- **Stable month view**: prev/next navigation, `+1 month` / `Full year` options.
- **Drag & Drop**: reschedule posts via drag, automatic day rebalance.
- **Priority slots `10h, 14h, 11h, 12h, 13h`**: 1st post → 10am, 2nd → 2pm, then 11am/12pm/1pm.
- **Draft reallocation**: dedicated button + posts-per-day selector (1 to 5). Drafts are rescheduled from D+1 onward, skipping taken slots.
- **Scheduled-post verification** (V21): on reallocation, `future` posts are realigned to correct slots; overflow > 5/day cascades to D+1.
- **Detailed result dialog** (V21): replaces the native `alert()`, shows reallocation + verified-scheduled sections (fixed, shifted, unchanged), scrollable.
- **Status bar** (V21) below the header, full width.
- **Filters**: title search, category filter, month/year selection, duplicate detection.

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
- 2026-06-16: Schedule Calendar `v21` — fix empty result popup (reconstructed already-built sections). Status bar now sticky below header (stays visible on scroll). Dialog body scroll fixed (flex: 1 + min-height: 0).
- 2026-06-15: Schedule Calendar `v21` — new slot order `10h, 14h, 11h, 12h, 13h`, scheduled-post verification (D+1 cascade when >5/day), detailed result dialog (replaces `alert()`), status bar below header, version badge in title. File renamed to `Schedule Calendar - v21`. `v19` archived.
- 2026-04-29: added `Admin Export Posts Markdown RAG - v1` snippet.
- 2026-04-29: README restructured (FR/EN) and RAG export workflow documented.

## 🔗 Links
- FR README: `README.md`
- Active snippets: `snippets/canonical/`
