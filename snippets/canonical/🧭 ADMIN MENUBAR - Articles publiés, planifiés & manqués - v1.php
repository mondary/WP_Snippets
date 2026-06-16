<?php
/*
 * Display name: 🧭 ADMIN MENUBAR - Articles publiés, planifiés & manqués - v1
 * Scope: global
 */

/**
 * Ajoute quatre sous-menus sous Articles :
 * 1. Publiés    → lien direct vers la liste filtrée (statut publish)
 * 2. Planifiés  → lien direct vers la liste filtrée (statut future)
 * 3. Manqués    → page dédiée listant les articles dont la date
 *    de publication programmée est dépassée sans avoir été publiés
 * 4. Brouillons → lien direct vers la liste filtrée (statut draft)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function clm_get_missed_schedule_posts() {
	return get_posts( [
		'post_type'      => 'post',
		'post_status'    => 'future',
		'posts_per_page' => -1,
		'date_query'     => [
			[
				'column' => 'post_date_gmt',
				'before' => gmdate( 'Y-m-d H:i:s' ),
			],
		],
	] );
}

add_action( 'admin_menu', function () {
	global $submenu;

	$counts    = wp_count_posts( 'post' );
	$published = isset( $counts->publish ) ? (int) $counts->publish : 0;
	$future    = isset( $counts->future ) ? (int) $counts->future : 0;
	$draft     = isset( $counts->draft ) ? (int) $counts->draft : 0;
	$missed    = count( clm_get_missed_schedule_posts() );

	// 1 — Articles publiés
	$label_published = 'Publiés';
	if ( $published > 0 ) {
		$label_published .= ' <span class="awaiting-mod count-' . $published . '">'
			. '<span class="pending-count" aria-hidden="true">' . number_format_i18n( $published ) . '</span>'
			. '</span>';
	}
	$submenu['edit.php'][5] = [
		$label_published,
		'edit_posts',
		'edit.php?post_status=publish&post_type=post',
	];

	// 2 — Articles planifiés (tous les posts future)
	$label_future = 'Planifiés';
	if ( $future > 0 ) {
		$label_future .= ' <span class="awaiting-mod count-' . $future . '">'
			. '<span class="pending-count" aria-hidden="true">' . number_format_i18n( $future ) . '</span>'
			. '</span>';
	}
	$submenu['edit.php'][6] = [
		$label_future,
		'edit_posts',
		'edit.php?post_status=future&post_type=post&orderby=date&order=asc',
	];

	// 3 — Articles manqués (posts future dont la date est dépassée)
	$label_missed = 'Manqués';
	if ( $missed > 0 ) {
		$label_missed .= ' <span class="awaiting-mod count-' . $missed . '">'
			. '<span class="pending-count" aria-hidden="true">' . number_format_i18n( $missed ) . '</span>'
			. '</span>';
	}
	add_submenu_page(
		'edit.php',
		'Articles manqués',
		$label_missed,
		'edit_posts',
		'clm-missed-schedule',
		'clm_render_missed_schedule_page',
		7
	);

	// 4 — Brouillons
	$label_draft = 'Brouillons';
	if ( $draft > 0 ) {
		$label_draft .= ' <span class="awaiting-mod count-' . $draft . '">'
			. '<span class="pending-count" aria-hidden="true">' . number_format_i18n( $draft ) . '</span>'
			. '</span>';
	}
	$submenu['edit.php'][8] = [
		$label_draft,
		'edit_posts',
		'edit.php?post_status=draft&post_type=post',
	];
} );

function clm_render_missed_schedule_page() {
	$posts = clm_get_missed_schedule_posts();
	$count = count( $posts );

	echo '<div class="wrap">';
	echo '<h1>Articles manqués';
	if ( $count > 0 ) {
		echo ' <span class="title-count theme-count">' . number_format_i18n( $count ) . '</span>';
	}
	echo '</h1>';

	if ( 0 === $count ) {
		echo '<div class="notice notice-success"><p>Aucun article manqué.</p></div>';
		echo '</div>';
		return;
	}

	echo '<p class="description">'
		. 'Articles dont la date de publication programmée est dépassée sans avoir été publiés.'
		. '</p>';

	echo '<table class="wp-list-table widefat fixed striped posts">';
	echo '<thead><tr>';
	echo '<td class="manage-column column-cb check-column"><input type="checkbox" id="cb-select-all"></td>';
	echo '<th class="manage-column column-title column-primary sortable desc" scope="col">Titre</th>';
	echo '<th class="manage-column column-date sortable desc" scope="col" style="width:200px">Date planifiée</th>';
	echo '</tr></thead>';
	echo '<tbody>';

	foreach ( $posts as $post ) {
		$edit_url = get_edit_post_link( $post->ID, 'raw' );

		$preview_url = add_query_arg(
			'preview_id',
			$post->ID,
			wp_nonce_url( get_permalink( $post ), 'post_preview_' . $post->ID )
		);

		$trash_url = get_delete_post_link( $post->ID, '', true );

		echo '<tr>';
		echo '<th class="check-column"><input type="checkbox" name="post[]" value="' . $post->ID . '"></th>';

		echo '<td class="title column-title column-primary" data-colname="Titre">';
		echo '<strong><a class="row-title" href="' . esc_url( $edit_url ) . '">'
			. esc_html( get_the_title( $post ) ) . '</a></strong>';
		echo '<div class="row-actions">';
		echo '<span class="edit"><a href="' . esc_url( $edit_url ) . '">Modifier</a> | </span>';
		echo '<span class="view"><a href="' . esc_url( $preview_url ) . '" target="_blank">Aperçu</a> | </span>';
		echo '<span class="trash"><a class="submitdelete" href="' . esc_url( $trash_url ) . '">Supprimer</a></span>';
		echo '</div></td>';

		echo '<td class="date column-date" data-colname="Date planifiée">';
		echo '<span style="color:#d63638;font-weight:600">';
		echo esc_html( get_the_date( '', $post ) . ' à ' . get_the_time( '', $post ) );
		echo '</span>';
		echo '<br><small>' . esc_html( human_time_diff( get_post_time( 'U', true, $post ), time() ) . ' de retard' ) . '</small>';
		echo '</td>';

		echo '</tr>';
	}

	echo '</tbody></table>';
	echo '</div>';
}
