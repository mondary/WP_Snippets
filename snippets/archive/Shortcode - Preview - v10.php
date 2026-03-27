/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/061__id-119__wp-shortcote-preview-10.php
 * Display name: WP_shortcote - preview 10
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 119
 * Online modified: 2025-05-13 16:17:20
 * Online revision: 9
 * Exact duplicate group: non
 * Version family: WP_shortcote - preview (11 variantes)
 * Version: v10
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/063__id-121__wp-shortcote-preview-12.php
 * Is family latest: non
 * Archive reasons: version-history-older
 * Features: shortcode
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: preview_links_shortcode, get_external_links_from_content, display_links_grid, get_preview_styles
 * Shortcodes: preview_links
 * Lignes / octets (brut): 115 / 3519
 * Hash code normalise (sha256): 31123d6275edc6e7f249c0cb72673afe2a50d5fd280d0e5c278c4f4b8a7fd15f
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: shortcode-preview__v010.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/shortcode-preview__v010.php
 * Resume fonctionnalites: shortcode WordPress, UI frontend (CSS/HTML), 4 fonction(s) clef
 * Features detectees: shortcode, css-ui
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: preview_links_shortcode, get_external_links_from_content, display_links_grid, get_preview_styles
 * Shortcodes: preview_links
 * APIs WP detectees: add_shortcode, get_external_links_from_content, get_preview_styles
 * Signatures contenu: inline-style, html-markup
 * Lignes / octets: 129 / 4205
 * Empreinte code (sha256): 31d7b04d92bf8db5bca497ca45e8ce80584a59605e7a59e59d359c120fffb2cb
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: shortcode-preview__v010.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/shortcode-preview__v010.php
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
        $output .= '<img class="screenshot-image" src="' . esc_url($thumb_url) . '" alt="Preview de ' . esc_attr($display_url) . '" loading="lazy" />';
        $output .= '<div class="link-info">' . esc_html($display_url) . '</div>';
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
                display: flex;
                flex-direction: column;
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
                text-decoration: none;
                overflow: hidden;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
                height: 150px;
            }

            .link-tile:hover {
                transform: translateY(-4px);
                box-shadow: 0 6px 10px rgba(0, 0, 0, 0.1);
            }

            .screenshot-image {
                width: 100%;
                height: 100px;
                object-fit: cover;
                border: none;
                display: block;
            }

            .link-info {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 0 6px;
                color: #111827;
                font-size: 12px;
                font-weight: bold;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                line-height: 1;
            }
        </style>';
    }
    return '';
}
