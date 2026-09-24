<?php
/*
 * Display name: MEDIA ▶️ VIDEO - Autoplay Loop Muted - v1
 * Scope: global
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Coche automatiquement "Lecture auto", "Boucle" et "Muet" pour toutes les vidéos.
 *
 * 1) Éditeur Gutenberg : dès l'insertion d'un bloc Vidéo, les 3 options sont
 *    pré-cochées par défaut (et les blocs Vidéo déjà présents les affichent
 *    cochées à la réouverture — enregistrement explicite à la sauvegarde).
 * 2) Front (filet de sécurité) : attributs posés en JS sur tous les <video>
 *    (blocs, widgets, shortcodes) et paramètres ajoutés aux iframes
 *    YouTube / Vimeo, même si la vidéo a été enregistrée sans ces options.
 *    Muet est indispensable : les navigateurs bloquent l'autoplay non muet.
 */

/* --- 1) Éditeur Gutenberg : valeurs par défaut du bloc Vidéo ------------- */
function clm_video_autoplay_defaults_editor() {
	$script = <<<'JS'
( function () {
	'use strict';

	if ( ! window.wp || ! wp.hooks || ! wp.blocks ) {
		return;
	}

	wp.hooks.addFilter(
		'blocks.registerBlockType',
		'clm/video-autoplay-defaults',
		function ( settings, name ) {
			if ( name !== 'core/video' || ! settings.attributes ) {
				return settings;
			}

			[ 'autoplay', 'loop', 'muted' ].forEach( function ( attr ) {
				if ( settings.attributes[ attr ] ) {
					settings.attributes[ attr ] = Object.assign(
						{},
						settings.attributes[ attr ],
						{ default: true }
					);
				}
			} );

			return settings;
		}
	);
} )();
JS;

	wp_add_inline_script( 'wp-blocks', $script, 'after' );
}
add_action( 'enqueue_block_editor_assets', 'clm_video_autoplay_defaults_editor' );

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
