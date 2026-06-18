<?php
/*
 * Display name: ADMIN 🔧 SETTINGS - Export Markdown RAG - v1
 * Scope: global
 */

<?php
/**
 * Export WordPress posts as one Markdown file per article (ZIP) for RAG ingestion.
 * UI: button on wp-admin/edit.php (Articles list).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'restrict_manage_posts', 'clm_wp_rag_export_render_button', 20 );
add_action( 'admin_post_clm_wp_rag_export_posts_markdown', 'clm_wp_rag_export_download' );

/**
 * Render export button on Posts list screen.
 */
function clm_wp_rag_export_render_button() {
	global $typenow;

	if ( 'post' !== $typenow || ! current_user_can( 'export' ) ) {
		return;
	}

	$nonce = wp_create_nonce( 'clm_wp_rag_export_posts_markdown' );
	$url   = admin_url( 'admin-post.php?action=clm_wp_rag_export_posts_markdown&_wpnonce=' . $nonce );

	echo '<a href="' . esc_url( $url ) . '" class="button button-secondary" style="margin-left:8px;">';
	echo esc_html__( 'Export Markdown (RAG)', 'default' );
	echo '</a>';
}

/**
 * Handle export download (ZIP with one .md file per post).
 */
function clm_wp_rag_export_download() {
	if ( ! current_user_can( 'export' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'default' ) );
	}

	check_admin_referer( 'clm_wp_rag_export_posts_markdown' );

	if ( ! class_exists( 'ZipArchive' ) ) {
		wp_die( esc_html__( 'ZipArchive is required on this server.', 'default' ) );
	}

	$zip_filename = 'wp-posts-rag-' . gmdate( 'Y-m-d' ) . '.zip';
	$tmp_zip      = wp_tempnam( $zip_filename );
	if ( ! $tmp_zip ) {
		wp_die( esc_html__( 'Cannot create temporary export file.', 'default' ) );
	}

	$zip = new ZipArchive();
	if ( true !== $zip->open( $tmp_zip, ZipArchive::OVERWRITE ) ) {
		wp_die( esc_html__( 'Cannot open ZIP archive for writing.', 'default' ) );
	}

	$index_lines   = array();
	$index_lines[] = '# WordPress Posts Export (RAG)';
	$index_lines[] = '';
	$index_lines[] = '- site: ' . home_url();
	$index_lines[] = '- exported_at_utc: ' . gmdate( 'c' );
	$index_lines[] = '';
	$index_lines[] = '## Files';
	$index_lines[] = '';

	$paged = 1;

	do {
		$query = new WP_Query(
			array(
				'post_type'           => 'post',
				'post_status'         => array( 'publish', 'future', 'draft', 'pending', 'private' ),
				'posts_per_page'      => 100,
				'paged'               => $paged,
				'orderby'             => 'date',
				'order'               => 'DESC',
				'ignore_sticky_posts' => true,
				'no_found_rows'       => false,
			)
		);

		if ( ! $query->have_posts() ) {
			break;
		}

		foreach ( $query->posts as $post ) {
			$file_name = clm_wp_rag_export_build_post_filename( $post );
			$content   = clm_wp_rag_export_build_markdown_for_post( $post );

			$zip->addFromString( $file_name, $content );
			$index_lines[] = '- `' . $file_name . '`';
		}

		$paged++;
		wp_reset_postdata();
	} while ( $paged <= (int) $query->max_num_pages );

	$zip->addFromString( 'INDEX.md', implode( "\n", $index_lines ) . "\n" );
	$zip->close();

	header( 'Content-Description: File Transfer' );
	header( 'Content-Type: application/zip' );
	header( 'Content-Disposition: attachment; filename=' . $zip_filename );
	header( 'Content-Length: ' . filesize( $tmp_zip ) );
	header( 'Pragma: public' );
	header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
	header( 'Expires: 0' );

	readfile( $tmp_zip );
	@unlink( $tmp_zip );

	exit;
}

/**
 * Build deterministic filename for a post markdown file.
 *
 * @param WP_Post $post Post object.
 */
function clm_wp_rag_export_build_post_filename( $post ) {
	$post_id = (int) $post->ID;
	$date    = get_post_time( 'Y-m-d', true, $post_id );
	$slug    = sanitize_title( $post->post_name ? $post->post_name : get_the_title( $post_id ) );

	if ( '' === $slug ) {
		$slug = 'post-' . $post_id;
	}

	return $date . '__' . $slug . '__id-' . $post_id . '.md';
}

/**
 * Build one post block with YAML front matter + markdown body.
 *
 * @param WP_Post $post Post object.
 */
function clm_wp_rag_export_build_markdown_for_post( $post ) {
	$post_id     = (int) $post->ID;
	$title       = get_the_title( $post_id );
	$slug        = $post->post_name;
	$status      = $post->post_status;
	$post_type   = $post->post_type;
	$url         = get_permalink( $post_id );
	$author_id   = (int) $post->post_author;
	$author_name = get_the_author_meta( 'display_name', $author_id );
	$date_gmt    = get_post_time( 'c', true, $post_id );
	$mod_gmt     = get_post_modified_time( 'c', true, $post_id );
	$excerpt     = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : wp_trim_words( wp_strip_all_tags( $post->post_content ), 55 );

	$categories = wp_get_post_categories( $post_id, array( 'fields' => 'names' ) );
	$tags       = wp_get_post_tags( $post_id, array( 'fields' => 'names' ) );
	$keywords   = array_values( array_unique( array_filter( array_merge( $tags, $categories ) ) ) );

	$featured_image = get_the_post_thumbnail_url( $post_id, 'full' );
	if ( ! $featured_image ) {
		$featured_image = '';
	}

	$content_markdown = clm_wp_rag_export_html_to_markdown( $post->post_content );

	$front_matter  = "---\n";
	$front_matter .= 'id: ' . $post_id . "\n";
	$front_matter .= 'title: "' . clm_wp_rag_export_escape_yaml( $title ) . '"' . "\n";
	$front_matter .= 'slug: "' . clm_wp_rag_export_escape_yaml( $slug ) . '"' . "\n";
	$front_matter .= 'post_type: "' . clm_wp_rag_export_escape_yaml( $post_type ) . '"' . "\n";
	$front_matter .= 'status: "' . clm_wp_rag_export_escape_yaml( $status ) . '"' . "\n";
	$front_matter .= 'date_gmt: "' . clm_wp_rag_export_escape_yaml( $date_gmt ) . '"' . "\n";
	$front_matter .= 'modified_gmt: "' . clm_wp_rag_export_escape_yaml( $mod_gmt ) . '"' . "\n";
	$front_matter .= 'author: "' . clm_wp_rag_export_escape_yaml( $author_name ) . '"' . "\n";
	$front_matter .= 'author_id: ' . $author_id . "\n";
	$front_matter .= 'url: "' . clm_wp_rag_export_escape_yaml( $url ) . '"' . "\n";
	$front_matter .= 'featured_image: "' . clm_wp_rag_export_escape_yaml( $featured_image ) . '"' . "\n";
	$front_matter .= 'categories: [' . clm_wp_rag_export_yaml_list_inline( $categories ) . "]\n";
	$front_matter .= 'tags: [' . clm_wp_rag_export_yaml_list_inline( $tags ) . "]\n";
	$front_matter .= 'keywords: [' . clm_wp_rag_export_yaml_list_inline( $keywords ) . "]\n";
	$front_matter .= 'excerpt: "' . clm_wp_rag_export_escape_yaml( $excerpt ) . '"' . "\n";
	$front_matter .= "---\n\n";

	return $front_matter . '# ' . $title . "\n\n" . trim( $content_markdown ) . "\n";
}

/**
 * Very lightweight HTML -> markdown converter for article content.
 */
function clm_wp_rag_export_html_to_markdown( $html ) {
	$replacements = array(
		'/<h1[^>]*>(.*?)<\/h1>/is'         => "\n# $1\n",
		'/<h2[^>]*>(.*?)<\/h2>/is'         => "\n## $1\n",
		'/<h3[^>]*>(.*?)<\/h3>/is'         => "\n### $1\n",
		'/<h4[^>]*>(.*?)<\/h4>/is'         => "\n#### $1\n",
		'/<h5[^>]*>(.*?)<\/h5>/is'         => "\n##### $1\n",
		'/<h6[^>]*>(.*?)<\/h6>/is'         => "\n###### $1\n",
		'/<strong[^>]*>(.*?)<\/strong>/is' => '**$1**',
		'/<b[^>]*>(.*?)<\/b>/is'           => '**$1**',
		'/<em[^>]*>(.*?)<\/em>/is'         => '*$1*',
		'/<i[^>]*>(.*?)<\/i>/is'           => '*$1*',
		'/<code[^>]*>(.*?)<\/code>/is'     => '`$1`',
		'/<blockquote[^>]*>(.*?)<\/blockquote>/is' => "\n> $1\n",
		'/<br\s*\/?>/i'                    => "\n",
		'/<\/p>/i'                          => "\n\n",
		'/<p[^>]*>/i'                       => '',
		'/<li[^>]*>(.*?)<\/li>/is'          => "- $1\n",
		'/<\/ul>/i'                         => "\n",
		'/<\/ol>/i'                         => "\n",
	);

	$html = preg_replace_callback(
		'/<a\s[^>]*href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/is',
		static function ( $matches ) {
			$url  = trim( html_entity_decode( $matches[1], ENT_QUOTES, 'UTF-8' ) );
			$text = trim( wp_strip_all_tags( $matches[2] ) );

			if ( '' === $text ) {
				$text = $url;
			}

			return '[' . $text . '](' . $url . ')';
		},
		$html
	);

	foreach ( $replacements as $pattern => $replacement ) {
		$html = preg_replace( $pattern, $replacement, $html );
	}

	$html = preg_replace( '/<[^>]+>/', '', $html );
	$html = html_entity_decode( $html, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	$html = preg_replace( "/\n{3,}/", "\n\n", $html );

	return trim( $html );
}

/**
 * Escape YAML scalar double-quoted string.
 */
function clm_wp_rag_export_escape_yaml( $value ) {
	$value = (string) $value;
	$value = str_replace( array( "\\", '"', "\r", "\n" ), array( "\\\\", '\\"', ' ', ' ' ), $value );

	return trim( $value );
}

/**
 * Convert array values to inline YAML list.
 */
function clm_wp_rag_export_yaml_list_inline( $items ) {
	if ( empty( $items ) || ! is_array( $items ) ) {
		return '';
	}

	$escaped = array();
	foreach ( $items as $item ) {
		$escaped[] = '"' . clm_wp_rag_export_escape_yaml( (string) $item ) . '"';
	}

	return implode( ', ', $escaped );
}

