<?php
/*
 * Display name: MEDIA 🖼️ IMAGES - Gutenberg Auto Filename - v1
 * Scope: global
 */

/**
 * Plugin Name: Gutenberg Auto Image Filename
 * Description: Renomme les images collees dans Gutenberg a partir du titre de l'article.
 * Version: 1.0.0
 *
 * Exemple : titre "Google Chrome" + image.png => google-chrome.png.
 * En cas de doublon, WordPress ajoute automatiquement -1, -2, etc.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'clm_gutenberg_image_filename_v1_enqueue' ) ) {
	/**
	 * Transmet le titre actuellement saisi a la requete REST d'upload Gutenberg.
	 */
	function clm_gutenberg_image_filename_v1_enqueue() {
		if ( ! current_user_can( 'upload_files' ) ) {
			return;
		}

		$script = <<<'JS'
( function ( wp ) {
	'use strict';

	if ( ! wp.apiFetch || ! wp.data || typeof window.FormData === 'undefined' ) {
		return;
	}

	function isGenericClipboardName( filename ) {
		var basename = String( filename || '' )
			.toLowerCase()
			.replace( /\.[^.]+$/, '' )
			.replace( /[\s_()]+/g, '-' )
			.replace( /-+/g, '-' )
			.replace( /^-|-$/g, '' );

		return /^(?:image|pasted-image|clipboard-image|blob)(?:-\d+)?$/.test( basename );
	}

	wp.apiFetch.use( function ( options, next ) {
		try {
			var path = String( options.path || options.url || '' );
			var body = options.body;

			if (
				/(?:^|\/)wp\/v2\/media(?:[/?]|$)/.test( path ) &&
				body instanceof window.FormData
			) {
				var file = body.get( 'file' );
				var editor = wp.data.select( 'core/editor' );
				var title = editor && editor.getEditedPostAttribute( 'title' );

				if ( file && isGenericClipboardName( file.name ) && typeof title === 'string' && title.trim() ) {
					body.set( 'clm_gutenberg_image_title', title.trim() );
				}
			}
		} catch ( error ) {
			// Ne jamais bloquer un upload si Gutenberg ou un plugin change son API.
		}

		return next( options );
	} );
} )( window.wp );
JS;

		wp_add_inline_script( 'wp-api-fetch', $script, 'after' );
	}
	add_action( 'enqueue_block_editor_assets', 'clm_gutenberg_image_filename_v1_enqueue' );

	/**
	 * Indique si le nom vient probablement d'un collage dans l'editeur.
	 *
	 * @param string $filename Nom original du fichier.
	 * @return bool
	 */
	function clm_gutenberg_image_filename_v1_is_generic( $filename ) {
		$basename = strtolower( pathinfo( sanitize_file_name( $filename ), PATHINFO_FILENAME ) );
		$basename = preg_replace( '/[\s_()]+/', '-', $basename );
		$basename = preg_replace( '/-+/', '-', $basename );
		$basename = trim( $basename, '-' );

		return 1 === preg_match( '/^(?:image|pasted-image|clipboard-image|blob)(?:-\d+)?$/', $basename );
	}

	/**
	 * Remplace le nom generique juste avant l'enregistrement du fichier.
	 *
	 * @param array $file Donnees du fichier recu par WordPress.
	 * @return array
	 */
	function clm_gutenberg_image_filename_v1_rename( $file ) {
		if (
			! current_user_can( 'upload_files' ) ||
			empty( $file['name'] ) ||
			empty( $file['type'] ) ||
			0 !== strpos( strtolower( (string) $file['type'] ), 'image/' ) ||
			! clm_gutenberg_image_filename_v1_is_generic( $file['name'] )
		) {
			return $file;
		}

		$title = '';

		if ( isset( $_POST['clm_gutenberg_image_title'] ) && is_scalar( $_POST['clm_gutenberg_image_title'] ) ) {
			$title = sanitize_text_field( wp_unslash( $_POST['clm_gutenberg_image_title'] ) );
		}

		// Repli utile si le titre courant est deja enregistre par Gutenberg.
		if ( '' === $title && isset( $_REQUEST['post'] ) && is_scalar( $_REQUEST['post'] ) ) {
			$post_id = absint( wp_unslash( $_REQUEST['post'] ) );
			$title   = $post_id ? get_the_title( $post_id ) : '';
		}

		$slug = sanitize_title( $title );
		$slug = trim( substr( $slug, 0, 120 ), '-' );

		if ( '' === $slug ) {
			return $file;
		}

		$extension = strtolower( pathinfo( sanitize_file_name( $file['name'] ), PATHINFO_EXTENSION ) );
		if ( '' === $extension ) {
			return $file;
		}

		$file['name'] = $slug . '.' . $extension;

		return $file;
	}
	add_filter( 'wp_handle_upload_prefilter', 'clm_gutenberg_image_filename_v1_rename' );
	add_filter( 'wp_handle_sideload_prefilter', 'clm_gutenberg_image_filename_v1_rename' );
}
