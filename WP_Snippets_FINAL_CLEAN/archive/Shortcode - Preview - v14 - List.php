/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/066__id-124__wp-shortcode-preview14-list.php
 * Display name: WP_Shortcode_preview14 (list)
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 124
 * Online modified: 2025-06-04 11:57:02
 * Online revision: 2
 * Exact duplicate group: non
 * Version family: WP_Shortcode_preview14 (list) (1 variantes)
 * Version: v14
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/066__id-124__wp-shortcode-preview14-list.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: shortcode
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: preview_links_shortcode, get_external_links_from_content, display_links_banner, get_preview_styles
 * Shortcodes: preview_links
 * Lignes / octets (brut): 156 / 5060
 * Hash code normalise (sha256): 0c0ac4c68e7bc16449cc7834d53187194695090a673e63aafeeff66d6e1ea841
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: shortcode-preview14-list__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/shortcode-preview14-list__v001.php
 * Resume fonctionnalites: shortcode WordPress, UI frontend (CSS/HTML), 4 fonction(s) clef
 * Features detectees: shortcode, css-ui
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: preview_links_shortcode, get_external_links_from_content, display_links_banner, get_preview_styles
 * Shortcodes: preview_links
 * APIs WP detectees: add_shortcode, get_external_links_from_content, get_preview_styles
 * Signatures contenu: inline-style, html-markup
 * Lignes / octets: 170 / 5755
 * Empreinte code (sha256): 252fb712122f630c3c6eb8b13a32b793de7c04d23a4494fce7a2257837375263
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: shortcode-preview14-list__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/shortcode-preview14-list__v001.php
 * Bucket FINAL: archive
 * Statut: INACTIVE
 * Cluster principal: shortcode_preview
 * Clusters secondaires: aucun
 * Domaine: shortcode
 * Confiance: high
 * Scores (top): shortcode_preview=20, shortcode_other=2, frontend_ui_widget=2
 * Raisons principales: shortcode, preview_links, preview, shortcode-preview
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

if (!defined('ABSPATH')) exit;

add_shortcode('preview_links', 'preview_links_shortcode');

function preview_links_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'style' => 'banner', // style par défaut changé en banner
    ), $atts);

    if (empty($content)) {
        return '<p>Veuillez ajouter des liens à prévisualiser.</p>';
    }

    $content = trim($content);
    $links = get_external_links_from_content($content);

    if (empty($links)) {
        return '<p>Aucun lien externe trouvé.</p>';
    }

    $output = '';
    if ($atts['style'] === 'banner') {
        $output .= display_links_banner($links);
    } elseif ($atts['style'] === 'grid') {
        $output .= display_links_grid($links);
    }

    $output .= get_preview_styles($atts['style']);
    return $output;
}

function get_external_links_from_content($content) {
    preg_match_all('/https?\:\/\/[a-zA-Z0-9\-\._~\/?#[\]@!$&\'()*+,;=]+/', $content, $matches);
    return array_unique($matches[0]); // enlever doublons si besoin
}

function display_links_banner($links) {
    $output = '';
    foreach ($links as $link) {
        $thumb_url = 'https://image.thum.io/get/width/300/' . $link;
        $display_url = preg_replace('#^https?://#', '', rtrim($link, '/'));

        $output .= '<div class="banner-link">';
        $output .= '<a href="' . esc_url($link) . '" target="_blank" rel="noopener noreferrer">';
        $output .= '<img src="' . esc_url($thumb_url) . '" alt="Preview de ' . esc_attr($display_url) . '" loading="lazy" class="banner-image" />';
        $output .= '<div class="banner-text">' . esc_html($display_url) . '</div>';
        $output .= '</a>';
        $output .= '</div>';
    }
    return $output;
}

function get_preview_styles($style) {
    if ($style === 'banner') {
        return '<style>
            .banner-link {
                background: #f0f4f8;
                border-left: 5px solid #0073aa;
                margin: 12px 0;
                padding: 12px 16px;
                border-radius: 6px;
                display: flex;
                align-items: center;
                box-shadow: 0 2px 6px rgb(0 0 0 / 0.1);
                transition: background-color 0.2s ease;
            }
            .banner-link:hover {
                background: #dbe9f9;
            }
            .banner-link a {
                display: flex;
                align-items: center;
                text-decoration: none;
                color: inherit;
                width: 100%;
            }
            .banner-image {
                width: 120px;
                height: 80px;
                object-fit: cover;
                border-radius: 4px;
                margin-right: 16px;
                flex-shrink: 0;
                box-shadow: 0 1px 3px rgb(0 0 0 / 0.2);
            }
            .banner-text {
                font-weight: 600;
                font-size: 16px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
        </style>';
    } elseif ($style === 'grid') {
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
