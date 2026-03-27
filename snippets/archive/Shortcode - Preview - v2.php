/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/front-end/053__id-111__wp-shortcote-preview-2.php
 * Display name: WP_shortcote - preview 2
 * Scope: front-end
 * Online snippet: oui
 * Online active: non
 * Online ID: 111
 * Online modified: 2025-05-13 16:23:53
 * Online revision: 40
 * Exact duplicate group: non
 * Version family: WP_shortcote - preview (11 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/063__id-121__wp-shortcote-preview-12.php
 * Is family latest: non
 * Archive reasons: version-history-older
 * Features: shortcode
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: preview_links_shortcode, get_external_links_from_content, display_links_grid, get_preview_styles
 * Shortcodes: preview_links
 * Lignes / octets (brut): 95 / 2894
 * Hash code normalise (sha256): e7de521c26e0978e9aa9a443f068ad2d5a2804f611cfae05cd9fbd43da2d8fc8
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: shortcode-preview__v002.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/shortcode-preview__v002.php
 * Resume fonctionnalites: shortcode WordPress, UI frontend (CSS/HTML), 4 fonction(s) clef
 * Features detectees: shortcode, css-ui
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: preview_links_shortcode, get_external_links_from_content, display_links_grid, get_preview_styles
 * Shortcodes: preview_links
 * APIs WP detectees: add_shortcode, get_external_links_from_content, get_preview_styles
 * Signatures contenu: inline-style, html-markup
 * Lignes / octets: 109 / 3580
 * Empreinte code (sha256): cf96ba4c0c8294a9460f317017e3d02195307f3a4f658458699efeb43fdc0df9
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: shortcode-preview__v002.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/shortcode-preview__v002.php
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
    // Affiche les liens sous forme de grille
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
    // Retourne les styles CSS pour la grille
    if ($style === 'grid') {
        return '<style>
            .links-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 16px;
                padding: 10px;
            }
            .link-tile {
                background: #f0f0f0;
                padding: 15px;
                border-radius: 8px;
                box-shadow: 0 2px 6px rgba(0,0,0,0.1);
                text-align: center;
            }
            .link-tile a {
                color: #333;
                text-decoration: none;
                font-weight: bold;
                display: block;
            }
            .link-tile a:hover {
                color: #0073e6;
            }
        </style>';
    }
    return '';
}
