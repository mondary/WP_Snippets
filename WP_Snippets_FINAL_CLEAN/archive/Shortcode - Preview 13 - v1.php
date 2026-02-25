/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/045__id-102__wp-shortcode-preview.php
 * Display name: WP_Shortcode_preview
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 102
 * Online modified: 2025-05-13 13:37:01
 * Online revision: 12
 * Exact duplicate group: non
 * Version family: WP_Shortcode_preview (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/045__id-102__wp-shortcode-preview.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: shortcode
 * Dependances probables: jQuery
 * Hooks WP: aucun
 * Fonctions clefs: preview_links_shortcode
 * Shortcodes: preview_links
 * Lignes / octets (brut): 73 / 2826
 * Hash code normalise (sha256): 08239e166c15215a31e9bca96af2f417558baa329f70a5d0c08c49bf477c77b0
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: shortcode-preview__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/shortcode-preview__v001.php
 * Resume fonctionnalites: shortcode WordPress, 1 fonction(s) clef
 * Features detectees: shortcode
 * Dependances probables: jQuery
 * Hooks WP: aucun
 * Fonctions clefs: preview_links_shortcode
 * Shortcodes: preview_links
 * APIs WP detectees: add_shortcode, wp_enqueue_script, get_thumbnail_script, get_external_links_from_content, get_preview_styles
 * Signatures contenu: html-markup
 * Lignes / octets: 87 / 3408
 * Empreinte code (sha256): 85a1a031be0930405b96ccca7ef0569c83566450df9d8d4630c8f93f7257d688
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: shortcode-preview__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/shortcode-preview__v001.php
 * Bucket FINAL: archive
 * Statut: INACTIVE
 * Cluster principal: shortcode_preview
 * Clusters secondaires: aucun
 * Domaine: shortcode
 * Confiance: high
 * Scores (top): shortcode_preview=20, shortcode_other=2
 * Raisons principales: shortcode, preview_links, preview, shortcode-preview
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

if (!defined('ABSPATH')) exit;

add_shortcode('preview_links', 'preview_links_shortcode');

function preview_links_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'thumbnail' => 'false', // Option pour activer les miniatures
        'examples'  => 'false', // Option pour afficher des exemples
        'style'     => 'grid',  // Style d'affichage: 'grid' ou 'list'
    ), $atts);
    
    $html_output = ''; // Variable pour stocker le HTML généré

    // Gérer le cas des exemples
    if ($atts['examples'] === 'true') {
        // Note: display_example_links sera modifié pour ne plus ajouter le script ou jQuery lui-même
        $html_output = display_example_links($atts['thumbnail'] === 'true', $atts['style']);
        
        // Si les exemples doivent afficher des miniatures, on prépare le chargement du script
        if ($atts['thumbnail'] === 'true') {
            wp_enqueue_script('jquery'); // S'assurer que jQuery est chargé
            $html_output .= get_thumbnail_script(); // Ajouter le script des miniatures
        }
    } 
    // Gérer le cas normal (pas les exemples)
    else {
        if (empty($content)) {
            return '<p>Veuillez ajouter des liens à prévisualiser.</p>';
        }
        
        $content = do_shortcode($content);
        $links = get_external_links_from_content($content);
        
        if (empty($links)) {
            return '<p>Aucun lien externe trouvé.</p>';
        }
        
        // Construire l'affichage des liens
        if ($atts['style'] === 'list') {
            $html_output = display_links_list($links, $atts['thumbnail'] === 'true');
            // Si la liste doit afficher des miniatures, on prépare le chargement du script
            if ($atts['thumbnail'] === 'true' && !empty($links)) {
                wp_enqueue_script('jquery');
                $html_output .= get_thumbnail_script();
            }
        } else { // Style 'grid'
            $html_output = display_links_grid($links, $atts['thumbnail'] === 'true');
            // Le style grille utilise toujours les miniatures si des liens existent
            if (!empty($links)) {
                wp_enqueue_script('jquery');
                $html_output .= get_thumbnail_script();
            }
        }
    }
    
    // Ajouter les styles CSS à la fin
    $html_output .= get_preview_styles($atts['style']);
    
    return $html_output;
}