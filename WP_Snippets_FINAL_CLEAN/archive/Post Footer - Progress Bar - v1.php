
/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: WP_Snippets
 * Source path: WP_Snippets/WP_POST - progress bar.php
 * Display name: WP_POST - progress bar
 * Scope: front-end
 * Online snippet: non
 * Exact duplicate group: oui (ac445a3127c8…, 2 membres)
 * Canonical exact group ID: 92
 * Version family: DUP POST - progress bar (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets/WP_POST - progress bar.php
 * Is family latest: oui
 * Archive reasons: exact-duplicate
 * Features: head-injection, footer-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_body_open, wp_head, wp_footer
 * Fonctions clefs: add_reading_progress_bar, add_progress_bar_styles, add_progress_bar_script
 * Lignes / octets (brut): 47 / 1395
 * Hash code normalise (sha256): ac445a3127c87b0dd75d22a9968f3d3f184c27e095f300e6e8560504ee6d2bf7
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: post-progress-bar__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-progress-bar__v001.php
 * Bucket FINAL: archive
 * Statut: LOCAL
 * Cluster principal: post_footer_ui
 * Clusters secondaires: aucun
 * Domaine: post-front
 * Confiance: low
 * Scores (top): post_footer_ui=5
 * Raisons principales: footer
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

-CANONICAL-META
 * Role final: archive
 * Source root: WP_Snippets
 * Source path: WP_Snippets/WP_POST - progress bar.php
 * Display name: WP_POST - progress bar
 * Scope: front-end
 * Online snippet: non
 * Exact duplicate group: oui (ac445a3127c8…, 2 membres)
 * Canonical exact group ID: 92
 * Version family: DUP POST - progress bar (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets/WP_POST - progress bar.php
 * Is family latest: oui
 * Archive reasons: exact-duplicate
 * Features: head-injection, footer-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_body_open, wp_head, wp_footer
 * Fonctions clefs: add_reading_progress_bar, add_progress_bar_styles, add_progress_bar_script
 * Lignes / octets (brut): 47 / 1395
 * Hash code normalise (sha256): ac445a3127c87b0dd75d22a9968f3d3f184c27e095f300e6e8560504ee6d2bf7
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

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
?>