/* CLM-CREATED-AT: 2026-02-25 */
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/active/global/028__id-51__post-progress-bar.php
 * Display name: POST - progress bar
 * Scope: global
 * Online snippet: oui
 * Online active: oui
 * Online ID: 51
 * Online modified: 2025-02-10 17:25:31
 * Online revision: 4
 * Exact duplicate group: oui (ac445a3127c8…, 2 membres)
 * Canonical exact group ID: 92
 * Version family: DUP POST - progress bar (1 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets_Online_Current/active/global/028__id-51__post-progress-bar.php
 * Is family latest: oui
 * Canonical reasons: exact-group-canonical, protected-online-active
 * Features: head-injection, footer-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_body_open, wp_head, wp_footer
 * Fonctions clefs: add_reading_progress_bar, add_progress_bar_styles, add_progress_bar_script
 * Lignes / octets (brut): 59 / 1874
 * Hash code normalise (sha256): ac445a3127c87b0dd75d22a9968f3d3f184c27e095f300e6e8560504ee6d2bf7
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: ACTIVE__global__post-progress-bar__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__post-progress-bar__v2__src-wp_snippets_online_current.php
 * Resume fonctionnalites: UI frontend (CSS/HTML), 3 hook(s) WP, 3 fonction(s) clef
 * Features detectees: css-ui, footer-head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_body_open, wp_head, wp_footer
 * Fonctions clefs: add_reading_progress_bar, add_progress_bar_styles, add_progress_bar_script
 * Selecteurs / IDs: .reading-progress-bar
 * APIs WP detectees: add_action, add_reading_progress_bar, add_progress_bar_styles, add_progress_bar_script
 * Signatures contenu: inline-style, inline-script, html-markup
 * Lignes / octets: 72 / 2533
 * Empreinte code (sha256): 09cd06fe2218acc8340669395d068c917d3685ced1c117c7740f6a0e87c16563
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: ACTIVE__global__post-progress-bar__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__post-progress-bar__v2__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: ACTIVE
 * Cluster principal: post_footer_ui
 * Clusters secondaires: frontend_ui_widget
 * Domaine: post-front
 * Confiance: low
 * Scores (top): post_footer_ui=5, frontend_ui_widget=4
 * Raisons principales: footer
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

// Ajouter la barre de progression dans le header
add_action('wp_body_open', 'add_reading_progress_bar');
function add_reading_progress_bar() {
    echo '<div class="reading-progress-bar"></div>';
}

// Ajouter le CSS
add_action('wp_head', 'add_progress_bar_styles');
function add_progress_bar_styles() {
    ?>
    <style>
        .reading-progress-bar {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 4px;
            background: #007bff;
            z-index: 9999;
            transition: width 0.2s ease-in-out;
        }
    </style>
    <?php
}

// Ajouter le JavaScript
add_action('wp_footer', 'add_progress_bar_script');
function add_progress_bar_script() {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const progressBar = document.querySelector('.reading-progress-bar');
            
            window.addEventListener('scroll', function() {
                const windowHeight = document.documentElement.clientHeight;
                const documentHeight = document.documentElement.scrollHeight - windowHeight;
                const scrolled = window.scrollY;
                
                const progress = (scrolled / documentHeight) * 100;
                progressBar.style.width = progress + '%';
            });
        });
    </script>
    <?php
}
