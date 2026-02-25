/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: WP_Snippets
 * Source path: WP_Snippets/WP_POST - Display Reading Time.php
 * Display name: WP_POST - Display Reading Time
 * Scope: front-end
 * Online snippet: non
 * Exact duplicate group: oui (3d3bee72bf61…, 2 membres)
 * Canonical exact group ID: 82
 * Version family: DUP POST - Display reading time (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets/WP_POST - Display Reading Time.php
 * Is family latest: oui
 * Archive reasons: exact-duplicate
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 13 / 584
 * Hash code normalise (sha256): 3d3bee72bf613b16354bf08d3abd58379435cbd042ded4632cbd30ab8eefd4a0
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: post-display-reading-time__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-display-reading-time__v001.php
 * Resume fonctionnalites: snippet PHP / JS / CSS
 * Features detectees: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: aucun
 * APIs WP detectees: get_post_field, get_the_id
 * Signatures contenu: html-markup
 * Lignes / octets: 36 / 1427
 * Empreinte code (sha256): 5b438544c25346899a20e700573fd348b2506d95c6806edf97b69cc686ace782
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: post-display-reading-time__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-display-reading-time__v001.php
 * Bucket FINAL: archive
 * Statut: LOCAL
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