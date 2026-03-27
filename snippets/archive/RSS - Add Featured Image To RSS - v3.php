/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/007__id-12__rss-add-featured-image-to-rss.php
 * Display name: RSS - Add featured image to RSS
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 12
 * Online modified: 2025-03-07 14:44:06
 * Online revision: 4
 * Exact duplicate group: oui (6e2d5a3b9aba…, 2 membres)
 * Canonical exact group ID: 122
 * Version family: DUP RSS - Add featured image to RSS (1 variantes)
 * Version: v3
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/007__id-12__rss-add-featured-image-to-rss.php
 * Is family latest: oui
 * Canonical reasons: exact-group-canonical
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: the_excerpt_rss, the_content_feed
 * Fonctions clefs: featuredtoRSS
 * Lignes / octets (brut): 30 / 1136
 * Hash code normalise (sha256): 6e2d5a3b9aba23ad7c93825e38ef2c98878e8023ddf9293567ecf9050a14711c
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: rss-add-featured-image-to-rss__v002.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/rss-add-featured-image-to-rss__v002.php
 * Resume fonctionnalites: flux RSS, 2 hook(s) WP, 1 fonction(s) clef
 * Features detectees: rss, rss-hooks
 * Dependances probables: WordPress core hooks
 * Hooks WP: the_excerpt_rss, the_content_feed
 * Fonctions clefs: featuredtoRSS
 * APIs WP detectees: get_the_post_thumbnail, add_filter
 * Signatures contenu: html-markup
 * Lignes / octets: 43 / 1667
 * Empreinte code (sha256): 4e23cabdc328d188133f59676d41fc3ab654d5dec0ebf4735995d2b49b1d21ac
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: rss-add-featured-image-to-rss__v002.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/rss-add-featured-image-to-rss__v002.php
 * Bucket FINAL: archive
 * Statut: INACTIVE
 * Cluster principal: rss_feed
 * Clusters secondaires: media_images
 * Domaine: rss
 * Confiance: medium
 * Scores (top): rss_feed=12, media_images=12
 * Raisons principales: rss, feed
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

// Permet d'ajouter les features images dans le flux RSS, a combiner avec l'extension RSS Feed Styles


function featuredtoRSS($content) {
    global $post;

    if (has_post_thumbnail($post->ID)) {
        // Utiliser 'full' pour la taille de l'image
        $content = '<div style="width: 100%;"><div>' . get_the_post_thumbnail($post->ID, 'full', array('style' => 'width: 100%; height: auto; margin-bottom: 15px;')) . '</div></div>' . $content;
    }

    return $content;
}

add_filter('the_excerpt_rss', 'featuredtoRSS');
add_filter('the_content_feed', 'featuredtoRSS');