/* CLM-CREATED-AT: 2026-02-25 */
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/active/admin/002__id-6__admin-list-duplicate-post-page.php
 * Display name: ADMIN - List Duplicate Post/Page
 * Scope: admin
 * Online snippet: oui
 * Online active: oui
 * Online ID: 6
 * Online modified: 2025-02-10 17:39:19
 * Online revision: 5
 * Exact duplicate group: oui (c6b485d1fb4c…, 3 membres)
 * Canonical exact group ID: 75
 * Version family: DUP ADMIN - List Duplicate Post/Page (1 variantes)
 * Version: v3
 * Recommended latest in family: WP_Snippets_Online_Current/active/admin/002__id-6__admin-list-duplicate-post-page.php
 * Is family latest: oui
 * Canonical reasons: exact-group-canonical, protected-online-active
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: post_row_actions, page_row_actions, admin_action_wpcode_snippet_duplicate_post
 * Fonctions clefs: wpcode_snippet_duplicate_post_link
 * Lignes / octets (brut): 131 / 4187
 * Hash code normalise (sha256): c6b485d1fb4c6893208ff7ede5f555193aa6862a9100004ee51779f534cdd170
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: ACTIVE__admin__admin-list-duplicate-post-page__v3__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__admin__admin-list-duplicate-post-page__v3__src-wp_snippets_online_current.php
 * Resume fonctionnalites: 3 hook(s) WP, 1 fonction(s) clef
 * Features detectees: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: post_row_actions, page_row_actions, admin_action_wpcode_snippet_duplicate_post
 * Fonctions clefs: wpcode_snippet_duplicate_post_link
 * APIs WP detectees: add_filter, get_post_type_object, wp_nonce_url, add_query_arg, add_action, wp_die, wp_verify_nonce, get_post, wp_get_current_user, wp_insert_post, get_object_taxonomies, get_post_type, wp_get_object_terms, wp_set_object_terms, get_post_meta … (+3)
 * Signatures contenu: html-markup
 * Lignes / octets: 144 / 4857
 * Empreinte code (sha256): 09bc73132e0ce12e0eeb62b327e656eafc8cb30382a55243c635c0c7abc36948
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: ACTIVE__admin__admin-list-duplicate-post-page__v3__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__admin__admin-list-duplicate-post-page__v3__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: ACTIVE
 * Cluster principal: misc_utilities
 * Clusters secondaires: aucun
 * Domaine: post-front
 * Confiance: low
 * Scores (top): misc_utilities=1
 * Raisons principales: fallback
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

// Add duplicate button to post/page list of actions.
add_filter( 'post_row_actions', 'wpcode_snippet_duplicate_post_link', 10, 2 );
add_filter( 'page_row_actions', 'wpcode_snippet_duplicate_post_link', 10, 2 );

// Let's make sure the function doesn't already exist.
if ( ! function_exists( 'wpcode_snippet_duplicate_post_link' ) ) {
	/**
	 * @param array   $actions The actions added as links to the admin.
	 * @param WP_Post $post The post object.
	 *
	 * @return array
	 */
	function wpcode_snippet_duplicate_post_link( $actions, $post ) {

		// Don't add action if the current user can't create posts of this post type.
		$post_type_object = get_post_type_object( $post->post_type );

		if ( null === $post_type_object || ! current_user_can( $post_type_object->cap->create_posts ) ) {
			return $actions;
		}


		$url = wp_nonce_url(
			add_query_arg(
				array(
					'action'  => 'wpcode_snippet_duplicate_post',
					'post_id' => $post->ID,
				),
				'admin.php'
			),
			'wpcode_duplicate_post_' . $post->ID,
			'wpcode_duplicate_nonce'
		);

		$actions['wpcode_duplicate'] = '<a href="' . $url . '" title="Duplicate item" rel="permalink">Duplicate</a>';

		return $actions;
	}
}

/**
 * Handle the custom action when clicking the button we added above.
 */
add_action( 'admin_action_wpcode_snippet_duplicate_post', function () {

	if ( empty( $_GET['post_id'] ) ) {
		wp_die( 'No post id set for the duplicate action.' );
	}

	$post_id = absint( $_GET['post_id'] );

	// Check the nonce specific to the post we are duplicating.
	if ( ! isset( $_GET['wpcode_duplicate_nonce'] ) || ! wp_verify_nonce( $_GET['wpcode_duplicate_nonce'], 'wpcode_duplicate_post_' . $post_id ) ) {
		// Display a message if the nonce is invalid, may it expired.
		wp_die( 'The link you followed has expired, please try again.' );
	}

	// Load the post we want to duplicate.
	$post = get_post( $post_id );

	// Create a new post data array from the post loaded.
	if ( $post ) {
		$current_user = wp_get_current_user();
		$new_post     = array(
			'comment_status' => $post->comment_status,
			'menu_order'     => $post->menu_order,
			'ping_status'    => $post->ping_status,
			'post_author'    => $current_user->ID,
			'post_content'   => $post->post_content,
			'post_excerpt'   => $post->post_excerpt,
			'post_name'      => $post->post_name,
			'post_parent'    => $post->post_parent,
			'post_password'  => $post->post_password,
			'post_status'    => 'draft',
			'post_title'     => $post->post_title . ' (copy)',// Add "(copy)" to the title.
			'post_type'      => $post->post_type,
			'to_ping'        => $post->to_ping,
		);
		// Create the new post
		$duplicate_id = wp_insert_post( $new_post );
		// Copy the taxonomy terms.
		$taxonomies = get_object_taxonomies( get_post_type( $post ) );
		if ( $taxonomies ) {
			foreach ( $taxonomies as $taxonomy ) {
				$post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
				wp_set_object_terms( $duplicate_id, $post_terms, $taxonomy );
			}
		}
		// Copy all the custom fields.
		$post_meta = get_post_meta( $post_id );
		if ( $post_meta ) {

			foreach ( $post_meta as $meta_key => $meta_values ) {
				if ( '_wp_old_slug' === $meta_key ) { // skip old slug.
					continue;
				}
				foreach ( $meta_values as $meta_value ) {
					add_post_meta( $duplicate_id, $meta_key, maybe_unserialize( $meta_value ) );
				}
			}
		}

		// Redirect to edit the new post.
		wp_safe_redirect(
			add_query_arg(
				array(
					'action' => 'edit',
					'post'   => $duplicate_id
				),
				admin_url( 'post.php' )
			)
		);
		exit;
	} else {
		wp_die( 'Error loading post for duplication, please try again.' );
	}
} );
