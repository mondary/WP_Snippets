/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: A TRIER
 * Source path: A TRIER/WP_POST smart hashtags/WP_POST Smart hashtags.php
 * Display name: WP_POST Smart hashtags
 * Scope: front-end
 * Online snippet: non
 * Exact duplicate group: oui (a70ed25e26e3…, 3 membres)
 * Canonical exact group ID: 121
 * Version family: DUP POST - Smart hashtags 🔴 (1 variantes)
 * Version: v1
 * Recommended latest in family: A TRIER/WP_POST smart hashtags/WP_POST Smart hashtags.php
 * Is family latest: oui
 * Archive reasons: exact-duplicate
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: the_content
 * Fonctions clefs: convert_hashtags_to_links
 * Lignes / octets (brut): 24 / 956
 * Hash code normalise (sha256): a70ed25e26e3fedeafe3550ea82c38dcd2021c70bc84cd1f3147c5ee146c084a
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: post-smart-hashtags__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-smart-hashtags__v001.php
 * Resume fonctionnalites: 1 hook(s) WP, 1 fonction(s) clef
 * Features detectees: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: the_content
 * Fonctions clefs: convert_hashtags_to_links
 * APIs WP detectees: add_filter
 * Signatures contenu: html-markup
 * Lignes / octets: 47 / 1835
 * Empreinte code (sha256): 95104bb3e731648ab5583bcdf6444f225e1d4d338ea08d6fa664d41c6ea60e49
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: post-smart-hashtags__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-smart-hashtags__v001.php
 * Bucket FINAL: archive
 * Statut: LOCAL
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