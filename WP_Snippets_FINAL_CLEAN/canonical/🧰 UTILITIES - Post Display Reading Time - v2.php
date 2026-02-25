/* CLM-CREATED-AT: 2026-02-25 */
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/active/global/009__id-14__post-display-reading-time.php
 * Display name: POST - Display reading time
 * Scope: global
 * Online snippet: oui
 * Online active: oui
 * Online ID: 14
 * Online modified: 2024-12-06 15:58:33
 * Online revision: 3
 * Exact duplicate group: oui (3d3bee72bf61…, 2 membres)
 * Canonical exact group ID: 82
 * Version family: DUP POST - Display reading time (1 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets_Online_Current/active/global/009__id-14__post-display-reading-time.php
 * Is family latest: oui
 * Canonical reasons: exact-group-canonical, protected-online-active
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 27 / 1087
 * Hash code normalise (sha256): 3d3bee72bf613b16354bf08d3abd58379435cbd042ded4632cbd30ab8eefd4a0
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: ACTIVE__global__post-display-reading-time__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__post-display-reading-time__v2__src-wp_snippets_online_current.php
 * Resume fonctionnalites: snippet PHP / JS / CSS
 * Features detectees: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: aucun
 * APIs WP detectees: get_post_field, get_the_id
 * Signatures contenu: html-markup
 * Lignes / octets: 40 / 1645
 * Empreinte code (sha256): 9781f1a1437031a91d98185d74cfd50359955498453503fe9d2418a7d50c8496
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: ACTIVE__global__post-display-reading-time__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__post-display-reading-time__v2__src-wp_snippets_online_current.php
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

/**
 * Affiche le temps de lecture estimé d'un article.
 *
 * Calcule le temps de lecture en fonction du nombre de mots et d'une vitesse de lecture par défaut.
 * Affiche ensuite le temps de lecture estimé dans un paragraphe.
 */
 
 $reading_speed = 200; // 200 words per minute
$content       = get_post_field( 'post_content', get_the_id() );
$word_count    = str_word_count( strip_tags( $content ) );
$reading_time  = ceil( $word_count / $reading_speed );

echo '<p>Temps de lecture estimé : ' . absint( $reading_time ) . ' ' . _n( 'minute', 'minutes', $reading_time ) . '</p>';