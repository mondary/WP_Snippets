/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: WP_Snippets
 * Source path: WP_Snippets/WP_ADMIN - ReadButton.php
 * Display name: WP_ADMIN - ReadButton
 * Scope: admin
 * Online snippet: non
 * Exact duplicate group: oui (95418e787951…, 3 membres)
 * Canonical exact group ID: 78
 * Version family: DUP ADMIN - Readbutton (1 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets/WP_ADMIN - ReadButton.php
 * Is family latest: oui
 * Archive reasons: exact-duplicate
 * Features: admin-bar
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_bar_menu
 * Fonctions clefs: add_custom_admin_bar_button
 * Lignes / octets (brut): 16 / 2231
 * Hash code normalise (sha256): 95418e787951d06941d6fc0d7a7615a0ce03ebd208f990f20ea8e514344f5e74
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: admin-readbutton__v002.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/admin-readbutton__v002.php
 * Resume fonctionnalites: flux RSS, 1 hook(s) WP, 1 fonction(s) clef
 * Features detectees: rss, admin-menubar, svg-ui
 * Dependances probables: WordPress core hooks
 * Hooks WP: admin_bar_menu
 * Fonctions clefs: add_custom_admin_bar_button
 * Selecteurs / IDs: #3858E9
 * APIs WP detectees: add_custom_admin_bar_button, add_node, add_action
 * Signatures contenu: html-markup
 * Lignes / octets: 39 / 3064
 * Empreinte code (sha256): b17bca2beb9d0a99bc1afddc40e181738ecdfdd28ba24ff7def574570171f6f5
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: admin-readbutton__v002.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/admin-readbutton__v002.php
 * Bucket FINAL: archive
 * Statut: LOCAL
 * Cluster principal: admin_menubar
 * Clusters secondaires: aucun
 * Domaine: admin
 * Confiance: high
 * Scores (top): admin_menubar=18, rss_feed=6, frontend_ui_widget=2
 * Raisons principales: admin-menubar, menubar, admin_bar_menu
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

/**
 * Add a custom button to the WordPress admin bar.
 */
function add_custom_admin_bar_button() {
    global $wp_admin_bar;

    // Add the button to the admin bar.
    $wp_admin_bar->add_node( array(
        'id'    => 'custom-button',
        'title' => '<span style="display: inline-block; background-color: white; padding: 4px 8px 0 8px; margin-bottom: -8px; margin-right: 30px; border-radius: 5px 5px 0 0;"><svg width="24" height="11" viewBox="0 0 24 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="masterbar__menu-icon masterbar_svg-reader"><path d="M22.8746 4.60676L22.8197 4.3575C22.3347 2.17436 20.276 0.584279 17.9245 0.584279C16.6527 0.584279 15.4358 1.03122 14.5116 1.84775C14.1914 2.13139 13.9443 2.44081 13.743 2.74163C13.1849 2.63849 12.6085 2.56114 12.032 2.56114H12.0046C11.419 2.56114 10.8425 2.64709 10.2753 2.75023C10.0648 2.44081 9.82691 2.13139 9.49752 1.83915C8.57338 1.01403 7.35646 0.575684 6.08463 0.575684C3.72398 0.584279 1.66527 2.17436 1.18033 4.3575L1.12543 4.60676H0V6.00775H1.12543L1.18033 6.257C1.63782 8.44014 3.69653 10.0302 6.07548 10.0302C8.83873 10.0302 11.0804 7.91585 11.0804 5.31155C11.0804 5.31155 11.0896 4.72709 10.8517 3.97072C11.236 3.91915 11.6203 3.87618 12.0046 3.87618C12.3706 3.87618 12.7549 3.91056 13.1483 3.96213C12.9012 4.72709 12.9195 5.31155 12.9195 5.31155C12.9195 7.91585 15.1613 10.0302 17.9245 10.0302C20.3035 10.0302 22.3622 8.44874 22.8197 6.257L22.8746 6.00775H24V4.60676H22.8746ZM6.07548 8.62923C4.13572 8.62923 2.5528 7.14229 2.5528 5.30295C2.5528 3.46362 4.13572 1.97667 6.07548 1.97667C8.01524 1.97667 9.59816 3.46362 9.59816 5.30295C9.59816 7.14229 8.01524 8.62923 6.07548 8.62923ZM17.9245 8.62923C15.9847 8.62923 14.4018 7.14229 14.4018 5.30295C14.4018 3.46362 15.9847 1.97667 17.9245 1.97667C19.8643 1.97667 21.4472 3.46362 21.4472 5.30295C21.4472 7.14229 19.8643 8.62923 17.9245 8.62923Z" fill="#3858E9"></path></svg></span>', // SVG icon with specific color
        'href'  => 'https://wordpress.com/read/feeds/119173277', // Button URL
        'parent' => 'top-secondary', // Place in the secondary menu
        'meta'  => array( 'class' => 'custom-reader-button' )
    ) );
}
add_action( 'admin_bar_menu', 'add_custom_admin_bar_button', 90 );
