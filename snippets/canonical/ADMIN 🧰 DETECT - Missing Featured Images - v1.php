<?php
/*
 * Display name: ADMIN 🧰 DETECT - Missing Featured Images - v1
 * Scope: global
 *
 * v1:
 * - Filtre dans la liste des articles (Avec / Sans image mise en avant)
 * - Sous-menu "Sans image" avec compteur sous Articles
 * - Page dédiée listant les articles publiés sans image mise en avant
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * COMPTE LES ARTICLES PUBLIÉS SANS IMAGE MISE EN AVANT
 */
function clm_count_posts_missing_thumbnail(): int {
	$query = new WP_Query( [
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'meta_query'     => [
			[
				'key'     => '_thumbnail_id',
				'compare' => 'NOT EXISTS',
			],
		],
		'no_found_rows'  => true,
	] );
	return $query->post_count;
}

/**
 * SOUS-MENU "Sans image" SOUS ARTICLES
 */
add_action( 'admin_menu', function () {
	$count = clm_count_posts_missing_thumbnail();

	$badge_style = $count > 0 ? '#d63638' : '#00a32a';
	$label       = 'Sans image'
		. ' <span class="awaiting-mod" style="background:' . $badge_style . '!important">'
		. '<span class="pending-count" aria-hidden="true">' . number_format_i18n( $count ) . '</span>'
		. '</span>';

	add_submenu_page(
		'edit.php',
		'Articles publiés sans image mise en avant',
		$label,
		'edit_posts',
		'clm-missing-thumbnails',
		'clm_render_missing_thumbnails_page'
	);
} );

/**
 * PAGE DÉDIÉE : ARTICLES PUBLIÉS SANS IMAGE MISE EN AVANT
 */
function clm_render_missing_thumbnails_page() {
	$posts = get_posts( [
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'meta_query'     => [
			[
				'key'     => '_thumbnail_id',
				'compare' => 'NOT EXISTS',
			],
		],
	] );

	$count = count( $posts );

	echo '<div class="wrap">';
	echo '<h1>Articles publiés sans image mise en avant';
	if ( $count > 0 ) {
		echo ' <span class="title-count theme-count">' . number_format_i18n( $count ) . '</span>';
	}
	echo '</h1>';

	if ( 0 === $count ) {
		echo '<div class="notice notice-success"><p>Tous les articles publiés ont une image mise en avant.</p></div>';
		echo '</div>';
		return;
	}

	echo '<p class="description">Ces articles publiés n\'ont pas d\'image mise en avant. '
		. 'Ajoutez-en une pour améliorer le référencement et l\'affichage.</p>';

	echo '<table class="wp-list-table widefat fixed striped posts">';
	echo '<thead><tr>';
	echo '<th class="manage-column column-title column-primary" scope="col">Titre</th>';
	echo '<th class="manage-column column-date" scope="col" style="width:180px">Date de publication</th>';
	echo '<th class="manage-column" scope="col" style="width:160px">Actions</th>';
	echo '</tr></thead>';
	echo '<tbody>';

	foreach ( $posts as $post ) {
		$edit_url = get_edit_post_link( $post->ID, 'raw' );
		$view_url = get_permalink( $post->ID );

		echo '<tr>';
		echo '<td class="title column-title column-primary" data-colname="Titre">';
		echo '<strong><a class="row-title" href="' . esc_url( $edit_url ) . '">'
			. esc_html( get_the_title( $post ) ) . '</a></strong>';
		echo '</td>';
		echo '<td class="date column-date" data-colname="Date">'
			. esc_html( get_the_date( '', $post ) ) . '</td>';
		echo '<td data-colname="Actions">';
		echo '<a class="button button-small" href="' . esc_url( $edit_url ) . '">Modifier</a> ';
		echo '<a class="button button-small" href="' . esc_url( $view_url ) . '" target="_blank">Voir</a>';
		echo '</td>';
		echo '</tr>';
	}

	echo '</tbody></table>';
	echo '</div>';
}

/**
 * FILTRE DANS LA LISTE ARTICLES (edit.php)
 */
add_action( 'restrict_manage_posts', function ( $post_type ) {
	if ( 'post' !== $post_type ) {
		return;
	}

	$current = isset( $_GET['clm_thumbnail_filter'] ) ? $_GET['clm_thumbnail_filter'] : '';
	?>
	<select name="clm_thumbnail_filter">
		<option value="">Tous les articles</option>
		<option value="missing" <?php selected( $current, 'missing' ); ?>>Sans image mise en avant</option>
		<option value="has" <?php selected( $current, 'has' ); ?>>Avec image mise en avant</option>
	</select>
	<?php
} );

/**
 * APPLIQUE LE FILTRE À LA REQUÊTE
 */
add_action( 'pre_get_posts', function ( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}

	if ( 'post' !== $query->get( 'post_type' ) ) {
		return;
	}

	$filter = isset( $_GET['clm_thumbnail_filter'] ) ? $_GET['clm_thumbnail_filter'] : '';

	if ( 'missing' === $filter ) {
		$query->set( 'meta_query', [
			[
				'key'     => '_thumbnail_id',
				'compare' => 'NOT EXISTS',
			],
		] );
	} elseif ( 'has' === $filter ) {
		$query->set( 'meta_query', [
			[
				'key'     => '_thumbnail_id',
				'compare' => 'EXISTS',
			],
		] );
	}
} );
