/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/056__id-114__wp-shortcote-preview-5.php
 * Display name: WP_shortcote - preview 5
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 114
 * Online modified: 2025-05-13 15:30:40
 * Online revision: 5
 * Exact duplicate group: non
 * Version family: WP_shortcote - preview (11 variantes)
 * Version: v5
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/063__id-121__wp-shortcote-preview-12.php
 * Is family latest: non
 * Archive reasons: version-history-older
 * Features: shortcode
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: preview_links_shortcode, get_external_links_from_content, display_links_grid, get_preview_styles
 * Shortcodes: preview_links
 * Lignes / octets (brut): 107 / 3185
 * Hash code normalise (sha256): 4d30144e1d65357fbebfb3dbf91f35c1cdb5bbe37d318a7baa114e9729465437
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: shortcode-preview__v005.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/shortcode-preview__v005.php
 * Resume fonctionnalites: shortcode WordPress, UI frontend (CSS/HTML), 4 fonction(s) clef
 * Features detectees: shortcode, css-ui
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: preview_links_shortcode, get_external_links_from_content, display_links_grid, get_preview_styles
 * Shortcodes: preview_links
 * APIs WP detectees: add_shortcode, get_external_links_from_content, get_preview_styles
 * Signatures contenu: inline-style, html-markup
 * Lignes / octets: 121 / 3869
 * Empreinte code (sha256): cd4fb4b6d4f34fb476d152c1362a392ab6cf681225390c059a853ad8c74a09d2
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: shortcode-preview__v005.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/shortcode-preview__v005.php
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
        $output .= '<a class="link-tile" href="' . esc_url($link) . '" target="_blank" rel="noopener noreferrer">';
        $output .= '<div class="screenshot-placeholder"></div>';
        $output .= '<div class="link-footer">' . esc_html($link) . '</div>';
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
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
                gap: 20px;
                padding: 20px;
            }

            .link-tile {
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                background: #ffffff;
                border-radius: 12px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
                text-decoration: none;
                overflow: hidden;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
                height: 200px;
            }

            .link-tile:hover {
                transform: translateY(-4px);
                box-shadow: 0 8px 12px rgba(0, 0, 0, 0.1);
            }

            .screenshot-placeholder {
                flex: 1;
                background-color: #f3f4f6;
                border-bottom: 1px solid #e5e7eb;
            }

            .link-footer {
                background: #f9fafb;
                padding: 10px;
                text-align: center;
                border-top: 1px solid #e5e7eb;
                color: #111827;
                font-size: 14px;
                font-weight: 500;
                word-break: break-word;
            }
        </style>';
    }
    return '';
}
