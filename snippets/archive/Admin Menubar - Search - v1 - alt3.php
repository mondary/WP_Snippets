/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/018__id-30__admin-search-v0.php
 * Display name: ADMIN Search v0
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 30
 * Online modified: 2025-02-10 15:03:39
 * Online revision: 18
 * Exact duplicate group: non
 * Version family: ADMIN Search v0 (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/018__id-30__admin-search-v0.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: ajax, search-ui, admin-bar, head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_before_admin_bar_render, admin_head, wp_head, wp_ajax_admin_search_posts, wp_ajax_nopriv_admin_search_posts
 * Fonctions clefs: add_admin_bar_search, admin_bar_search_styles, admin_search_posts_callback
 * Actions AJAX: admin_search_posts
 * Lignes / octets (brut): 162 / 5779
 * Hash code normalise (sha256): 09898632620632893a292214a65a36ec3b92cc059f81c6b7bbc8e1c195959caf
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: INACTIVE__global__admin-search-v0__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__admin-search-v0__v1__src-wp_snippets_online_current.php
 * Resume fonctionnalites: interactions AJAX, interface de recherche, UI frontend (CSS/HTML), 5 hook(s) WP, 3 fonction(s) clef
 * Features detectees: ajax, admin-menubar, search-ui, css-ui, footer-head-injection
 * Dependances probables: WordPress AJAX
 * Hooks WP: wp_before_admin_bar_render, admin_head, wp_head, wp_ajax_admin_search_posts, wp_ajax_nopriv_admin_search_posts
 * Fonctions clefs: add_admin_bar_search, admin_bar_search_styles, admin_search_posts_callback
 * Actions AJAX: admin_search_posts
 * Selecteurs / IDs: .admin-bar-search-input, .admin-bar-search-results
 * APIs WP detectees: add_admin_bar_search, is_admin, add_menu, home_url, get_search_query, add_action, admin_url, the_post, get_the_title, get_permalink, wp_reset_postdata, wp_send_json
 * Signatures contenu: inline-style, inline-script, html-markup
 * Lignes / octets: 175 / 6467
 * Empreinte code (sha256): f0588ee3f5d2b5f0f16781edb7da44d2b8fb03aa318cf2946dbc1c7ee16ff6de
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: INACTIVE__global__admin-search-v0__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__admin-search-v0__v1__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: INACTIVE
 * Cluster principal: admin_menubar
 * Clusters secondaires: search_ui, rest_ajax_integration
 * Domaine: admin
 * Confiance: medium
 * Scores (top): admin_menubar=12, search_ui=10, rest_ajax_integration=6, post_footer_ui=5, frontend_ui_widget=4
 * Raisons principales: admin-menubar, menubar
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

/**
 * Add a search form to the WordPress admin bar.
 */
function add_admin_bar_search() {
    global $wp_admin_bar;
    if ( ! is_admin() ) {
        return;
    }
    $wp_admin_bar->add_menu( array(
        'id'    => 'admin-search',
        'title' => '<div class="admin-bar-search-container">
                        <form role="search" method="get" class="admin-bar-search-form" action="' . esc_url( home_url( '/' ) ) . '">
                            <input type="search" class="admin-bar-search-input" placeholder="Rechercher des articles..." value="' . get_search_query() . '" name="s" />
                            <div class="admin-bar-search-results"></div>
                        </form>
                    </div>',
        'meta'  => array(
            'class' => 'admin-bar-search-menu',
        ),
    ) );
}
add_action( 'wp_before_admin_bar_render', 'add_admin_bar_search' );

function admin_bar_search_styles() {
    if ( ! is_admin() ) {
        return;
    }
    ?>
    <style type="text/css">
        .admin-bar-search-menu {
            padding: 0;
        }
        .admin-bar-search-container {
            position: relative;
        }
        .admin-bar-search-form {
            display: flex;
            align-items: center;
            margin: 0;
            padding: 0;
        }
        .admin-bar-search-input {
            border: 1px solid #ddd;
            padding: 5px 10px;
            border-radius: 4px;
            margin: 0;
            font-size: 13px;
            line-height: 1.5;
            height: 30px;
            transition: width 0.3s ease;
            width: 150px;
        }
        .admin-bar-search-input:focus {
            border-color: #007cba;
            outline: none;
            box-shadow: 0 0 0 1px #007cba;
            width: 300px;
        }
        .admin-bar-search-results {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background: #fff;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 4px 4px;
            display: none;
            z-index: 1000;
        }
        .admin-bar-search-input:focus + .admin-bar-search-results {
            display: block;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('.admin-bar-search-input');
            const searchResults = document.querySelector('.admin-bar-search-results');

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value;
                if (searchTerm.length < 3) {
                    searchResults.innerHTML = '';
                    searchResults.style.display = 'none';
                    return;
                }

                fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=admin_search_posts&s=' + encodeURIComponent(searchTerm),
                })
                .then(response => response.json())
                .then(data => {
                    searchResults.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(item => {
                            const resultItem = document.createElement('a');
                            resultItem.href = item.link;
                            resultItem.textContent = item.title;
                            searchResults.appendChild(resultItem);
                        });
                        searchResults.style.display = 'block';
                    } else {
                        const noResults = document.createElement('p');
                        noResults.textContent = 'No results found';
                        searchResults.appendChild(noResults);
                        searchResults.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    searchResults.innerHTML = '<p>Error fetching results</p>';
                    searchResults.style.display = 'block';
                });
            });
        });
    </script>
    <?php
}
add_action( 'admin_head', 'admin_bar_search_styles' );
add_action( 'wp_head', 'admin_bar_search_styles' );

function admin_search_posts_callback() {
    $search_term = isset( $_POST['s'] ) ? sanitize_text_field( $_POST['s'] ) : '';
    $args = array(
        's'              => $search_term,
        'post_type'      => 'post',
        'posts_per_page' => 5,
    );
    $query = new WP_Query( $args );
    $results = array();
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $results[] = array(
                'title' => get_the_title(),
                'link'  => get_permalink(),
            );
        }
        wp_reset_postdata();
    }
    wp_send_json( $results );
}
add_action( 'wp_ajax_admin_search_posts', 'admin_search_posts_callback' );
add_action( 'wp_ajax_nopriv_admin_search_posts', 'admin_search_posts_callback' );
