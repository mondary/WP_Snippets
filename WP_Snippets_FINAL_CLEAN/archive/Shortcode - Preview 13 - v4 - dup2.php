/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/067__id-125__wp-shortcode-preview-tiles.php
 * Display name: WP_Shortcode_preview_tiles
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 125
 * Online modified: 2025-07-08 08:49:00
 * Online revision: 9
 * Exact duplicate group: oui (2b25feb9312e…, 2 membres)
 * Canonical exact group ID: 152
 * Version family: DUP WP_Shortcode_preview_tiles (1 variantes)
 * Version: v4
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/067__id-125__wp-shortcode-preview-tiles.php
 * Is family latest: oui
 * Canonical reasons: exact-group-canonical
 * Features: shortcode
 * Dependances probables: jQuery
 * Hooks WP: aucun
 * Fonctions clefs: preview_links_shortcode, display_links_tiles, display_link_tile, get_tiles_styles, get_thumbnail_script_tiles, get_external_links_from_content
 * Shortcodes: preview_links
 * Lignes / octets (brut): 222 / 7353
 * Hash code normalise (sha256): 2b25feb9312e32d1b3892ce2c2a07acfb173c25fadd001c1e3529266894c79fa
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: INACTIVE__global__wp-shortcode-preview-tiles__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__wp-shortcode-preview-tiles__v2__src-wp_snippets_online_current.php
 * Resume fonctionnalites: shortcode WordPress, UI frontend (CSS/HTML), 6 fonction(s) clef
 * Features detectees: shortcode, css-ui
 * Dependances probables: jQuery
 * Hooks WP: aucun
 * Fonctions clefs: preview_links_shortcode, display_links_tiles, display_link_tile, get_tiles_styles, get_thumbnail_script_tiles, get_external_links_from_content
 * Shortcodes: preview_links
 * Selecteurs / IDs: .thumbnail-container
 * APIs WP detectees: add_shortcode, get_external_links_from_content, get_tiles_styles, get_thumbnail_script_tiles, home_url
 * Signatures contenu: inline-style, inline-script, html-markup
 * Lignes / octets: 237 / 8149
 * Empreinte code (sha256): 64a1530fc85c8769eb15291738e4a3df651e77a21f7c374c58dbae925d117d4f
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: INACTIVE__global__wp-shortcode-preview-tiles__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__wp-shortcode-preview-tiles__v2__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: INACTIVE
 * Cluster principal: shortcode_preview
 * Clusters secondaires: aucun
 * Domaine: shortcode
 * Confiance: high
 * Scores (top): shortcode_preview=20, shortcode_other=2, frontend_ui_widget=2
 * Raisons principales: shortcode, preview_links, preview, shortcode-preview
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

/*
 * Plugin Name: External Links Preview Tiles
 * Description: Affiche les liens externes sous forme de tuiles modernes avec prévisualisation, favicon et titre centré, style inspiré de Netflix.
 * Version: 1.1
 */

if (!defined('ABSPATH')) exit;

add_shortcode('preview_links', 'preview_links_shortcode');

function preview_links_shortcode($atts, $content = null) {
    if (empty($content)) {
        return '<p>Veuillez ajouter des liens à prévisualiser.</p>';
    }
    $content = do_shortcode($content);
    $links = get_external_links_from_content($content);
    if (empty($links)) {
        return '<p>Aucun lien externe trouvé.</p>';
    }
    $output = display_links_tiles($links);
    $output .= get_tiles_styles();
    $output .= get_thumbnail_script_tiles();
    return $output;
}

function display_links_tiles($links) {
    $output = '<div class="external-links-tiles">';
    foreach ($links as $link) {
        $output .= display_link_tile($link);
    }
    $output .= '</div>';
    return $output;
}

function display_link_tile($link) {
    $url = esc_url($link['url']);
    $domain = parse_url($url, PHP_URL_HOST);
    $small_favicon = "https://www.google.com/s2/favicons?domain={$domain}&sz=32";
    $emoji = '';
    if (strpos($domain, 'stream') !== false || strpos($domain, 'flix') !== false) {
        $emoji = ' 🍿';
    }
    $output = '<a href="' . $url . '" class="external-link-tile" target="_blank" rel="noopener">';
    $output .= '<div class="tile-bg thumbnail-container" data-url="' . esc_attr($url) . '"><div class="loading">Chargement...</div></div>';
    $output .= '<div class="tile-content">';
    $output .= '<img src="' . esc_url($small_favicon) . '" class="tile-favicon" alt="">';
    $output .= '<div class="tile-title">' . esc_html($link['title'] ?? $domain) . $emoji . '</div>';
    $output .= '</div>';
    $output .= '</a>';
    return $output;
}

function get_tiles_styles() {
    return '
    <style>
    .external-links-tiles {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 12px;
        margin: 10px 0;
    }
    .external-link-tile {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-end;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0,0,0,0.18);
        background: transparent; /* plus de fond noir ici */
        min-width: 340px;
        min-height: 140px;
        aspect-ratio: 2.2/1;
        position: relative;
        transition: transform 0.2s, box-shadow 0.2s;
        margin: 4px;
        text-decoration: none;
    }
    .external-link-tile:hover {
        transform: translateY(-4px) scale(1.03);
        box-shadow: 0 8px 32px rgba(0,0,0,0.28);
    }
    .tile-bg {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        filter: blur(2.5px) brightness(0.92);
        z-index: 1;
        background: #23242a;
        min-height: 140px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .tile-content {
        position: relative;
        z-index: 2;
        padding: 32px 12px 18px 12px;
        width: 100%;
        text-align: center;
        color: #fff;
        font-size: 1.2em;
        font-weight: bold;
        text-shadow: 0 2px 8px rgba(0,0,0,0.35);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: rgba(35,36,42,0.55); /* fond sombre sous le texte seulement */
        border-radius: 0 0 10px 10px;
    }
    .tile-favicon {
        width: 40px;
        height: 40px;
        margin-bottom: 12px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.18);
        background: #fff;
    }
    .tile-title {
        color: #fff;
        font-size: 1.1em;
        font-weight: bold;
        margin-top: 0;
        margin-bottom: 0;
        letter-spacing: 0.01em;
        text-shadow: 0 2px 8px rgba(0,0,0,0.35);
        word-break: break-all;
    }
    .loading {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        font-style: italic;
        color: #999;
    }
    </style>';
}

function get_thumbnail_script_tiles() {
    return '
    <script>
    jQuery(document).ready(function($) {
        const previewService = "https://image.thum.io/get/width/800/";
        const thumbnailContainers = document.querySelectorAll(".thumbnail-container");
        thumbnailContainers.forEach(function(container) {
            const url = container.getAttribute("data-url");
            if (!url) return;
            const img = new Image();
            img.onload = function() {
                container.innerHTML = "";
                container.appendChild(img);
            };
            img.onerror = function() {
                container.innerHTML = "<div style=\"padding: 10px; text-align: center; color: #fff;\">Impossible de charger la prévisualisation</div>";
            };
            img.src = previewService + url;
        });
    });
    </script>';
}

function get_external_links_from_content($content) {
    $links = array();
    $processed_urls = array();
    $site_host = parse_url(home_url(), PHP_URL_HOST);
    preg_match_all('/\[(.*?)\]\((https?:\/\/[^\)]+)\)/', $content, $md_matches, PREG_SET_ORDER);
    foreach ($md_matches as $match) {
        $url = $match[2];
        $link_host = parse_url($url, PHP_URL_HOST);
        if ($link_host && $link_host !== $site_host) {
            if (!in_array($url, $processed_urls)) {
                $links[] = array(
                    'url' => $url,
                    'title' => $match[1] ?: $link_host
                );
                $processed_urls[] = $url;
            }
        }
    }
    preg_match_all('/<a\s+(?:[^>]*?\s+)?href=([\"\'])(https?:\/\/[^\"\']+)\1[^>]*>(.*?)<\/a>/i', $content, $html_matches, PREG_SET_ORDER);
    foreach ($html_matches as $match) {
        $url = $match[2];
        $link_host = parse_url($url, PHP_URL_HOST);
        if ($link_host && $link_host !== $site_host) {
            if (!in_array($url, $processed_urls)) {
                $links[] = array(
                    'url' => $url,
                    'title' => strip_tags($match[3]) ?: $link_host
                );
                $processed_urls[] = $url;
            }
        }
    }
    preg_match_all('/(?<!\[|href=["\'])(?:https?:\/\/[^\s<>"\)\]]+)/', $content, $plain_matches);
    foreach ($plain_matches[0] as $url) {
        $link_host = parse_url($url, PHP_URL_HOST);
        if ($link_host && $link_host !== $site_host) {
            if (!in_array($url, $processed_urls)) {
                $links[] = array(
                    'url' => $url,
                    'title' => $link_host
                );
                $processed_urls[] = $url;
            }
        }
    }
    return $links;
} 