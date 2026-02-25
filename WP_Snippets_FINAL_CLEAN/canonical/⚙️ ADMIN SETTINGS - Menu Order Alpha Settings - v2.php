/* CLM-CREATED-AT: 2026-02-25 */
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/active/admin/027__id-49__admin-menu-order-alpha-settings.php
 * Display name: ADMIN - Menu order alpha +settings
 * Scope: admin
 * Online snippet: oui
 * Online active: oui
 * Online ID: 49
 * Online modified: 2025-02-10 17:24:36
 * Online revision: 20
 * Exact duplicate group: oui (99ff784aee9c…, 2 membres)
 * Canonical exact group ID: 79
 * Version family: DUP ADMIN - Menu order alpha +settings (1 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets_Online_Current/active/admin/027__id-49__admin-menu-order-alpha-settings.php
 * Is family latest: oui
 * Canonical reasons: exact-group-canonical, protected-online-active
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_menu
 * Fonctions clefs: menu_order_settings_page, custom_menu_order
 * Lignes / octets (brut): 125 / 4320
 * Hash code normalise (sha256): 99ff784aee9ccd5fb1b5c009ce104a132e731fa61f342574ab3dfa5ddd6aea94
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: ACTIVE__admin__admin-menu-order-alpha-settings__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__admin__admin-menu-order-alpha-settings__v2__src-wp_snippets_online_current.php
 * Resume fonctionnalites: customisation interface admin, automatisation date/programmation, 1 hook(s) WP, 2 fonction(s) clef
 * Features detectees: admin-ui, scheduler-date
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_menu
 * Fonctions clefs: menu_order_settings_page, custom_menu_order
 * APIs WP detectees: add_action, add_options_page, get_option
 * Signatures contenu: html-markup
 * Lignes / octets: 138 / 4925
 * Empreinte code (sha256): f14faf06682d641d24767e78ec1ca29a7e20b031377262d7f359dcabb8fd24d8
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: ACTIVE__admin__admin-menu-order-alpha-settings__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__admin__admin-menu-order-alpha-settings__v2__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: ACTIVE
 * Cluster principal: admin_ui_settings
 * Clusters secondaires: scheduler_posts
 * Domaine: admin
 * Confiance: medium
 * Scores (top): admin_ui_settings=8, scheduler_posts=8
 * Raisons principales: admin-ui, settings
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

/**
 * Plugin pour trier le menu admin WordPress avec options et gestion des extensions.
 */

// Ajouter une entrée dans le menu Réglages
add_action('admin_menu', function() {
    add_options_page(
        'Ordre du Menu', // Titre de la page
        'Ordre du Menu', // Titre du menu
        'manage_options', // Capacité requise
        'menu-order-settings', // Slug de la page
        'menu_order_settings_page' // Fonction de rappel
    );
});

// Affichage de la page de réglages
function menu_order_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['menu_order_option'])) {
        update_option('menu_order_option', sanitize_text_field($_POST['menu_order_option']));
        echo '<div class="updated"><p>Ordre du menu mis à jour.</p></div>';
    }

    $menu_order_option = get_option('menu_order_option', 'normal');

    ?>
    <div class="wrap">
        <h1>Réorganiser les Entrées du Menu</h1>
        <form method="post" action="">
            <h2>Options :</h2>
            <p>
                <label>
                    <input type="radio" name="menu_order_option" value="normal" <?php checked($menu_order_option, 'normal'); ?> />
                    Afficher le menu normalement
                </label>
            </p>
            <p>
                <label>
                    <input type="radio" name="menu_order_option" value="alphabetical" <?php checked($menu_order_option, 'alphabetical'); ?> />
                    Afficher le menu par ordre alphabétique
                </label>
            </p>
            <p>
                <label>
                    <input type="radio" name="menu_order_option" value="extensions" <?php checked($menu_order_option, 'extensions'); ?> />
                    Afficher les extensions triées après le menu WordPress par défaut
                </label>
            </p>
            <input type="submit" class="button button-primary" value="Enregistrer l'Ordre" />
        </form>
    </div>
    <?php
}

// Appliquer le tri du menu en fonction des réglages
add_action('admin_menu', 'custom_menu_order', 999);

function custom_menu_order() {
    global $menu;

    $menu_order_option = get_option('menu_order_option', 'normal');

    // Liste des entrées de base WordPress
    $core_menus = [
        'index.php',            // Tableau de bord
        'edit.php',             // Articles
        'upload.php',           // Médias
        'edit.php?post_type=page', // Pages
        'edit-comments.php',    // Commentaires
        'themes.php',           // Apparence
        'plugins.php',          // Extensions
        'users.php',            // Utilisateurs
        'tools.php',            // Outils
        'options-general.php',  // Réglages
    ];

    if ($menu_order_option === 'alphabetical') {
        // Tri alphabétique de tout le menu
        usort($menu, function($a, $b) {
            return strcmp($a[0], $b[0]);
        });
    }

    if ($menu_order_option === 'extensions') {
        // Séparer les entrées de base et les extensions
        $default_menu = [];
        $extensions_menu = [];

        foreach ($menu as $item) {
            if (!empty($item[2]) && in_array($item[2], $core_menus)) {
                $default_menu[] = $item; // Menu de base
            } else {
                $extensions_menu[] = $item; // Extensions
            }
        }

        // Trier les extensions par ordre alphabétique
        usort($extensions_menu, function($a, $b) {
            return strcmp($a[0], $b[0]);
        });

        // Ajouter un séparateur avant les extensions
        $default_menu[] = ['---', 'read', 'separator'];

        // Fusionner les deux parties
        $menu = array_merge($default_menu, $extensions_menu);
    }
}
