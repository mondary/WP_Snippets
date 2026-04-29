# WP_Snippets

[🇬🇧 EN](README_en.md) · [🇫🇷 FR](README.md)

✨ WordPress snippet collection focused on admin productivity, publishing, and editorial workflows.

## ✅ Features
- Ready-to-use snippet base in `snippets/canonical/`.
- History and variants in `snippets/archive/`.
- WordPress sync workflow via `CODE_SNIPPETS_SYNC/`.
- New RAG export snippet: one Markdown file per post (ZIP).

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
- 2026-04-29: added `Admin Export Posts Markdown RAG - v1` snippet.
- 2026-04-29: README restructured (FR/EN) and RAG export workflow documented.

## 🔗 Links
- FR README: `README.md`
- Active snippets: `snippets/canonical/`
