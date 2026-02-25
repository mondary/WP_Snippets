
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: archives
 * Source path: archives/WP_ADMIN - Menu order manuel.php
 * Display name: WP_ADMIN - Menu order manuel
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: WP_ADMIN - Menu order manuel (1 variantes)
 * Version: v1
 * Recommended latest in family: archives/WP_ADMIN - Menu order manuel.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: custom_menu_order, menu_order
 * Fonctions clefs: custom_menu_order
 * Lignes / octets (brut): 26 / 810
 * Hash code normalise (sha256): 293062c0e903eda4b006b4444dd4f5327db52fe04cb6f62523205b4bdf5917ae
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: LOCAL__admin__wp-admin-menu-order-manuel__v1__src-archives.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/LOCAL__admin__wp-admin-menu-order-manuel__v1__src-archives.php
 * Bucket FINAL: canonical
 * Statut: LOCAL
 * Cluster principal: misc_utilities
 * Clusters secondaires: aucun
 * Domaine: admin
 * Confiance: low
 * Scores (top): misc_utilities=1
 * Raisons principales: fallback
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

rchives
 * Source path: archives/WP_ADMIN - Menu order manuel.php
 * Display name: WP_ADMIN - Menu order manuel
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: WP_ADMIN - Menu order manuel (1 variantes)
 * Version: v1
 * Recommended latest in family: archives/WP_ADMIN - Menu order manuel.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: custom_menu_order, menu_order
 * Fonctions clefs: custom_menu_order
 * Lignes / octets (brut): 26 / 810
 * Hash code normalise (sha256): 293062c0e903eda4b006b4444dd4f5327db52fe04cb6f62523205b4bdf5917ae
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/**
 * Script pour réorganiser les entrées du menu d'administration WordPress.
 */

add_filter('custom_menu_order', '__return_true');
add_filter('menu_order', 'custom_menu_order');

function custom_menu_order($menu_ord) {
    // Définissez l'ordre souhaité des éléments de menu ici
    $new_menu_order = array(
        'index.php',          // Tableau de bord
        'edit.php',           // Tous les articles
        'upload.php',         // Média
        'edit.php?post_type=page', // Pages
        'edit-comments.php',  // Commentaires
        'themes.php',         // Apparence
        'plugins.php',        // Extensions
        'users.php',          // Utilisateurs
        'tools.php',          // Outils
        'options-general.php' // Réglages
    );

    return $new_menu_order;
}
?>
