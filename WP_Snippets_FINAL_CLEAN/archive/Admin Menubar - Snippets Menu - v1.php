
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets
 * Source path: WP_Snippets/WP_ADMIN - Snippets.php
 * Display name: WP_ADMIN - Snippets
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: WP_ADMIN - Snippets (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets/WP_ADMIN - Snippets.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: admin-bar
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_bar_menu
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 17 / 514
 * Hash code normalise (sha256): a2918e81e07eb87c3d67ae1dceb117a648295a98ea8f4144eafdf0bf58aef159
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: admin-snippets__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/admin-snippets__v001.php
 * Bucket FINAL: archive
 * Statut: LOCAL
 * Cluster principal: admin_menubar
 * Clusters secondaires: aucun
 * Domaine: admin
 * Confiance: medium
 * Scores (top): admin_menubar=6
 * Raisons principales: admin_bar_menu
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

ppets/WP_ADMIN - Snippets.php
 * Display name: WP_ADMIN - Snippets
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: WP_ADMIN - Snippets (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets/WP_ADMIN - Snippets.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: admin-bar
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_bar_menu
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 17 / 514
 * Hash code normalise (sha256): a2918e81e07eb87c3d67ae1dceb117a648295a98ea8f4144eafdf0bf58aef159
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/**
 * Ajoute un bouton avec une icône de ciseaux dans la barre d'administration
 * menant vers la page des snippets WPCode
 */

add_action('admin_bar_menu', function($admin_bar) {
    $admin_bar->add_node([
        'id'    => 'wpcode_snippets',
        'title' => '<span class="ab-icon dashicons dashicons-editor-code"></span>',
        'href'  => admin_url('admin.php?page=snippets&status=active'),
        'meta'  => [
            'title' => 'Accéder aux snippets WPCode',
        ],
    ]);
}, 100);
?>