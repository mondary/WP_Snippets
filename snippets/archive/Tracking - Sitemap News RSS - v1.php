/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/052__id-110__tracker-sitemap-news-rss.php
 * Display name: TRACKER - sitemap-news.rss
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 110
 * Online modified: 2025-05-13 09:26:23
 * Online revision: 2
 * Exact duplicate group: non
 * Version family: TRACKER - sitemap-news.rss (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/052__id-110__tracker-sitemap-news-rss.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: init, template_redirect
 * Fonctions clefs: mondary_news_rss_feed, generate_mondary_news_rss, mondary_add_custom_feed_rewrite, mondary_pretty_news_feed_redirect
 * Lignes / octets (brut): 87 / 3003
 * Hash code normalise (sha256): 32f5ede3fd015c36a352d9fbc31637c329e4c5ae6a95f3105c78db88d540ace3
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: INACTIVE__global__tracker-sitemap-news-rss__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__tracker-sitemap-news-rss__v1__src-wp_snippets_online_current.php
 * Resume fonctionnalites: tracking / analytics, flux RSS, automatisation date/programmation, 2 hook(s) WP, 4 fonction(s) clef
 * Features detectees: rss, tracking-analytics, scheduler-date
 * Dependances probables: WordPress core hooks
 * Hooks WP: init, template_redirect
 * Fonctions clefs: mondary_news_rss_feed, generate_mondary_news_rss, mondary_add_custom_feed_rewrite, mondary_pretty_news_feed_redirect
 * APIs WP detectees: add_feed, add_action, get_option, the_post, the_title_rss, the_permalink_rss, get_post_time, get_the_date, wp_reset_postdata, add_rewrite_rule, is_feed, wp_redirect, home_url
 * Signatures contenu: html-markup
 * Lignes / octets: 100 / 3697
 * Empreinte code (sha256): 91054290397b9472c7be06c9e53f3f7e39335a16f3625ac7882846a918367762
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: INACTIVE__global__tracker-sitemap-news-rss__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__tracker-sitemap-news-rss__v1__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: INACTIVE
 * Cluster principal: tracking_analytics
 * Clusters secondaires: rss_feed, scheduler_posts
 * Domaine: tracking
 * Confiance: medium
 * Scores (top): tracking_analytics=12, rss_feed=12, scheduler_posts=8
 * Raisons principales: tracker, analytics
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

// 1. Créer un flux RSS personnalisé compatible Google News
function mondary_news_rss_feed() {
    add_feed('sitemap-news', 'generate_mondary_news_rss');
}
add_action('init', 'mondary_news_rss_feed');

function generate_mondary_news_rss() {
    header('Content-Type: application/rss+xml; charset=' . get_option('blog_charset'), true);

    $args = array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => 100,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'date_query'     => array(
            array('after' => '2 days ago')
        )
    );

    $news_posts = new WP_Query($args);

    echo '<?xml version="1.0" encoding="' . get_option('blog_charset') . '" ?>' . "\n";
    ?>
<rss version="2.0"
    xmlns:news="http://www.google.com/schemas/sitemap-news/0.9"
    xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
    <title><?php bloginfo_rss('name'); ?> - Google News</title>
    <link><?php bloginfo_rss('url'); ?></link>
    <description>Articles récents publiés sur <?php bloginfo_rss('name'); ?></description>
    <language>fr</language>
    <atom:link href="<?php bloginfo_rss('url'); ?>/sitemap-news.rss" rel="self" type="application/rss+xml" />

    <?php while ($news_posts->have_posts()) : $news_posts->the_post(); ?>
      <item>
        <title><?php the_title_rss(); ?></title>
        <link><?php the_permalink_rss(); ?></link>
        <pubDate><?php echo get_post_time('r', true); ?></pubDate>
        <guid><?php the_permalink_rss(); ?></guid>
        <news:news>
          <news:publication>
            <news:name><?php bloginfo_rss('name'); ?></news:name>
            <news:language>fr</news:language>
          </news:publication>
          <news:publication_date><?php echo get_the_date('c'); ?></news:publication_date>
          <news:title><?php the_title_rss(); ?></news:title>
        </news:news>
      </item>
    <?php endwhile; ?>
  </channel>
</rss>
<?php
    wp_reset_postdata();
}

// 2. Créer une URL lisible /sitemap-news.rss
function mondary_add_custom_feed_rewrite() {
    add_rewrite_rule(
        '^sitemap-news\.rss$',
        'index.php?feed=sitemap-news',
        'top'
    );
}
add_action('init', 'mondary_add_custom_feed_rewrite');

// 3. Rediriger automatiquement ?feed=sitemap-news vers /sitemap-news.rss (facultatif mais propre)
function mondary_pretty_news_feed_redirect() {
    if (is_feed('sitemap-news')) {
        wp_redirect(home_url('/sitemap-news.rss'), 301);
        exit;
    }
}
add_action('template_redirect', 'mondary_pretty_news_feed_redirect');
