/* CLM-CREATED-AT: 2026-02-25 */
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/active/global/087__id-147__admin-recentrer-gutemberg.php
 * Display name: ADMIN - recentrer Gutemberg
 * Scope: global
 * Online snippet: oui
 * Online active: oui
 * Online ID: 147
 * Online modified: 2025-11-14 10:25:55
 * Online revision: 1
 * Exact duplicate group: non
 * Version family: ADMIN - recentrer Gutemberg (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/active/global/087__id-147__admin-recentrer-gutemberg.php
 * Is family latest: oui
 * Canonical reasons: unique-code, protected-online-active
 * Features: footer-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_footer
 * Fonctions clefs: applyPadding
 * Lignes / octets (brut): 43 / 1319
 * Hash code normalise (sha256): 2fa142dc8645d94d92cad12bac3633f6d326052b6b8f2e0c0daead76c5b29d52
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: ACTIVE__global__admin-recentrer-gutemberg__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__admin-recentrer-gutemberg__v1__src-wp_snippets_online_current.php
 * Resume fonctionnalites: integration Gutenberg, UI frontend (CSS/HTML), 1 hook(s) WP, 1 fonction(s) clef
 * Features detectees: gutenberg, css-ui, footer-head-injection
 * Dependances probables: Gutenberg JS
 * Hooks WP: admin_footer
 * Fonctions clefs: applyPadding
 * APIs WP detectees: add_action
 * Signatures contenu: inline-script, html-markup
 * Lignes / octets: 56 / 1927
 * Empreinte code (sha256): b525b0434929edb6e942b8755a0e8552b5beefe152c3745133bacc1ca46b0ed7
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: ACTIVE__global__admin-recentrer-gutemberg__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__admin-recentrer-gutemberg__v1__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: ACTIVE
 * Cluster principal: gutenberg_editor
 * Clusters secondaires: post_footer_ui, frontend_ui_widget
 * Domaine: global
 * Confiance: medium
 * Scores (top): gutenberg_editor=6, post_footer_ui=5, frontend_ui_widget=4
 * Raisons principales: gutenberg
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

add_action('admin_footer', function () {
    ?>
    <script>
    // Ce script s'exécute uniquement dans l'admin
    document.addEventListener("DOMContentLoaded", () => {
        function applyPadding() {
            const iframe = document.querySelector('iframe[name="editor-canvas"]');
            if (!iframe) return;

            const doc = iframe.contentDocument || iframe.contentWindow.document;
            if (!doc) return;

            const el = doc.querySelector(".editor-styles-wrapper.block-editor-writing-flow");
            if (!el) return;

            el.style.paddingLeft = "20%";
        }

        // premier essai
        applyPadding();

        // on insiste un peu (Gutenberg charge lentement son iframe)
        const interval = setInterval(applyPadding, 500);

        // on arrête au bout de 10s
        setTimeout(() => clearInterval(interval), 10000);
    });
    </script>
    <?php
});
