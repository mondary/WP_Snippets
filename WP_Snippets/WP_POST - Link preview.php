if (!defined('ABSPATH')) exit;

add_shortcode('preview_links', 'preview_links_shortcode');

function preview_links_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'style' => 'grid',
    ), $atts);

    if (empty($content)) {
        return '<p>Veuillez ajouter des liens à prévisualiser.</p>';
    }

    // Nettoyer balises automatiques WP sur le contenu du shortcode
    $content = shortcode_unautop(trim($content));

    // Extraire toutes les URLs dans le contenu via regex
    preg_match_all('/https?:\/\/[^\s<>\'"]+/', $content, $matches);
    $links = array_unique($matches[0]);

    if (empty($links)) {
        return '<p>Aucun lien externe trouvé.</p>';
    }

    $output = '';
    if ($atts['style'] === 'grid') {
        $output .= display_links_grid($links);
    }

    $output .= get_preview_styles($atts['style']);
    return $output;
}

function display_links_grid($links) {
    $output = '<div class="links-grid">';
    foreach ($links as $link) {
        $thumb_url = 'https://image.thum.io/get/width/150/' . $link;
        $display_url = preg_replace('#^https?://#', '', rtrim($link, '/'));

        $output .= '<a class="link-tile" href="' . esc_url($link) . '" target="_blank" rel="noopener noreferrer">';
        $output .= '<div class="screenshot-wrapper">';
        $output .= '<img class="screenshot-image" src="' . esc_url($thumb_url) . '" alt="Preview de ' . esc_attr($display_url) . '" loading="lazy" />';
        $output .= '<div class="overlay-text">' . esc_html($display_url) . '</div>';
        $output .= '</div>';
        $output .= '</a>';
    }
    $output .= '</div>';
    return $output;
}

function get_preview_styles($style) {
    if ($style === 'grid') {
        return '<style>
            .links-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 10px;
                padding: 10px;
            }

            .link-tile {
                display: block;
                border-radius: 8px;
                overflow: hidden;
                text-decoration: none;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .link-tile:hover {
                transform: translateY(-3px);
                box-shadow: 0 6px 10px rgba(0, 0, 0, 0.1);
            }

            .screenshot-wrapper {
                position: relative;
                width: 100%;
                aspect-ratio: 3 / 2;
            }

            .screenshot-image {
                width: 100%;
                height: 100%;
                object-fit: cover;
                object-position: top center;
                display: block;
            }

            .overlay-text {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                background: rgba(0, 0, 0, 0.6);
                color: #fff;
                font-size: 13px;
                font-weight: bold;
                padding: 4px 6px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
        </style>';
    }
    return '';
}
