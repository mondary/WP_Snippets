
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: archives
 * Source path: archives/WP_POST - Footer copyright.php
 * Display name: WP_POST - Footer copyright
 * Scope: front-end
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: WP_POST - Footer copyright (1 variantes)
 * Version: v1
 * Recommended latest in family: archives/WP_POST - Footer copyright.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: head-injection, footer-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head, wp_footer
 * Fonctions clefs: custom_gradient_footer
 * Lignes / octets (brut): 59 / 1605
 * Hash code normalise (sha256): c3a71b22b48f0b9b1394eb266cea7999f147ea1039ecfb8b4858850fb41667df
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: LOCAL__front-end__wp-post-footer-copyright__v1__src-archives.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/LOCAL__front-end__wp-post-footer-copyright__v1__src-archives.php
 * Bucket FINAL: canonical
 * Statut: LOCAL
 * Cluster principal: post_footer_ui
 * Clusters secondaires: aucun
 * Domaine: post-front
 * Confiance: high
 * Scores (top): post_footer_ui=10
 * Raisons principales: footer, copyright
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

NONICAL-META
 * Role final: canonical
 * Source root: archives
 * Source path: archives/WP_POST - Footer copyright.php
 * Display name: WP_POST - Footer copyright
 * Scope: front-end
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: WP_POST - Footer copyright (1 variantes)
 * Version: v1
 * Recommended latest in family: archives/WP_POST - Footer copyright.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: head-injection, footer-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head, wp_footer
 * Fonctions clefs: custom_gradient_footer
 * Lignes / octets (brut): 59 / 1605
 * Hash code normalise (sha256): c3a71b22b48f0b9b1394eb266cea7999f147ea1039ecfb8b4858850fb41667df
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* 
Title: Custom Gradient Footer
Description: Adds a custom footer with gradient effect and large text
Code Type: Universal Footer
*/

// Add the CSS styles
add_action('wp_head', function() {
    ?>
    <style>
        .custom-footer {
            padding: 0;
            text-align: center;
            background: linear-gradient(to top, rgba(255,255,255,1) 0%, rgba(255,255,255,0.7) 30%, rgba(255,255,255,0.4) 60%, rgba(255,255,255,0) 100%);
            position: fixed;
            bottom: 0;
            width: 100%;
            height: 100px;
            overflow: hidden;
            position: relative;
        }

        .footer-mask {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to top, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.6) 30%, rgba(255,255,255,0.3) 60%, rgba(255,255,255,0) 100%);
            z-index: 1;
        }

        .footer-text {
            color:rgb(168, 164, 164);
            font-size: 80px;
            font-weight: bold;
            letter-spacing: 2px;
            position: absolute;
            bottom: -40px;
            width: 100%;
            opacity: 0.5;
        }
    </style>
    <?php
});

// Add the footer HTML
function custom_gradient_footer() {
    ?>
    <footer class="custom-footer">
        <div class="footer-mask"></div>
        <div class="footer-text">© 2025 Clement MONDARY</div>
    </footer>
    <?php
}

// Hook the footer to display at the bottom of the page
add_action('wp_footer', 'custom_gradient_footer');