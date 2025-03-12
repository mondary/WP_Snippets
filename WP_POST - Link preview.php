
/*
 * Plugin Name: External Links Preview
 * Description: Affiche les liens externes avec des prévisualisations favicons
 * Version: 1.0
 */

if (!defined('ABSPATH')) exit;

add_shortcode('preview_links', 'preview_links_shortcode');

function preview_links_shortcode($atts, $content = null) {
    // Si pas de contenu, afficher un message
    if (empty($content)) {
        return '<p>Veuillez ajouter des liens à prévisualiser.</p>';
    }
    
    // Traiter le contenu
    $content = do_shortcode($content);
    
    // Extraire les liens
    $links = get_external_links_from_content($content);
    
    if (empty($links)) {
        return '<p>Aucun lien externe trouvé.</p>';
    }
    
    // Construire l'affichage des liens
    $output = '<div class="external-links-grid">';
    foreach ($links as $link) {
        $output .= display_link_preview($link);
    }
    $output .= '</div>';
    
    return $output . get_preview_styles();
}

function get_external_links_from_content($content) {
    $links = array();
    $processed_urls = array();
    $site_host = parse_url(home_url(), PHP_URL_HOST);
    
    // Recherche des liens Markdown [texte](url)
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
    
    // Recherche des liens HTML <a href="url">texte</a>
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
    
    // Recherche des URLs simples dans le texte
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

function display_link_preview($link) {
    $url = esc_url($link['url']);
    $domain = parse_url($url, PHP_URL_HOST);
    $favicon = "https://www.google.com/s2/favicons?domain={$domain}&sz=64";
    $small_favicon = "https://www.google.com/s2/favicons?domain={$domain}&sz=16";
    $color = get_color_from_domain($domain);
    
    return '<a href="' . $url . '" class="external-link-preview" target="_blank" rel="noopener" style="background-color: '.$color.';">
        <div class="preview-image">
            <img src="' . esc_url($favicon) . '" alt="" loading="lazy">
        </div>
        <div class="preview-domain">
            <img src="' . esc_url($small_favicon) . '" class="domain-favicon" alt="">
            <span>' . esc_html($domain) . '</span>
        </div>
    </a>';
}

function get_color_from_domain($domain) {
    $favicon_url = "https://www.google.com/s2/favicons?domain={$domain}&sz=64";
    $cache_key = 'fav_color_' . md5($domain);
    $cached_color = get_transient($cache_key);

    if ($cached_color) {
        return $cached_color;
    }

    // Télécharger le favicon
    $image_data = @file_get_contents($favicon_url);
    if (!$image_data) {
        return '#f0f0f0';
    }

    // Analyser la couleur dominante
    $image = @imagecreatefromstring($image_data);
    if (!$image) {
        return '#f0f0f0';
    }

    // Réduire l'image pour obtenir la couleur moyenne
    $resized = imagecreatetruecolor(1, 1);
    imagecopyresampled($resized, $image, 0, 0, 0, 0, 1, 1, imagesx($image), imagesy($image));
    $rgb = imagecolorat($resized, 0, 0);

    $r = ($rgb >> 16) & 0xFF;
    $g = ($rgb >> 8) & 0xFF;
    $b = $rgb & 0xFF;

    // Conversion RGB vers HSL
    $hsl = rgb_to_hsl($r, $g, $b);
    $color = "hsl({$hsl[0]}, {$hsl[1]}%, {$hsl[2]}%)";

    // Mettre en cache pour 1 semaine
    set_transient($cache_key, $color, WEEK_IN_SECONDS);

    return $color;
}

function rgb_to_hsl($r, $g, $b) {
    $r /= 255;
    $g /= 255;
    $b /= 255;

    $max = max($r, $g, $b);
    $min = min($r, $g, $b);
    $h = $s = $l = ($max + $min) / 2;

    if ($max !== $min) {
        $d = $max - $min;
        $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);
        switch ($max) {
            case $r:
                $h = ($g - $b) / $d + ($g < $b ? 6 : 0);
                break;
            case $g:
                $h = ($b - $r) / $d + 2;
                break;
            case $b:
                $h = ($r - $g) / $d + 4;
                break;
        }
        $h /= 6;
    }

    return [
        round($h * 360),
        round($s * 100),
        round($l * 80) // Réduire la luminosité pour meilleur contraste
    ];
}

function get_preview_styles() {
    return '
    <style>
    .external-links-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 12px;
        margin: 15px 0;
    }
    .external-link-preview {
        display: block;
        text-decoration: none;
        color: inherit;
        /* Removed background: white */
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        transition: all 0.25s ease;
        height: 100%;
    }
    .external-link-preview:hover {
        transform: translateY(-2px);
        box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    }
    .preview-image {
        height: 100px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        border-bottom: 1px solid #f0f0f0;
    }
    .preview-image img {
        width: 48px;
        height: 48px;
        transition: transform 0.3s ease;
    }
    .external-link-preview:hover .preview-image img {
        transform: scale(1.1);
    }
    .preview-domain {
        padding: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }
    .domain-favicon {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
    }
    .preview-domain span {
        color: #fff;
        font-size: 13px;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-shadow: 0 1px 2px rgba(0,0,0,0.3);
    }
    </style>';
}
