<?php
/*
 * Display name: MEDIA ▶️ VIDEO - Autoplay Loop Muted - v2
 * Scope: global
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Coche automatiquement "Lecture auto", "Boucle" et "Muet" pour toutes les vidéos.
 *
 * v2 : en éditeur, les 3 toggles Gutenberg sont réellement cochés (ce sont les
 * vrais attributs du bloc, sauvegardés dans l'article) dès qu'un bloc Vidéo
 * est sélectionné — nouveau bloc ou vidéo existante. Désactivable à la main
 * pendant la session d'édition (recochera à la prochaine ouverture).
 *
 * 1) Éditeur : watcher sur la sélection — un bloc core/video sélectionné
 *    sans les 3 options reçoit autoplay/loop/muted = true.
 * 2) Front (filet de sécurité) : attributs posés en JS sur tous les <video>
 *    et paramètres ajoutés aux iframes YouTube / Vimeo.
 *    Muet est indispensable : les navigateurs bloquent l'autoplay non muet.
 */

/* --- 1) Éditeur Gutenberg : coche les toggles des blocs Vidéo ------------- */
function clm_video_autoplay_defaults_editor() {
	$script = <<<'JS'
( function () {
	'use strict';

	if ( ! window.wp || ! wp.data ) {
		return;
	}

	var done = new Set();

	function fix( block ) {
		if ( ! block || block.name !== 'core/video' || done.has( block.clientId ) ) {
			return;
		}
		done.add( block.clientId );

		var a = block.attributes || {};
		if ( a.autoplay && a.loop && a.muted ) {
			return;
		}
		wp.data.dispatch( 'core/block-editor' ).updateBlockAttributes(
			block.clientId,
			{ autoplay: true, loop: true, muted: true }
		);
	}

	wp.data.subscribe( function () {
		var selector = wp.data.select( 'core/block-editor' );
		if ( ! selector || ! selector.getBlock ) {
			return;
		}
		var ids = typeof selector.getSelectedBlockClientIds === 'function'
			? selector.getSelectedBlockClientIds()
			: ( selector.getSelectedBlockClientId() ? [ selector.getSelectedBlockClientId() ] : [] );
		ids.forEach( function ( id ) {
			fix( selector.getBlock( id ) );
		} );
	} );
} )();
JS;

	wp_add_inline_script( 'wp-edit-post', $script, 'after' );
}

function clm_video_autoplay_defaults_enqueue( $hook ) {
	if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	wp_enqueue_script( 'wp-edit-post' );
	clm_video_autoplay_defaults_editor();
}
add_action( 'admin_enqueue_scripts', 'clm_video_autoplay_defaults_enqueue' );

/* --- 2) Front : force les attributs à l'affichage ------------------------- */
function clm_video_autoplay_defaults_front() {
	$script = <<<'JS'
( function () {
	'use strict';

	function fixVideos() {
		document.querySelectorAll( 'video' ).forEach( function ( v ) {
			v.autoplay = true;
			v.loop = true;
			v.muted = true;
			v.defaultMuted = true;
			v.playsInline = true;
			[ 'autoplay', 'loop', 'muted', 'playsinline' ].forEach( function ( a ) {
				v.setAttribute( a, '' );
			} );
			var p = v.play();
			if ( p && p.catch ) {
				p.catch( function () {} );
			}
		} );

		document.querySelectorAll( 'iframe[src]' ).forEach( function ( f ) {
			var src = f.src;
			if ( ! /youtube\.com|youtu\.be|vimeo\.com/i.test( src ) ) {
				return;
			}
			var u;
			try {
				u = new URL( src );
			} catch ( e ) {
				return;
			}

			if ( u.hostname.indexOf( 'vimeo' ) !== -1 ) {
				u.searchParams.set( 'autoplay', '1' );
				u.searchParams.set( 'loop', '1' );
				u.searchParams.set( 'muted', '1' );
			} else {
				// YouTube : loop=1 exige playlist=<id de la vidéo>.
				var m = u.pathname.match( /(?:embed\/|shorts\/|video\/)?([A-Za-z0-9_-]{11})/ );
				var id = u.searchParams.get( 'v' ) || ( m ? m[ 1 ] : '' );
				u.searchParams.set( 'autoplay', '1' );
				u.searchParams.set( 'mute', '1' );
				if ( id ) {
					u.searchParams.set( 'loop', '1' );
					u.searchParams.set( 'playlist', id );
				}
			}

			var next = u.toString();
			if ( next !== src ) {
				f.src = next;
			}
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', fixVideos );
	} else {
		fixVideos();
	}
	window.addEventListener( 'load', fixVideos );
} )();
JS;

	echo '<script>' . $script . '</script>';
}
add_action( 'wp_footer', 'clm_video_autoplay_defaults_front', 99 );
