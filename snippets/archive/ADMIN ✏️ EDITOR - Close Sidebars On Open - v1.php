<?php
/*
 * Display name: ADMIN ✏️ EDITOR - Close Sidebars On Open - v1
 * Scope: admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ferme les volets de l'éditeur Gutenberg à l'ouverture d'un article :
 * - le volet Réglages (barre latérale des paramètres)
 * - la vue liste (panneau gauche)
 * - l'inserteur de blocs (panneau gauche)
 * Chaque volet n'est fermé qu'une fois : l'utilisateur peut toujours
 * les rouvrir manuellement ensuite.
 */
function clm_editor_close_sidebars_script() {

	$script = <<<JS
( function () {
	'use strict';

	if ( ! window.wp || ! wp.data || ! wp.domReady ) {
		return;
	}

	wp.domReady( function () {
		var closed = {};

		function once( key, fn ) {
			if ( closed[ key ] ) {
				return;
			}
			closed[ key ] = true;
			try { fn(); } catch ( e ) {}
		}

		function closeAll() {
			var store;
			try { store = wp.data.dispatch( 'core/edit-post' ); } catch ( e ) { return; }
			if ( ! store ) {
				return;
			}

			// Volet Réglages (Paramètres).
			if ( typeof store.closeGeneralSidebar === 'function' ) {
				once( 'sidebar', function () { store.closeGeneralSidebar(); } );
			}

			// Vue liste (panneau gauche).
			if ( typeof store.closeListView === 'function' ) {
				once( 'listview', function () { store.closeListView(); } );
			} else if ( typeof store.setIsListViewOpened === 'function' ) {
				once( 'listview', function () { store.setIsListViewOpened( false ); } );
			}

			// Inserteur de blocs (panneau gauche).
			if ( typeof store.closeInserter === 'function' ) {
				once( 'inserter', function () { store.closeInserter(); } );
			} else if ( typeof store.setIsInserterOpened === 'function' ) {
				once( 'inserter', function () { store.setIsInserterOpened( false ); } );
			}
		}

		closeAll();

		// Rattrape les volets qui s'ouvrent après l'hydratation de l'éditeur,
		// puis arrête de surveiller (l'utilisateur garde la main).
		var unsub = wp.data.subscribe( closeAll );
		window.setTimeout( function () {
			if ( unsub ) {
				unsub();
				unsub = null;
			}
		}, 5000 );
	} );
} )();
JS;

	wp_add_inline_script( 'wp-edit-post', $script, 'after' );
}

function clm_editor_close_sidebars_enqueue( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	wp_enqueue_script( 'wp-edit-post' );
	clm_editor_close_sidebars_script();
}
add_action( 'admin_enqueue_scripts', 'clm_editor_close_sidebars_enqueue' );
