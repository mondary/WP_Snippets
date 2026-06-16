<?php
/*
 * Display name: 🧭 ADMIN MENUBAR - Afficher les articles planifiés avec badge - v1
 * Scope: global
 */

/**
 * Ajoute un sous-menu « Articles planifiés » dans la colonne latérale gauche,
 * sous le menu Articles. Affiche un badge avec le nombre d'articles planifiés.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', function () {
	global $submenu;

	$scheduled = wp_count_posts( 'post' );
	$count     = isset( $scheduled->future ) ? (int) $scheduled->future : 0;

	$label = 'Articles planifiés';
	if ( $count > 0 ) {
		$label .= ' <span class="awaiting-mod count-' . $count . '">'
			. '<span class="pending-count" aria-hidden="true">' . number_format_i18n( $count ) . '</span>'
			. '</span>';
	}

	$submenu['edit.php'][6] = [
		$label,
		'edit_posts',
		'edit.php?post_status=future&post_type=post&orderby=date&order=asc',
	];
} );
