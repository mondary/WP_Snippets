/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/006__id-11__post-smart-hashtags.php
 * Display name: POST - Smart hashtags 🔴
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 11
 * Online modified: 2025-01-23 16:37:19
 * Online revision: 4
 * Exact duplicate group: oui (a70ed25e26e3…, 3 membres)
 * Canonical exact group ID: 121
 * Version family: DUP POST - Smart hashtags 🔴 (1 variantes)
 * Version: v3
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/006__id-11__post-smart-hashtags.php
 * Is family latest: oui
 * Canonical reasons: exact-group-canonical
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: the_content
 * Fonctions clefs: convert_hashtags_to_links
 * Lignes / octets (brut): 38 / 1460
 * Hash code normalise (sha256): a70ed25e26e3fedeafe3550ea82c38dcd2021c70bc84cd1f3147c5ee146c084a
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: INACTIVE__global__post-smart-hashtags__v3__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__post-smart-hashtags__v3__src-wp_snippets_online_current.php
 * Resume fonctionnalites: 1 hook(s) WP, 1 fonction(s) clef
 * Features detectees: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: the_content
 * Fonctions clefs: convert_hashtags_to_links
 * APIs WP detectees: add_filter
 * Signatures contenu: html-markup
 * Lignes / octets: 51 / 2009
 * Empreinte code (sha256): cb378dcdc8014cb837c99156a6f1cfca8d5600da27d757f7a6ec1ef7602a4b16
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: INACTIVE__global__post-smart-hashtags__v3__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__post-smart-hashtags__v3__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: INACTIVE
 * Cluster principal: taxonomy_tags
 * Clusters secondaires: aucun
 * Domaine: post-front
 * Confiance: low
 * Scores (top): taxonomy_tags=5
 * Raisons principales: tags
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

function convert_hashtags_to_links($content) {
    // Sauvegarder temporairement les balises HTML
    $placeholders = array();
    $content = preg_replace_callback('/<[^>]+>/', function($matches) use (&$placeholders) {
        $placeholder = '{{HTML_TAG_' . count($placeholders) . '}}';
        $placeholders[] = $matches[0];
        return $placeholder;
    }, $content);

    // Convertir les hashtags (commencent par #)
    $content = preg_replace_callback('/#(\w+)/', function($matches) {
        return '<a href="https://mondary.design/tag/' . strtolower($matches[1]) . '" target="_blank">#' . $matches[1] . '</a>';
    }, $content);

    // Restaurer les balises HTML
    foreach ($placeholders as $index => $tag) {
        $content = str_replace('{{HTML_TAG_' . $index . '}}', $tag, $content);
    }

    return $content;
}

// Appliquer le filtre sur le contenu des articles avant l'affichage
add_filter('the_content', 'convert_hashtags_to_links');