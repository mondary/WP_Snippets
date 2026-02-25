/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/054__id-112__wp-shortcote-preview-3.php
 * Display name: WP_shortcote - preview 3
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 112
 * Online modified: 2025-05-13 14:59:12
 * Online revision: 6
 * Exact duplicate group: non
 * Version family: WP_shortcote - preview (11 variantes)
 * Version: v3
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/063__id-121__wp-shortcote-preview-12.php
 * Is family latest: non
 * Archive reasons: version-history-older
 * Features: shortcode
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: preview_links_shortcode, get_external_links_from_content, display_links_grid, get_preview_styles
 * Shortcodes: preview_links
 * Lignes / octets (brut): 109 / 3456
 * Hash code normalise (sha256): bf7f576bc7d6cecc9f33cffd627ee3bd157782ffeade31f8b5f79fa7fb16ad01
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: shortcode-preview__v003.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/shortcode-preview__v003.php
 * Resume fonctionnalites: shortcode WordPress, UI frontend (CSS/HTML), 4 fonction(s) clef
 * Features detectees: shortcode, css-ui
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: preview_links_shortcode, get_external_links_from_content, display_links_grid, get_preview_styles
 * Shortcodes: preview_links
 * APIs WP detectees: add_shortcode, get_external_links_from_content, get_preview_styles
 * Signatures contenu: inline-style, html-markup
 * Lignes / octets: 123 / 4140
 * Empreinte code (sha256): 331fdf224d0b666cc9bf7fd158cd873e620ee3326c2ffa8d480dbeca3bd9a97e
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: shortcode-preview__v003.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/shortcode-preview__v003.php
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
        'style' => 'grid',  // Style d'affichage: 'grid' uniquement
    ), $atts);
    
    $html_output = ''; // Variable pour stocker le HTML généré

    // Gérer le cas normal (pas d'exemples)
    if (empty($content)) {
        return '<p>Veuillez ajouter des liens à prévisualiser.</p>';
    }

    $content = do_shortcode($content);
    $links = get_external_links_from_content($content);

    if (empty($links)) {
        return '<p>Aucun lien externe trouvé.</p>';
    }

    // Construire l'affichage des liens en mode 'grid'
    if ($atts['style'] === 'grid') {
        $html_output = display_links_grid($links);
    }

    // Ajouter les styles CSS à la fin (le style grid est par défaut)
    $html_output .= get_preview_styles($atts['style']);
    
    return $html_output;
}

function get_external_links_from_content($content) {
    // Récupère les liens externes du contenu
    preg_match_all('/https?\:\/\/[a-zA-Z0-9\-\._~\/?#[\]@!$&\'()*+,;=]+/', $content, $matches);
    return $matches[0];
}

function display_links_grid($links) {
    // Affiche les liens sous forme de grille avec style ShadCN
    $output = '<div class="links-grid">';
    foreach ($links as $link) {
        $output .= '<div class="link-tile">';
        $output .= '<a href="' . esc_url($link) . '" target="_blank" rel="noopener noreferrer">' . esc_html($link) . '</a>';
        $output .= '</div>';
    }
    $output .= '</div>';
    return $output;
}

function get_preview_styles($style) {
    // Retourne les styles CSS pour la grille avec le style ShadCN
    if ($style === 'grid') {
        return '<style>
            .links-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 16px;
                padding: 20px;
            }
            .link-tile {
                background: #ffffff;
                padding: 20px;
                border-radius: 12px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
                text-align: center;
                cursor: pointer;
                overflow: hidden;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 150px;
            }
            .link-tile a {
                color: #333;
                text-decoration: none;
                font-weight: 600;
                font-size: 16px;
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            .link-tile a:hover {
                color: #0073e6;
                text-decoration: underline;
            }
            .link-tile:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            }
        </style>';
    }
    return '';
}
