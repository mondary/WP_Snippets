/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/076__id-136__admin-link-autofill-posts-only.php
 * Display name: ADMIN - Link autofill + posts only
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 136
 * Online modified: 2025-09-12 13:52:45
 * Online revision: 1
 * Exact duplicate group: non
 * Version family: ADMIN - Link autofill + posts only (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/076__id-136__admin-link-autofill-posts-only.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: search-ui
 * Dependances probables: WordPress core hooks
 * Hooks WP: enqueue_block_editor_assets
 * Fonctions clefs: getSelectedText, findVisibleLinkInputs, fillInputWithSelection, tryImmediateFill
 * Lignes / octets (brut): 122 / 5458
 * Hash code normalise (sha256): ea69f93419fdcb8c926c1346edff37db147e7e239972cbdf1d086f49a3b5c9dd
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: INACTIVE__global__admin-link-autofill-posts-only__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__admin-link-autofill-posts-only__v1__src-wp_snippets_online_current.php
 * Resume fonctionnalites: integration Gutenberg, interface de recherche, UI frontend (CSS/HTML), 1 hook(s) WP, 4 fonction(s) clef
 * Features detectees: gutenberg, search-ui, css-ui
 * Dependances probables: Gutenberg JS
 * Hooks WP: enqueue_block_editor_assets
 * Fonctions clefs: getSelectedText, findVisibleLinkInputs, fillInputWithSelection, tryImmediateFill
 * Selecteurs / IDs: .block-editor-link-control, .components-popover, .editor-link, .components-dropdown
 * APIs WP detectees: add_action, wp_register_script, wp_add_inline_script, wp_enqueue_script
 * Signatures contenu: inline-script
 * Lignes / octets: 135 / 6139
 * Empreinte code (sha256): 5ee3913856410c09463c209d45f5551ba3ba048ffa1f25aaee6e3f740f2673ff
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: INACTIVE__global__admin-link-autofill-posts-only__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__admin-link-autofill-posts-only__v1__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: INACTIVE
 * Cluster principal: gutenberg_editor
 * Clusters secondaires: search_ui
 * Domaine: post-front
 * Confiance: medium
 * Scores (top): gutenberg_editor=12, search_ui=10, media_images=4, frontend_ui_widget=2
 * Raisons principales: gutenberg, block_editor
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

/**
 * Auto-préremplissage du champ "Ajouter un lien" dans Gutenberg
 * Placez ce snippet via WP-Code (snippet PHP) et activez l'exécution dans l'admin / éditeur.
 */

add_action( 'enqueue_block_editor_assets', function () {
    // On enregistre un script inline qui s'exécute uniquement dans l'éditeur de blocs.
    wp_register_script( 'mondary-autolink', false, array( 'wp-dom-ready', 'wp-element', 'wp-data' ), '1.0', true );

    $script = <<<'JS'
(function () {
    wp.domReady(function () {

        // Récupère le texte sélectionné (sûr)
        function getSelectedText() {
            try {
                return (window.getSelection && window.getSelection().toString().trim()) || '';
            } catch (e) {
                return '';
            }
        }

        // Cherche les inputs visibles susceptibles d'être l'UI de lien
        function findVisibleLinkInputs() {
            const nodes = Array.from(document.querySelectorAll('input, textarea'));
            return nodes.filter(input => {
                try {
                    if (!input.offsetParent) return false; // pas visible
                    // Si l'input est dans un composant popover/link-control connu
                    if (input.closest('.block-editor-link-control') ||
                        input.closest('.components-popover') ||
                        input.closest('.editor-link') ||
                        input.closest('.components-dropdown')) {
                        return true;
                    }
                    // fallback : type url/search
                    if (input.type === 'url' || input.type === 'search') return true;
                    // fallback : aria / placeholder contenant des mots-clés
                    const aria = (input.getAttribute('aria-label') || '').toLowerCase();
                    const ph = (input.getAttribute('placeholder') || '').toLowerCase();
                    if (aria.indexOf('url') !== -1 || aria.indexOf('lien') !== -1 || aria.indexOf('search') !== -1 || aria.indexOf('rechercher') !== -1) return true;
                    if (ph.indexOf('url') !== -1 || ph.indexOf('lien') !== -1 || ph.indexOf('search') !== -1 || ph.indexOf('rechercher') !== -1) return true;
                    return false;
                } catch (e) { return false; }
            });
        }

        // Remplit l'input avec la sélection si besoin
        function fillInputWithSelection(input) {
            try {
                if (!input) return false;
                if (input.value && input.value.trim().length) return false; // ne pas écraser
                const sel = getSelectedText();
                if (!sel) return false;
                input.focus();
                input.value = sel;
                // déclenche les événements pour que React/Gutenberg détecte le changement
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.dispatchEvent(new Event('change', { bubbles: true }));
                try { input.setSelectionRange(input.value.length, input.value.length); } catch (e) {}
                return true;
            } catch (err) {
                console.error('mondary-autolink: fill error', err);
                return false;
            }
        }

        // Flag pour éviter réinjections infinies pour la même sélection
        let filledForThisSelection = false;

        // Tentative immédiate (au cas où le popover est déjà ouvert)
        setTimeout(function tryImmediateFill() {
            const inputs = findVisibleLinkInputs();
            for (const input of inputs) {
                if (fillInputWithSelection(input)) { filledForThisSelection = true; break; }
            }
        }, 250);

        // Observer pour détecter l'ouverture du popover/link UI
        const observer = new MutationObserver(function () {
            if (filledForThisSelection) return;
            const inputs = findVisibleLinkInputs();
            for (const input of inputs) {
                if (fillInputWithSelection(input)) { filledForThisSelection = true; break; }
            }
        });
        observer.observe(document.body, { childList: true, subtree: true });

        // Réinitialise le flag quand l'utilisateur clique dans l'éditeur (nouvelle sélection possible)
        document.addEventListener('click', function (e) {
            if (e.target.closest('.editor-styles-wrapper, .block-editor-writing-flow, .block-editor-block-list')) {
                filledForThisSelection = false;
            }
            // petite attente : le popover peut s'ouvrir après le click
            setTimeout(function () {
                const inputs = findVisibleLinkInputs();
                for (const input of inputs) {
                    if (fillInputWithSelection(input)) { filledForThisSelection = true; break; }
                }
            }, 150);
        }, true);

    });
})();
JS;

    wp_add_inline_script( 'mondary-autolink', $script );
    wp_enqueue_script( 'mondary-autolink' );
} );
