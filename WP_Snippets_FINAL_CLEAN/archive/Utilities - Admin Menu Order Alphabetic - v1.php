
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: archives
 * Source path: archives/WP_ADMIN - Menu order alphabetic.php
 * Display name: WP_ADMIN - Menu order alphabetic
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: WP_ADMIN - Menu order alphabetic (1 variantes)
 * Version: v1
 * Recommended latest in family: archives/WP_ADMIN - Menu order alphabetic.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_menu
 * Fonctions clefs: trier_menu_admin
 * Lignes / octets (brut): 21 / 562
 * Hash code normalise (sha256): 919b74b319eb01c1fc509af59c41767b9e803c32416ce6d2da8b7e55fa857630
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: LOCAL__admin__wp-admin-menu-order-alphabetic__v1__src-archives.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/LOCAL__admin__wp-admin-menu-order-alphabetic__v1__src-archives.php
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

l: canonical
 * Source root: archives
 * Source path: archives/WP_ADMIN - Menu order alphabetic.php
 * Display name: WP_ADMIN - Menu order alphabetic
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: WP_ADMIN - Menu order alphabetic (1 variantes)
 * Version: v1
 * Recommended latest in family: archives/WP_ADMIN - Menu order alphabetic.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_menu
 * Fonctions clefs: trier_menu_admin
 * Lignes / octets (brut): 21 / 562
 * Hash code normalise (sha256): 919b74b319eb01c1fc509af59c41767b9e803c32416ce6d2da8b7e55fa857630
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/**
 * Plugin Name: Trier le menu admin WordPress par ordre alphabétique
 * Description: Ce script trie les entrées du menu admin WordPress par ordre alphabétique.
 * Author: Clément
 * Version: 1.0
 */

add_action('admin_menu', 'trier_menu_admin', 999);

function trier_menu_admin() {
    global $menu;

    // Vérifie si le menu existe
    if (is_array($menu)) {
        // Trie le menu par ordre alphabétique en utilisant le titre des éléments
        usort($menu, function($a, $b) {
            return strcmp($a[0], $b[0]);
        });
    }
}
