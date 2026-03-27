/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: WP_Snippets
 * Source path: WP_Snippets/WP_POST - search custom.php
 * Display name: WP_POST - search custom
 * Scope: front-end
 * Online snippet: non
 * Exact duplicate group: oui (9c88aa0b7d33…, 2 membres)
 * Canonical exact group ID: 104
 * Version family: DUP POST - search custom (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets/WP_POST - search custom.php
 * Is family latest: oui
 * Archive reasons: exact-duplicate
 * Features: search-ui, head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 26 / 708
 * Hash code normalise (sha256): 9c88aa0b7d333748d125618983cf270535b779cfe3e2526ddb86844e23f412a3
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: post-search-custom__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-search-custom__v001.php
 * Resume fonctionnalites: interface de recherche, UI frontend (CSS/HTML), 1 hook(s) WP
 * Features detectees: search-ui, css-ui, footer-head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head
 * Fonctions clefs: aucun
 * APIs WP detectees: add_action
 * Signatures contenu: inline-style, html-markup
 * Lignes / octets: 49 / 1540
 * Empreinte code (sha256): fd738cccb451bddee6f1ab1c3281140894a3c2ab0ba39a7fb0f7418ad55afd57
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: post-search-custom__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-search-custom__v001.php
 * Bucket FINAL: archive
 * Statut: LOCAL
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
