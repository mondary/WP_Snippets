/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: WP_Snippets
 * Source path: WP_Snippets/WP_POST - Link preview.php
 * Display name: WP_POST - Link preview
 * Scope: front-end
 * Online snippet: non
 * Exact duplicate group: oui (f3dcaf4f51b0…, 2 membres)
 * Canonical exact group ID: 105
 * Version family: DUP WP_shortcote - preview 13 ✅ (1 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets/WP_POST - Link preview.php
 * Is family latest: oui
 * Archive reasons: exact-duplicate
 * Features: shortcode
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: preview_links_shortcode, display_links_grid, get_preview_styles
 * Shortcodes: preview_links
 * Lignes / octets (brut): 106 / 3281
 * Hash code normalise (sha256): f3dcaf4f51b02c7e640253557b6f3abdfa9ab826e7d78346ad29e0166e0e3fa2
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: shortcode-preview__v001__alt2.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/shortcode-preview__v001__alt2.php
 * Resume fonctionnalites: shortcode WordPress, UI frontend (CSS/HTML), 3 fonction(s) clef
 * Features detectees: shortcode, css-ui
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: preview_links_shortcode, display_links_grid, get_preview_styles
 * Shortcodes: preview_links
 * APIs WP detectees: add_shortcode, get_preview_styles
 * Signatures contenu: inline-style, html-markup
 * Lignes / octets: 130 / 4190
 * Empreinte code (sha256): 94dfe1c506b9cc829a089251b392e7c9dc31eb71dd0894d69b633aa93ce855e3
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: shortcode-preview__v001__alt2.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/shortcode-preview__v001__alt2.php
 * Bucket FINAL: archive
 * Statut: LOCAL
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
