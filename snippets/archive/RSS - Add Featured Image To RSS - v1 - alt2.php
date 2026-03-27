/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: A TRIER
 * Source path: A TRIER/WP_RSS.image.feed/RSS - Add featured image to RSS.php
 * Display name: RSS - Add featured image to RSS
 * Scope: global
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: RSS - Add featured image to RSS (1 variantes)
 * Version: v1
 * Recommended latest in family: A TRIER/WP_RSS.image.feed/RSS - Add featured image to RSS.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: the_excerpt_rss, the_content_feed
 * Fonctions clefs: featuredtoRSS
 * Lignes / octets (brut): 17 / 608
 * Hash code normalise (sha256): 242e08d9d7136772a9b1124181ebb4bf98e3c4a5ed731e0a7e89530002201b04
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: LOCAL__global__rss-add-featured-image-to-rss__v1__src-a-trier.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/LOCAL__global__rss-add-featured-image-to-rss__v1__src-a-trier.php
 * Resume fonctionnalites: flux RSS, 2 hook(s) WP, 1 fonction(s) clef
 * Features detectees: rss, rss-hooks
 * Dependances probables: WordPress core hooks
 * Hooks WP: the_excerpt_rss, the_content_feed
 * Fonctions clefs: featuredtoRSS
 * APIs WP detectees: get_the_post_thumbnail, add_filter
 * Signatures contenu: html-markup
 * Lignes / octets: 39 / 1450
 * Empreinte code (sha256): 713f6a6f18646a901ecace5b38ace42a1ecf7c005a95175103559e5b3e41d210
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: LOCAL__global__rss-add-featured-image-to-rss__v1__src-a-trier.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/LOCAL__global__rss-add-featured-image-to-rss__v1__src-a-trier.php
 * Bucket FINAL: canonical
 * Statut: LOCAL
 * Cluster principal: rss_feed
 * Clusters secondaires: media_images
 * Domaine: rss
 * Confiance: medium
 * Scores (top): rss_feed=12, media_images=12
 * Raisons principales: rss, feed
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

// Code PHP a inserer dans WPcode
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