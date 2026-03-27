/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/044__id-99__admin-list-column-size-post-modif.php
 * Display name: ADMIN - List column size (POST) + modif
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 99
 * Online modified: 2025-03-31 09:14:29
 * Online revision: 4
 * Exact duplicate group: non
 * Version family: ADMIN - List column size (POST) + modif (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/044__id-99__admin-list-column-size-post-modif.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_init, admin_menu, admin_head
 * Fonctions clefs: column_width_settings_page, column_width_settings_init, column_width_menu, custom_admin_column_width
 * Lignes / octets (brut): 123 / 5544
 * Hash code normalise (sha256): 99e3239c5feee97e4e7af63ae5a4369e865a5f7d9205e0ba5202661c635821e4
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: INACTIVE__global__admin-list-column-size-post-modif__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__admin-list-column-size-post-modif__v1__src-wp_snippets_online_current.php
 * Resume fonctionnalites: customisation interface admin, UI frontend (CSS/HTML), automatisation date/programmation, 3 hook(s) WP, 4 fonction(s) clef
 * Features detectees: admin-columns, admin-ui, scheduler-date, css-ui, footer-head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_init, admin_menu, admin_head
 * Fonctions clefs: column_width_settings_page, column_width_settings_init, column_width_menu, custom_admin_column_width
 * APIs WP detectees: get_option, add_action, add_submenu_page
 * Signatures contenu: inline-style, html-markup
 * Lignes / octets: 136 / 6266
 * Empreinte code (sha256): 4141b852c0a8ffc5d042f3da9ce21ea03475f9a28391f5c01387f5257c911d19
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: INACTIVE__global__admin-list-column-size-post-modif__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__admin-list-column-size-post-modif__v1__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: INACTIVE
 * Cluster principal: admin_ui_settings
 * Clusters secondaires: scheduler_posts, admin_columns_list, post_footer_ui, frontend_ui_widget
 * Domaine: admin
 * Confiance: medium
 * Scores (top): admin_ui_settings=8, scheduler_posts=8, admin_columns_list=6, post_footer_ui=5, frontend_ui_widget=4
 * Raisons principales: admin-ui, settings
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

// Ajouter un formulaire dans l'admin pour saisir les pourcentages
function column_width_settings_page() {
    ?>
    <div class="wrap">
        <h1>Réglage des largeurs des colonnes</h1>
        <form method="post" action="options.php">
            <?php
            // Utiliser la fonction nonce pour sécuriser la soumission
            settings_fields('column_width_settings_group');
            do_settings_sections('column_width_settings');

            // Récupérer les largeurs enregistrées ou les valeurs par défaut
            $title_width = get_option('custom_column_width_title', '30');
            $author_width = get_option('custom_column_width_author', '15');
            $date_width = get_option('custom_column_width_date', '15');
            $comments_width = get_option('custom_column_width_comments', '10');
            $categories_width = get_option('custom_column_width_categories', '15');
            $views_width = get_option('custom_column_width_views', '15');
            ?>

            <table class="form-table">
                <tr>
                    <th><label for="title_width">Titre</label></th>
                    <td><input type="number" id="title_width" name="custom_column_width_title" value="<?php echo esc_attr($title_width); ?>" /> %</td>
                </tr>
                <tr>
                    <th><label for="author_width">Auteur</label></th>
                    <td><input type="number" id="author_width" name="custom_column_width_author" value="<?php echo esc_attr($author_width); ?>" /> %</td>
                </tr>
                <tr>
                    <th><label for="date_width">Date</label></th>
                    <td><input type="number" id="date_width" name="custom_column_width_date" value="<?php echo esc_attr($date_width); ?>" /> %</td>
                </tr>
                <tr>
                    <th><label for="comments_width">Commentaires</label></th>
                    <td><input type="number" id="comments_width" name="custom_column_width_comments" value="<?php echo esc_attr($comments_width); ?>" /> %</td>
                </tr>
                <tr>
                    <th><label for="categories_width">Catégories</label></th>
                    <td><input type="number" id="categories_width" name="custom_column_width_categories" value="<?php echo esc_attr($categories_width); ?>" /> %</td>
                </tr>
                <tr>
                    <th><label for="views_width">Vues</label></th>
                    <td><input type="number" id="views_width" name="custom_column_width_views" value="<?php echo esc_attr($views_width); ?>" /> %</td>
                </tr>
            </table>

            <p class="submit">
                <input type="submit" class="button-primary" value="Sauvegarder les largeurs">
            </p>
        </form>
    </div>
    <?php
}

// Enregistrer les paramètres de largeur des colonnes
function column_width_settings_init() {
    register_setting('column_width_settings_group', 'custom_column_width_title');
    register_setting('column_width_settings_group', 'custom_column_width_author');
    register_setting('column_width_settings_group', 'custom_column_width_date');
    register_setting('column_width_settings_group', 'custom_column_width_comments');
    register_setting('column_width_settings_group', 'custom_column_width_categories');
    register_setting('column_width_settings_group', 'custom_column_width_views');
}
add_action('admin_init', 'column_width_settings_init');

// Ajouter un menu dans l'admin pour afficher le formulaire
function column_width_menu() {
    add_submenu_page(
        'edit.php',
        'Réglages des largeurs des colonnes',
        'Largeurs des colonnes',
        'manage_options',
        'column_width_settings',
        'column_width_settings_page'
    );
}
add_action('admin_menu', 'column_width_menu');

// Appliquer les largeurs enregistrées au niveau des colonnes
function custom_admin_column_width() {
    $title_width = get_option('custom_column_width_title', '30');
    $author_width = get_option('custom_column_width_author', '15');
    $date_width = get_option('custom_column_width_date', '15');
    $comments_width = get_option('custom_column_width_comments', '10');
    $categories_width = get_option('custom_column_width_categories', '15');
    $views_width = get_option('custom_column_width_views', '15');
    
    echo '<style>
        .wp-list-table .column-title {
            width: ' . $title_width . '% !important;
        }
        .wp-list-table .column-author {
            width: ' . $author_width . '% !important;
        }
        .wp-list-table .column-date {
            width: ' . $date_width . '% !important;
        }
        .wp-list-table .column-comments {
            width: ' . $comments_width . '% !important;
        }
        .wp-list-table .column-categories {
            width: ' . $categories_width . '% !important;
        }
        .wp-list-table .column-iawp_total_views {
            width: ' . $views_width . '% !important;
        }
    </style>';
}
add_action('admin_head', 'custom_admin_column_width');
