<?php
/*
 * Display name: ADMIN 🧰 DETECT - Duplicates Posts - v2
 * Scope: global
 *
 * v2:
 * - Défauts revus après premier usage : méthode « Titres seulement », seuil 70 %, période 12 mois
 * - Seuil réglable de 70 % à 100 % par paliers de 5
 * - Miniature de l'image mise en avant devant chaque titre
 * - Clic sur un titre = ouvrir l'article dans un nouvel onglet (comparaison face à face)
 *
 * v1:
 * - Page « Doublons » sous le menu Articles (admin.php?page=clm-duplicates)
 * - Détection par similarité de titre (tokens normalisés FR : accents, pluriels et mots vides ignorés)
 *   et/ou URLs externes communes dans le contenu des articles
 * - Groupes (paires, triplons…) avec écart de dates, actions Modifier / Voir / Corbeille
 * - Filtres : méthode (titres / URLs / les deux), seuil, période, statuts
 * - Aucune suppression automatique : l'outil liste, vous décidez
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MOTS VIDES POUR LA NORMALISATION DES TITRES
 */
function clm_dup_stopwords(): array {
	return [
		'le', 'la', 'les', 'un', 'une', 'des', 'de', 'du', 'au', 'aux', 'et', 'ou', 'en',
		'dans', 'sur', 'pour', 'par', 'avec', 'sans', 'sous', 'chez', 'que', 'qui', 'quoi',
		'dont', 'est', 'sont', 'ce', 'cet', 'cette', 'ces', 'son', 'sa', 'ses', 'mon', 'ma',
		'mes', 'ton', 'ta', 'tes', 'notre', 'nos', 'votre', 'vos', 'leur', 'leurs', 'il',
		'elle', 'ils', 'elles', 'on', 'nous', 'vous', 'se', 'y', 'a',
		'the', 'an', 'and', 'of', 'to', 'in', 'on', 'for', 'with', 'is', 'my',
		'comment', 'pourquoi', 'quand',
	];
}

/**
 * NORMALISE UN TITRE EN TOKENS SIGNIFICATIFS
 * - minuscules, sans accents
 * - mots vides retirés
 * - pluriel simple ramené au singulier ("terminaux" -> "terminal")
 */
function clm_dup_title_tokens( string $title ): array {
	$clean = remove_accents( strtolower( $title ) );
	$parts = preg_split( '/[^a-z0-9]+/', $clean, -1, PREG_SPLIT_NO_EMPTY );
	if ( ! is_array( $parts ) ) {
		return [];
	}

	$stop   = clm_dup_stopwords();
	$tokens = [];

	foreach ( $parts as $part ) {
		if ( in_array( $part, $stop, true ) ) {
			continue;
		}
		$len = strlen( $part );
		if ( $len >= 5 && 's' === substr( $part, -1 ) && 'ss' !== substr( $part, -2 ) && 'us' !== substr( $part, -2 ) ) {
			$part = substr( $part, 0, -1 );
		}
		if ( strlen( $part ) >= 2 || ctype_digit( $part ) ) {
			$tokens[ $part ] = true;
		}
	}

	return array_keys( $tokens );
}

/**
 * L'HÔTE FAIT-IL PARTIE DU SITE LUI-MÊME ?
 */
function clm_dup_is_self_host( string $host, string $home_host ): bool {
	if ( '' === $home_host ) {
		return false;
	}
	if ( $host === $home_host || $host === 'www.' . $home_host ) {
		return true;
	}
	$suffix = '.' . $home_host;
	return substr( $host, -strlen( $suffix ) ) === $suffix;
}

/**
 * EXTRAIT LES URLS EXTERNES D'UN CONTENU (normalisées, sans query/fragment)
 * - le domaine du site lui-même est exclu (liens internes, médias hébergés)
 * - les hôtes d'infrastructure génériques sont exclus
 */
function clm_dup_extract_urls( string $content ): array {
	if ( '' === trim( $content ) ) {
		return [];
	}
	if ( ! preg_match_all( '/https?:\/\/[^\s"\'<>]+/i', $content, $matches ) ) {
		return [];
	}

	$home_host  = (string) wp_parse_url( home_url(), PHP_URL_HOST );
	$skip_hosts = [
		'schema.org', 'w.org', 's.w.org', 'wordpress.org',
		'googleapis.com', 'gstatic.com', 'goo.gl',
	];
	$urls = [];

	foreach ( $matches[0] as $raw ) {
		$raw = rtrim( $raw, ".,;:!?)]\"'" );
		$p   = wp_parse_url( $raw );
		if ( empty( $p['host'] ) ) {
			continue;
		}

		$host = strtolower( $p['host'] );
		if ( 0 === strpos( $host, 'www.' ) ) {
			$host = substr( $host, 4 );
		}
		if ( clm_dup_is_self_host( $host, $home_host ) || in_array( $host, $skip_hosts, true ) ) {
			continue;
		}

		$path = isset( $p['path'] ) ? $p['path'] : '';
		$path = rtrim( $path, '/' );
		$urls[ $host . $path ] = true;
	}

	return array_keys( $urls );
}

/**
 * SIGNAUX DE RESSEMBLANCE ENTRE DEUX ARTICLES
 * Retourne [ score_titre (0..1), nb_urls_communes, similarite_urls (0..1) ]
 */
function clm_dup_pair_signals( array $a, array $b ): array {
	// Titre : recouvrement / plus petit ensemble (containment)
	$shared_t = 0;
	$small = ( count( $a['tset'] ) <= count( $b['tset'] ) ) ? $a['tset'] : $b['tset'];
	$big   = ( $small === $a['tset'] ) ? $b['tset'] : $a['tset'];
	foreach ( $small as $tok => $_ ) {
		if ( isset( $big[ $tok ] ) ) {
			$shared_t++;
		}
	}
	$min_t = min( count( $a['tset'] ), count( $b['tset'] ) );

	$title_score = 0.0;
	if ( $min_t > 0 && ( $shared_t >= 2 || ( 1 === $min_t && 1 === $shared_t ) ) ) {
		$title_score = $shared_t / $min_t;
	}

	// URLs externes communes
	$shared_u = 0;
	foreach ( $a['uset'] as $u => $_ ) {
		if ( isset( $b['uset'][ $u ] ) ) {
			$shared_u++;
		}
	}
	$union_u = count( $a['uset'] ) + count( $b['uset'] ) - $shared_u;
	$url_jac = $union_u > 0 ? $shared_u / $union_u : 0.0;

	return [ $title_score, $shared_u, $url_jac ];
}

/**
 * UNION-FIND : racine d'un élément (avec compression de chemin)
 */
function clm_dup_find( array &$parent, int $x ): int {
	while ( $parent[ $x ] !== $x ) {
		$parent[ $x ] = $parent[ $parent[ $x ] ];
		$x = $parent[ $x ];
	}
	return $x;
}

/**
 * ANALYSE COMPLÈTE : retourne les groupes de doublons
 *
 * Args: method (both|title|url), threshold (0..1), months (0 = tout), statuses[]
 * Retour: [ items, groups (racine => membres triés par date), scores (racine => meilleur score), pairs, total ]
 */
function clm_dup_analyze( array $args ): array {
	$query_args = [
		'post_type'      => 'post',
		'post_status'    => $args['statuses'],
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	];
	if ( $args['months'] > 0 ) {
		$after = gmdate( 'Y-m-d H:i:s', strtotime( '-' . (int) $args['months'] . ' months' ) );
		$query_args['date_query'] = [ [ 'after' => $after, 'inclusive' => true ] ];
	}

	$posts = get_posts( $query_args );

	$items = [];
	foreach ( $posts as $post ) {
		$tokens = clm_dup_title_tokens( $post->post_title );
		$urls   = clm_dup_extract_urls( (string) $post->post_content );
		$items[] = [
			'id'     => $post->ID,
			'title'  => $post->post_title,
			'date'   => $post->post_date,
			'ts'     => strtotime( $post->post_date ),
			'status' => $post->post_status,
			'tokens' => $tokens,
			'tset'   => array_flip( $tokens ),
			'urls'   => $urls,
			'uset'   => array_flip( $urls ),
		];
	}

	$n = count( $items );

	// URLs omniprésentes (footer, soutien…) : trop fréquentes pour discriminer -> retirées
	$url_df  = [];
	foreach ( $items as $it ) {
		foreach ( $it['urls'] as $u ) {
			$url_df[ $u ] = ( $url_df[ $u ] ?? 0 ) + 1;
		}
	}
	$url_cap = max( 10, (int) floor( $n * 0.25 ) );
	foreach ( $items as $i => $it ) {
		$kept = [];
		foreach ( $it['urls'] as $u ) {
			if ( $url_df[ $u ] <= $url_cap ) {
				$kept[ $u ] = true;
			}
		}
		$items[ $i ]['urls'] = array_keys( $kept );
		$items[ $i ]['uset'] = $kept;
	}

	// Paires candidates via index inversé (on ne compare que ce qui partage un token ou une URL)
	$cand = [];
	if ( 'url' !== $args['method'] ) {
		$index = [];
		foreach ( $items as $i => $it ) {
			foreach ( $it['tokens'] as $tok ) {
				$index[ $tok ][] = $i;
			}
		}
		$cap = max( 80, (int) floor( $n * 0.12 ) );
		foreach ( $index as $ids ) {
			$c = count( $ids );
			if ( $c < 2 || $c > $cap ) {
				continue;
			}
			for ( $a = 0; $a < $c; $a++ ) {
				for ( $b = $a + 1; $b < $c; $b++ ) {
					$cand[ $ids[ $a ] . ':' . $ids[ $b ] ] = true;
				}
			}
		}
	}
	if ( 'title' !== $args['method'] ) {
		$uindex = [];
		foreach ( $items as $i => $it ) {
			foreach ( $it['urls'] as $u ) {
				$uindex[ $u ][] = $i;
			}
		}
		foreach ( $uindex as $ids ) {
			$c = count( $ids );
			if ( $c < 2 ) {
				continue;
			}
			for ( $a = 0; $a < $c; $a++ ) {
				for ( $b = $a + 1; $b < $c; $b++ ) {
					$cand[ $ids[ $a ] . ':' . $ids[ $b ] ] = true;
				}
			}
		}
	}

	// Évaluation des paires candidates
	$parent = range( 0, max( 0, $n - 1 ) );
	$pairs  = [];

	foreach ( array_keys( $cand ) as $key ) {
		$parts = explode( ':', $key );
		$i = (int) $parts[0];
		$j = (int) $parts[1];

		list( $title_score, $shared_u, $url_jac ) = clm_dup_pair_signals( $items[ $i ], $items[ $j ] );

		$url_match = ( $shared_u >= 2 ) || ( $shared_u >= 1 && $url_jac >= 0.5 );

		if ( 'title' === $args['method'] ) {
			$match = $title_score >= $args['threshold'];
		} elseif ( 'url' === $args['method'] ) {
			$match = $url_match;
		} else {
			$match = ( $title_score >= $args['threshold'] ) || $url_match;
		}
		if ( ! $match ) {
			continue;
		}

		$url_score = $url_match ? max( $url_jac, min( 1.0, $shared_u / 2 ) ) : 0.0;
		$pairs[] = [
			'i'     => $i,
			'j'     => $j,
			'score' => max( $title_score, $url_score ),
		];

		$ri = clm_dup_find( $parent, $i );
		$rj = clm_dup_find( $parent, $j );
		if ( $ri !== $rj ) {
			$parent[ $rj ] = $ri;
		}
	}

	// Groupes = composantes connexes de >= 2 articles
	$members = [];
	foreach ( $items as $i => $it ) {
		$root = clm_dup_find( $parent, $i );
		$members[ $root ][] = $i;
	}

	$scores = [];
	foreach ( $pairs as $pair ) {
		$root = clm_dup_find( $parent, $pair['i'] );
		if ( ! isset( $scores[ $root ] ) || $pair['score'] > $scores[ $root ] ) {
			$scores[ $root ] = $pair['score'];
		}
	}

	$groups = [];
	foreach ( $members as $root => $idxs ) {
		if ( count( $idxs ) < 2 ) {
			continue;
		}
		usort( $idxs, function ( $a, $b ) use ( $items ) {
			return $items[ $a ]['ts'] <=> $items[ $b ]['ts'];
		} );
		$groups[ $root ] = [
			'members' => $idxs,
			'score'   => $scores[ $root ] ?? 0.0,
		];
	}

	uasort( $groups, function ( $a, $b ) {
		if ( $b['score'] <=> $a['score'] ) {
			return $b['score'] <=> $a['score'];
		}
		return count( $b['members'] ) <=> count( $a['members'] );
	} );

	return [
		'items'  => $items,
		'groups' => $groups,
		'pairs'  => count( $pairs ),
		'total'  => $n,
	];
}

/**
 * SOUS-MENU « DOUBLONS » SOUS ARTICLES
 */
add_action( 'admin_menu', function () {
	add_submenu_page(
		'edit.php',
		'Doublons d\'articles',
		'🔍 Doublons',
		'edit_posts',
		'clm-duplicates',
		'clm_dup_render_page'
	);
} );

/**
 * BADGE DE STATUT EN FRANÇAIS
 */
function clm_dup_status_badge( string $status ): string {
	$map = [
		'publish' => [ 'Publié', '#00a32a' ],
		'future'  => [ 'Planifié', '#2271b1' ],
		'draft'   => [ 'Brouillon', '#787c82' ],
		'pending' => [ 'En attente', '#dba617' ],
	];
	if ( ! isset( $map[ $status ] ) ) {
		return '<span style="color:#787c82">' . esc_html( $status ) . '</span>';
	}
	return '<span style="display:inline-block;padding:1px 8px;border-radius:999px;font-size:11px;line-height:18px;color:#fff;background:' . $map[ $status ][1] . '">' . esc_html( $map[ $status ][0] ) . '</span>';
}

/**
 * PAGE : DÉTECTION DES DOUBLONS
 */
function clm_dup_render_page() {
	$all_statuses = [ 'publish', 'future', 'draft', 'pending' ];

	$method   = ( isset( $_GET['m'] ) && in_array( $_GET['m'], [ 'both', 'title', 'url' ], true ) ) ? $_GET['m'] : 'title';
	$thresh_p = isset( $_GET['t'] ) ? absint( $_GET['t'] ) : 70;
	$thresh_p = max( 70, min( 100, $thresh_p ) );
	$months   = isset( $_GET['p'] ) ? absint( $_GET['p'] ) : 12;
	if ( ! in_array( $months, [ 1, 3, 6, 12, 24, 0 ], true ) ) {
		$months = 12;
	}
	$statuses = isset( $_GET['st'] ) ? (array) $_GET['st'] : $all_statuses;
	$statuses = array_values( array_intersect( $statuses, $all_statuses ) );
	if ( empty( $statuses ) ) {
		$statuses = $all_statuses;
	}

	$base_url = admin_url( 'admin.php?page=clm-duplicates' );

	echo '<div class="wrap">';
	echo '<h1>Doublons d\'articles <span style="font-size:0.55em;font-weight:600;color:#2271b1;vertical-align:middle;background:#e8f0fe;padding:2px 8px;border-radius:999px;margin-left:6px;">v2</span></h1>';
	echo '<p class="description">Détecte les articles traitant du même sujet : similarité de titre et/ou URLs externes communes. '
		. 'Aucune suppression automatique — vous décidez article par article.</p>';

	if ( isset( $_GET['clm_dup_trashed'] ) ) {
		echo '<div id="message" class="notice notice-success is-dismissible"><p>Article déplacé à la corbeille.</p></div>';
	}

	// ===== Formulaire de filtres =====
	echo '<form method="get" style="background:#fff;border:1px solid #c3c4c7;border-radius:8px;padding:12px 16px;margin:12px 0 20px;display:flex;flex-wrap:wrap;gap:16px;align-items:flex-end;">';
	echo '<input type="hidden" name="page" value="clm-duplicates" />';

	echo '<div><label for="clm-dup-m" style="display:block;font-weight:600;margin-bottom:4px;">Méthode</label>';
	echo '<select id="clm-dup-m" name="m">';
	echo '<option value="both"' . selected( $method, 'both', false ) . '>Titres + URLs (large)</option>';
	echo '<option value="title"' . selected( $method, 'title', false ) . '>Titres seulement</option>';
	echo '<option value="url"' . selected( $method, 'url', false ) . '>URLs seulement</option>';
	echo '</select></div>';

	echo '<div><label for="clm-dup-t" style="display:block;font-weight:600;margin-bottom:4px;">Seuil de similarité des titres</label>';
	echo '<select id="clm-dup-t" name="t">';
	foreach ( [ 70, 75, 80, 85, 90, 95, 100 ] as $pct ) {
		echo '<option value="' . $pct . '"' . selected( $thresh_p, $pct, false ) . '>' . $pct . ' %</option>';
	}
	echo '</select></div>';

	echo '<div><label for="clm-dup-p" style="display:block;font-weight:600;margin-bottom:4px;">Période</label>';
	echo '<select id="clm-dup-p" name="p">';
	foreach ( [ 1 => '1 mois', 3 => '3 mois', 6 => '6 mois', 12 => '12 mois', 24 => '2 ans', 0 => 'Tout' ] as $m_val => $m_label ) {
		echo '<option value="' . $m_val . '"' . selected( $months, $m_val, false ) . '>' . esc_html( $m_label ) . '</option>';
	}
	echo '</select></div>';

	echo '<div><span style="display:block;font-weight:600;margin-bottom:4px;">Statuts</span>';
	foreach ( [ 'publish' => 'Publiés', 'future' => 'Planifiés', 'draft' => 'Brouillons', 'pending' => 'En attente' ] as $st_key => $st_label ) {
		echo '<label style="margin-right:10px;"><input type="checkbox" name="st[]" value="' . esc_attr( $st_key ) . '"' . checked( in_array( $st_key, $statuses, true ), true, false ) . '> ' . esc_html( $st_label ) . '</label>';
	}
	echo '</div>';

	echo '<div><button type="submit" class="button button-primary">🔍 Analyser</button></div>';
	echo '</form>';

	echo '<p class="description" style="margin:-12px 0 16px;">'
		. 'Titres : mots-clés des titres similaires · URLs : liens externes communs dans le contenu · '
		. 'Titres + URLs : les deux signaux combinés (le plus large, donc le plus bruyant).</p>';

	// ===== Analyse =====
	$result = clm_dup_analyze( [
		'method'    => $method,
		'threshold' => $thresh_p / 100,
		'months'    => $months,
		'statuses'  => $statuses,
	] );

	$items  = $result['items'];
	$groups = $result['groups'];
	$n      = $result['total'];
	$pairs  = $result['pairs'];

	$multi = 0;
	foreach ( $groups as $g ) {
		if ( count( $g['members'] ) >= 3 ) {
			$multi++;
		}
	}

	$label = ( 1 === count( $groups ) ) ? 'groupe' : 'groupes';
	echo '<p style="font-size:14px;"><strong>' . number_format_i18n( count( $groups ) ) . ' ' . $label . '</strong> détectés'
		. ( $multi > 0 ? ' (dont ' . number_format_i18n( $multi ) . ' triplons ou plus)' : '' )
		. ' — ' . number_format_i18n( $pairs ) . ' paires suspectes parmi ' . number_format_i18n( $n ) . ' articles analysés.</p>';

	if ( empty( $groups ) ) {
		echo '<div class="notice notice-success"><p>Aucun doublon détecté avec ces réglages. 🎉</p></div>';
		echo '</div>';
		return;
	}

	// ===== Groupes =====
	$group_num = 0;
	foreach ( $groups as $g ) {
		$group_num++;
		$members = $g['members'];
		$ref     = $items[ $members[0] ];
		$count   = count( $members );

		echo '<div style="background:#fff;border:1px solid #c3c4c7;border-radius:8px;margin-bottom:18px;overflow:hidden;">';

		$kind = ( $count >= 3 ) ? 'Triplon+' : 'Paire';
		echo '<div style="padding:10px 16px;background:#f6f7f7;border-bottom:1px solid #c3c4c7;display:flex;flex-wrap:wrap;gap:10px;align-items:center;">';
		echo '<strong>Groupe ' . $group_num . ' · ' . $kind . ' (' . $count . ' articles)</strong>';
		echo '<span class="title-count" style="background:#e8f0fe;color:#2271b1;">Meilleure correspondance : ' . intval( round( $g['score'] * 100 ) ) . ' %</span>';
		echo '</div>';

		echo '<table class="wp-list-table widefat fixed striped" style="border:none;">';
		echo '<thead><tr>'
			. '<th style="width:90px;">Statut</th>'
			. '<th>Titre</th>'
			. '<th style="width:130px;">Date</th>'
			. '<th style="width:100px;">Écart</th>'
			. '<th style="width:230px;">Pourquoi</th>'
			. '<th style="width:230px;">Actions</th>'
			. '</tr></thead><tbody>';

		foreach ( $members as $pos => $idx ) {
			$it = $items[ $idx ];

			echo '<tr>';

			echo '<td>' . clm_dup_status_badge( $it['status'] ) . '</td>';

			$edit_url = get_edit_post_link( $it['id'], 'raw' );
			$view_url = get_permalink( $it['id'] );
			$thumb    = get_the_post_thumbnail( $it['id'], [ 50, 50 ], [ 'style' => 'width:50px;height:50px;object-fit:cover;border-radius:6px;flex:none;' ] );
			if ( ! $thumb ) {
				$thumb = '<span style="display:inline-block;width:50px;height:50px;border-radius:6px;background:#f0f0f1;color:#a7aaad;text-align:center;line-height:50px;font-size:20px;flex:none;">🖼️</span>';
			}
			echo '<td><div style="display:flex;align-items:center;gap:10px;min-height:54px;">' . $thumb
				. '<strong style="min-width:0;"><a class="row-title" href="' . esc_url( $view_url ) . '" target="_blank" rel="noopener">' . esc_html( $it['title'] ) . '</a></strong></div></td>';

			echo '<td>' . esc_html( mysql2date( 'd/m/Y H:i', $it['date'] ) ) . '</td>';

			if ( 0 === $pos ) {
				echo '<td><span style="color:#787c82;">référence</span></td>';
				echo '<td><span style="color:#787c82;">—</span></td>';
			} else {
				$gap_days = (int) floor( ( $it['ts'] - $ref['ts'] ) / DAY_IN_SECONDS );

				list( $t_score, $shared_u, $url_jac ) = clm_dup_pair_signals( $it, $ref );

				$why = [];
				if ( $t_score > 0 ) {
					$why[] = 'Titre ' . intval( round( $t_score * 100 ) ) . ' %';
				}
				if ( $shared_u > 0 ) {
					$why[] = $shared_u . ' URL' . ( $shared_u > 1 ? 's' : '' ) . ' commune' . ( $shared_u > 1 ? 's' : '' );
				}
				if ( empty( $why ) ) {
					$why[] = 'Correspondance';
				}

				$gap_style = ( $gap_days <= 31 ) ? 'color:#d63638;font-weight:600;' : 'color:#787c82;';
				echo '<td style="' . $gap_style . '">+' . number_format_i18n( $gap_days ) . ' j</td>';
				echo '<td>' . esc_html( implode( ' · ', $why ) ) . '</td>';
			}

			$trash_url = wp_nonce_url(
				admin_url( 'admin-post.php?action=clm_dup_trash&post=' . $it['id'] ),
				'clm_dup_trash_' . $it['id']
			);

			echo '<td>'
				. '<a class="button button-small" href="' . esc_url( $edit_url ) . '">Modifier</a> '
				. '<a class="button button-small" style="color:#d63638;border-color:#d63638;" href="' . esc_url( $trash_url ) . '" onclick="return confirm(&#39;Mettre à la corbeille cet article ?&#39;);">🗑 Corbeille</a>'
				. '</td>';

			echo '</tr>';
		}

		echo '</tbody></table>';
		echo '</div>';
	}

	echo '</div>';
}

/**
 * ACTION : METTRE À LA CORBEILLE PUIS REVENIR SUR LA PAGE DOUBLONS
 */
add_action( 'admin_post_clm_dup_trash', function () {
	$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
	$back    = wp_get_referer();
	if ( ! $back ) {
		$back = admin_url( 'admin.php?page=clm-duplicates' );
	}

	check_admin_referer( 'clm_dup_trash_' . $post_id );

	if ( $post_id < 1 ) {
		wp_safe_redirect( add_query_arg( 'clm_dup_error', 'missing', $back ) );
		exit;
	}
	if ( ! current_user_can( 'delete_post', $post_id ) ) {
		wp_safe_redirect( add_query_arg( 'clm_dup_error', 'forbidden', $back ) );
		exit;
	}

	wp_trash_post( $post_id );

	wp_safe_redirect( add_query_arg( 'clm_dup_trashed', '1', $back ) );
	exit;
} );

