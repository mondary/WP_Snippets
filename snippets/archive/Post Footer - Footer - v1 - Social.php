
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: archives
 * Source path: archives/WP_POST - Footer social.php
 * Display name: WP_POST - Footer social
 * Scope: front-end
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: WP_POST - Footer social (1 variantes)
 * Version: v1
 * Recommended latest in family: archives/WP_POST - Footer social.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: head-injection, footer-injection
 * Dependances probables: Font Awesome
 * Hooks WP: wp_head, wp_enqueue_scripts, wp_footer
 * Fonctions clefs: render_custom_footer
 * Lignes / octets (brut): 102 / 3398
 * Hash code normalise (sha256): 7425baa77c2e4a9bec6243624f98d0ddbf3f359f32846823967be3db30cd4c92
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: LOCAL__front-end__wp-post-footer-social__v1__src-archives.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/LOCAL__front-end__wp-post-footer-social__v1__src-archives.php
 * Bucket FINAL: canonical
 * Statut: LOCAL
 * Cluster principal: post_footer_ui
 * Clusters secondaires: aucun
 * Domaine: post-front
 * Confiance: high
 * Scores (top): post_footer_ui=10
 * Raisons principales: footer, social
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

* CLM-FEATURES-DESCRIPTION:END */

/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: archives
 * Source path: archives/WP_POST - Footer social.php
 * Display name: WP_POST - Footer social
 * Scope: front-end
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: WP_POST - Footer social (1 variantes)
 * Version: v1
 * Recommended latest in family: archives/WP_POST - Footer social.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: head-injection, footer-injection
 * Dependances probables: Font Awesome
 * Hooks WP: wp_head, wp_enqueue_scripts, wp_footer
 * Fonctions clefs: render_custom_footer
 * Lignes / octets (brut): 102 / 3398
 * Hash code normalise (sha256): 7425baa77c2e4a9bec6243624f98d0ddbf3f359f32846823967be3db30cd4c92
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* 
Title: Footer Social Links
Description: Custom footer with social links, Ko-fi button, and custom icons
Code Type: PHP
*/

// Add custom CSS to style the footer
add_action('wp_head', function() {
    ?>
    <style>
        .custom-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #f8f9fa;
            margin-top: 30px;
        }

        .footer-section {
            flex: 1;
            display: flex;
            align-items: center;
        }

        .social-links {
            justify-content: flex-start;
        }

        .kofi-section {
            justify-content: center;
        }

        .custom-icons {
            justify-content: flex-end;
        }

        .social-links a, .custom-icons a {
            margin: 0 10px;
            color: #333;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .social-links a:hover, .custom-icons a:hover {
            color: #0066cc;
        }
    </style>
    <?php
});

// Footer content function
function render_custom_footer() {
    ?>
    <footer class="custom-footer">
        <!-- Left section - Social links -->
        <div class="footer-section social-links">
            <a href="<?php echo esc_url('https://linkedin.com/your-profile'); ?>" target="_blank" rel="noopener noreferrer">
                <i class="fab fa-linkedin"></i>
            </a>
            <a href="<?php echo esc_url('https://facebook.com/your-page'); ?>" target="_blank" rel="noopener noreferrer">
                <i class="fab fa-facebook"></i>
            </a>
            <a href="<?php echo esc_url('https://twitter.com/your-profile'); ?>" target="_blank" rel="noopener noreferrer">
                <i class="fab fa-twitter"></i>
            </a>
            <a href="<?php echo esc_url('https://instagram.com/your-profile'); ?>" target="_blank" rel="noopener noreferrer">
                <i class="fab fa-instagram"></i>
            </a>
        </div>

        <!-- Center section - Ko-fi button -->
        <div class="footer-section kofi-section">
            <a href='https://ko-fi.com/F1F31908HD' target='_blank'>
                <img height='36' style='border:0px;height:36px;' src='https://storage.ko-fi.com/cdn/kofi6.png?v=6' border='0' alt='Buy Me a Coffee at ko-fi.com' />
            </a>
        </div>

        <!-- Right section - Custom icons -->
        <div class="footer-section custom-icons">
            <a href="<?php echo esc_url('https://github.com/your-profile'); ?>" target="_blank" rel="noopener noreferrer">
                <i class="fab fa-github"></i>
            </a>
            <a href="<?php echo esc_url('https://your-rss-feed.com'); ?>" target="_blank" rel="noopener noreferrer">
                <i class="fas fa-rss"></i>
            </a>
            <a href="<?php echo esc_url('https://your-custom-link.com'); ?>" target="_blank" rel="noopener noreferrer">
                <i class="fas fa-link"></i>
            </a>
        </div>
    </footer>
    <?php
}

// Add Font Awesome for icons
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
});

// Hook the footer to display it
add_action('wp_footer', 'render_custom_footer');