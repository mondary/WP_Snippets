/* CLM-CREATED-AT: 2026-02-25 */
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/active/global/049__id-107__post-search-custom.php
 * Display name: POST - search custom
 * Scope: global
 * Online snippet: oui
 * Online active: oui
 * Online ID: 107
 * Online modified: 2025-05-05 09:35:52
 * Online revision: 35
 * Exact duplicate group: oui (9c88aa0b7d33…, 2 membres)
 * Canonical exact group ID: 104
 * Version family: DUP POST - search custom (1 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets_Online_Current/active/global/049__id-107__post-search-custom.php
 * Is family latest: oui
 * Canonical reasons: exact-group-canonical, protected-online-active
 * Features: search-ui, head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 39 / 1102
 * Hash code normalise (sha256): 9c88aa0b7d333748d125618983cf270535b779cfe3e2526ddb86844e23f412a3
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: ACTIVE__global__post-search-custom__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__post-search-custom__v2__src-wp_snippets_online_current.php
 * Resume fonctionnalites: interface de recherche, UI frontend (CSS/HTML), 1 hook(s) WP
 * Features detectees: search-ui, css-ui, footer-head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head
 * Fonctions clefs: aucun
 * APIs WP detectees: add_action
 * Signatures contenu: inline-style, html-markup
 * Lignes / octets: 53 / 1762
 * Empreinte code (sha256): fc29ab6cf45e59eeee573190bce70e2f82979a58820712a8cfa29908a19eebb1
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: ACTIVE__global__post-search-custom__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__post-search-custom__v2__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: ACTIVE
 * Cluster principal: search_ui
 * Clusters secondaires: post_footer_ui
 * Domaine: post-front
 * Confiance: high
 * Scores (top): search_ui=10, post_footer_ui=5, frontend_ui_widget=4
 * Raisons principales: search-ui, search
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

add_action('wp_head', function () {
    ?>
    <style>

    /* Nettoyage des couches internes */
    #search-drawer .drawer-inner-wrap,
    #search-drawer .search-form,
    #search-drawer .wp-block-search__inside-wrapper
	    {
        all: unset !important;
        display: flex !important;
        align-items: center;
        justify-content: flex-start;
        width: 100%;
    }    
    /* Champ de recherche géant, aligné à gauche */
			#search-drawer .search-field {
        all: unset !important;
        font-size: 15vw !important; /* Taille très grande */
        color: #ffffff !important;
        width: 100% !important;
        max-width: none !important;
    }
    </style>
    <?php
});
