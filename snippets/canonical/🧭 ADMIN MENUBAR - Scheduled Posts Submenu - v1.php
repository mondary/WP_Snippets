<?php
/*
 * Display name: 🧭 ADMIN MENUBAR - Scheduled Posts Submenu - v1.php
 * Scope: global
 */

<?php
/**
 * Ajoute un sous-menu « Articles planifiés » dans la colonne latérale gauche,
 * sous le menu Articles. Affiche un badge avec le nombre d'articles planifiés.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', function () {
	// Badge: nombre d'articles planifiés
	$scheduled = wp_count_posts( 'post' );
	$count     = isset( $scheduled->future ) ? (int) $scheduled->future : 0;

	$label = 'Articles planifiés';
	if ( $count > 0 ) {
		$label .= ' <span class="awaiting-mod count-' . $count . '">'
			. '<span class="pending-count" aria-hidden="true">' . number_format_i18n( $count ) . '</span>'
			. '</span>';
	}

	$hook = add_submenu_page(
		'edit.php',              // parent: menu Articles
		'Articles planifiés',    // titre de page
		$label,                  // libellé du menu (avec badge)
		'edit_posts',            // capacité requise
		'clm-scheduled-posts',   // slug unique
		'',                      // pas de callback de rendu (redirection via load-hook)
		6                        // position: après « Tous les articles »
	);

	// Redirection avant le rendu de la page → aucun flash visible
	if ( $hook ) {
		add_action( 'load-' . $hook, function () {
			wp_safe_redirect(
				admin_url( 'edit.php?post_status=future&post_type=post&orderby=date&order=asc' )
			);
			exit;
		} );
	}
} );
