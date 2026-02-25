/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: A TRIER
 * Source path: A TRIER/WP_ADMIN search/admin-search.php
 * Display name: admin-search
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: admin-search (1 variantes)
 * Version: v2
 * Recommended latest in family: A TRIER/WP_ADMIN search/admin-search.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: ajax, search-ui, admin-bar, head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_before_admin_bar_render, admin_head, wp_head, wp_ajax_admin_search_posts, wp_ajax_nopriv_admin_search_posts
 * Fonctions clefs: add_admin_bar_search, admin_bar_search_styles, showSearch, hideSearch, admin_search_posts_callback
 * Actions AJAX: admin_search_posts
 * Lignes / octets (brut): 194 / 7299
 * Hash code normalise (sha256): 1b57ddc9a0d986092531d9246d2fbfe1c5f995917b170ec556ba00a597f89aab
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: admin-search__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/admin-search__v001.php
 * Resume fonctionnalites: interactions AJAX, interface de recherche, UI frontend (CSS/HTML), 5 hook(s) WP, 5 fonction(s) clef
 * Features detectees: ajax, admin-menubar, search-ui, css-ui, footer-head-injection
 * Dependances probables: WordPress AJAX
 * Hooks WP: wp_before_admin_bar_render, admin_head, wp_head, wp_ajax_admin_search_posts, wp_ajax_nopriv_admin_search_posts
 * Fonctions clefs: add_admin_bar_search, admin_bar_search_styles, showSearch, hideSearch, admin_search_posts_callback
 * Actions AJAX: admin_search_posts
 * Selecteurs / IDs: .admin-bar-search-input, .admin-bar-search-results, .admin-bar-search-container
 * APIs WP detectees: add_admin_bar_search, is_admin, add_menu, home_url, get_search_query, add_action, admin_url, the_post, get_the_title, get_permalink, wp_reset_postdata, wp_send_json
 * Signatures contenu: inline-style, inline-script, html-markup
 * Lignes / octets: 217 / 8291
 * Empreinte code (sha256): dc5089ecc74e110e54b7cbe87e5d50db1b0af58da982183f5a76526c5a270bac
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: admin-search__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/admin-search__v001.php
 * Bucket FINAL: archive
 * Statut: LOCAL
 * Cluster principal: admin_menubar
 * Clusters secondaires: search_ui, rest_ajax_integration
 * Domaine: admin
 * Confiance: medium
 * Scores (top): admin_menubar=12, search_ui=10, rest_ajax_integration=6, post_footer_ui=5, frontend_ui_widget=4
 * Raisons principales: admin-menubar, menubar
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

function add_admin_bar_search() {
    global $wp_admin_bar;
    if ( ! is_admin() ) {
        return;
    }
    $wp_admin_bar->add_menu( array(
        'id'    => 'admin-search',
        'title' => '<div class="admin-bar-search-container" style="display:none;">
                        <form role="search" method="get" class="admin-bar-search-form" action="' . esc_url( home_url( '/' ) ) . '">
                        <input type="search" class="admin-bar-search-input" placeholder="Rechercher..." value="' . get_search_query() . '" name="s" />
                        </form>
                        <div class="admin-bar-search-results" style="display:none;"></div>
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
    position: absolute; /* Pour la positionner relative à la barre d'admin */
    left: 0; /* Positionnement à gauche */
    top: 100%; /* Juste en dessous de la barre d'admin */
    z-index: 9999;
    width: 300px; /* Ajuste selon la taille souhaitée */
    background-color: rgba(40, 42, 54, 0.9);
    border-radius: 4px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
    display: none;
    padding: 20px;
}

        .admin-bar-search-form {
            display: flex;
            align-items: center;
            padding: 5px;
        }
        .admin-bar-search-input {
            border: 1px solid #6272a4;
            padding: 10px;
            border-radius: 4px;
            font-size: 16px;
            color: #f8f8f2;
            background-color: #282a36;
            width: 100%;
        }
        .admin-bar-search-input:focus {
            border-color: #bd93f9;
            outline: none;
            box-shadow: 0 0 0 1px #bd93f9;
        }
        .admin-bar-search-results {
            position: relative;
            background: #282a36;
            border: 1px solid #6272a4;
            border-radius: 0 0 4px 4px;
            display: none;
            z-index: 1000;
            margin-top: 10px; /* Pour séparer les résultats de la recherche */
        }
        .admin-bar-search-results a {
            display: block;
            padding: 8px 12px;
            text-decoration: none;
            color: #f8f8f2;
        }
        .admin-bar-search-results a:hover {
            background-color: #44475a;
        }
        .admin-bar-search-input::placeholder {
            color: #6272a4;
        }
    </style>
    <script>
         document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('.admin-bar-search-input');
            const searchResults = document.querySelector('.admin-bar-search-results');
            const searchContainer = document.querySelector('.admin-bar-search-container');

            function showSearch() {
    const adminMenu = document.querySelector('.ab-top-menu.ab-top-secondary');
    const searchContainer = document.querySelector('.admin-bar-search-container');
    if (adminMenu) {
        const rect = adminMenu.getBoundingClientRect();
        searchContainer.style.top = `${rect.bottom}px`; // Position en dessous de la barre admin
        searchContainer.style.left = `${rect.left}px`; // Aligner à gauche
    }
    searchContainer.style.display = 'block';
    searchInput.focus();
}


            function hideSearch() {
                searchContainer.style.display = 'none';
                searchInput.value = '';
                searchResults.innerHTML = '';
            }

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
                        noResults.textContent = 'Aucun résultat trouvé';
                        searchResults.appendChild(noResults);
                        searchResults.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Erreur :', error);
                    searchResults.innerHTML = '<p>Erreur lors de la récupération des résultats</p>';
                    searchResults.style.display = 'block';
                });
            });

            document.addEventListener('keydown', function(event) {
                if (event.ctrlKey && event.key === 'k' || event.metaKey && event.key === 'k') {
                    event.preventDefault();
                    showSearch();
                }
                if (event.key === 'Escape') {
                    hideSearch();
                }
            });

            // Masquer le champ de recherche en cliquant en dehors
            document.addEventListener('click', function(event) {
                if (!searchContainer.contains(event.target) && searchContainer.style.display === 'block') {
                    hideSearch();
                }
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