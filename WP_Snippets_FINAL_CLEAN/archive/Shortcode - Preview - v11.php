/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/062__id-120__wp-shortcote-preview-11.php
 * Display name: WP_shortcote - preview 11
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 120
 * Online modified: 2025-05-13 16:18:46
 * Online revision: 3
 * Exact duplicate group: non
 * Version family: WP_shortcote - preview (11 variantes)
 * Version: v11
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/063__id-121__wp-shortcote-preview-12.php
 * Is family latest: non
 * Archive reasons: version-history-older
 * Features: shortcode
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: preview_links_shortcode, get_external_links_from_content, display_links_grid, get_preview_styles
 * Shortcodes: preview_links
 * Lignes / octets (brut): 122 / 3720
 * Hash code normalise (sha256): b5c8d9e67442f0ee662248aedbb856df1e36b8e6377a08ceb2c8f757127d0e1e
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: shortcode-preview__v011.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/shortcode-preview__v011.php
 * Resume fonctionnalites: shortcode WordPress, UI frontend (CSS/HTML), 4 fonction(s) clef
 * Features detectees: shortcode, css-ui
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: preview_links_shortcode, get_external_links_from_content, display_links_grid, get_preview_styles
 * Shortcodes: preview_links
 * APIs WP detectees: add_shortcode, get_external_links_from_content, get_preview_styles
 * Signatures contenu: inline-style, html-markup
 * Lignes / octets: 136 / 4406
 * Empreinte code (sha256): a5c97efa6ae5043d3a6f6b940fb62b8782d0d8379986aba98dc2191ebf5bc165
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: shortcode-preview__v011.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/shortcode-preview__v011.php
 * Bucket FINAL: archive
 * Statut: INACTIVE
 * Cluster principal: shortcode_preview
 * Clusters secondaires: aucun
 * Domaine: shortcode
 * Confiance: high
 * Scores (top): shortcode_preview=25, shortcode_other=2, frontend_ui_widget=2
 * Raisons principales: shortcode, preview_links, preview, shortcode-preview, shortcote
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

if (!defined('ABSPATH')) exit;

add_shortcode('preview_links', 'preview_links_shortcode');

function preview_links_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'style' => 'grid',
    ), $atts);

    if (empty($content)) {
        return '<p>Veuillez ajouter des liens à prévisualiser.</p>';
    }

    $content = do_shortcode($content);
    $links = get_external_links_from_content($content);

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

function get_external_links_from_content($content) {
    preg_match_all('/https?\:\/\/[a-zA-Z0-9\-\._~\/?#[\]@!$&\'()*+,;=]+/', $content, $matches);
    return $matches[0];
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
                grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
                gap: 10px;
                padding: 10px;
            }

            .link-tile {
                display: block;
                background: #fff;
                border-radius: 8px;
                overflow: hidden;
                text-decoration: none;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
                height: 140px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            }

            .link-tile:hover {
                transform: translateY(-3px);
                box-shadow: 0 6px 10px rgba(0, 0, 0, 0.1);
            }

            .screenshot-wrapper {
                position: relative;
                width: 100%;
                height: 100%;
            }

            .screenshot-image {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }

            .overlay-text {
                position: absolute;
                bottom: 6px;
                left: 6px;
                right: 6px;
                background: rgba(0, 0, 0, 0.6);
                color: #fff;
                font-size: 14px;
                font-weight: bold;
                padding: 4px 6px;
                border-radius: 4px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
        </style>';
    }
    return '';
}
