/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: archives
 * Source path: archives/WP_RSS - Add featured image to RSS.php
 * Display name: WP_RSS - Add featured image to RSS
 * Scope: global
 * Online snippet: non
 * Exact duplicate group: oui (6e2d5a3b9aba…, 2 membres)
 * Canonical exact group ID: 122
 * Version family: DUP RSS - Add featured image to RSS (1 variantes)
 * Version: v2
 * Recommended latest in family: archives/WP_RSS - Add featured image to RSS.php
 * Is family latest: oui
 * Archive reasons: exact-duplicate
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: the_excerpt_rss, the_content_feed
 * Fonctions clefs: featuredtoRSS
 * Lignes / octets (brut): 16 / 574
 * Hash code normalise (sha256): 6e2d5a3b9aba23ad7c93825e38ef2c98878e8023ddf9293567ecf9050a14711c
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: rss-add-featured-image-to-rss__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/rss-add-featured-image-to-rss__v001.php
 * Resume fonctionnalites: flux RSS, 2 hook(s) WP, 1 fonction(s) clef
 * Features detectees: rss, rss-hooks
 * Dependances probables: WordPress core hooks
 * Hooks WP: the_excerpt_rss, the_content_feed
 * Fonctions clefs: featuredtoRSS
 * APIs WP detectees: get_the_post_thumbnail, add_filter
 * Signatures contenu: html-markup
 * Lignes / octets: 39 / 1458
 * Empreinte code (sha256): 7254019957d26d2a5bfff3d519f4dc357881ccfec5b20636ee7dcee9f87f71fa
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: rss-add-featured-image-to-rss__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/rss-add-featured-image-to-rss__v001.php
 * Bucket FINAL: archive
 * Statut: LOCAL
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