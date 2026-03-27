/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: WP_Snippets
 * Source path: WP_Snippets/WP_POST - External links clickable.php
 * Display name: WP_POST - External links clickable
 * Scope: front-end
 * Online snippet: non
 * Exact duplicate group: oui (64905f21b650…, 2 membres)
 * Canonical exact group ID: 80
 * Version family: DUP POST - External links clickable (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets/WP_POST - External links clickable.php
 * Is family latest: oui
 * Archive reasons: exact-duplicate
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: the_content
 * Fonctions clefs: rendre_liens_https_cliquables
 * Lignes / octets (brut): 30 / 1289
 * Hash code normalise (sha256): 64905f21b650579cd3c740603f47131e100e236d162da0c80156ca9424097bee
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: post-external-links-clickable__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-external-links-clickable__v001.php
 * Resume fonctionnalites: 1 hook(s) WP, 1 fonction(s) clef
 * Features detectees: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: the_content
 * Fonctions clefs: rendre_liens_https_cliquables
 * APIs WP detectees: add_filter, is_singular
 * Signatures contenu: html-markup
 * Lignes / octets: 53 / 2179
 * Empreinte code (sha256): 9efd75855e98541c39518ba5886eb9689038a78a652ad8f7cac25e0e7f97044d
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: post-external-links-clickable__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-external-links-clickable__v001.php
 * Bucket FINAL: archive
 * Statut: LOCAL
 * Cluster principal: links_external
 * Clusters secondaires: aucun
 * Domaine: post-front
 * Confiance: high
 * Scores (top): links_external=10
 * Raisons principales: external links, clickable
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

add_filter('the_content', 'rendre_liens_https_cliquables');

function rendre_liens_https_cliquables($content) {
    if (is_singular(['post', 'page'])) {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        // Encodage pour supporter les caractères spéciaux
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $xpath = new DOMXPath($dom);
        foreach ($xpath->query('//text()') as $textNode) {
            if (strpos($textNode->nodeValue, 'https://') !== false) {
                $newHtml = preg_replace(
                    '/(?<!href=["\'])\b(https:\/\/[^\s<>"\']+)/i',
                    '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>',
                    $textNode->nodeValue
                );
                if ($newHtml !== $textNode->nodeValue) {
                    $fragment = $dom->createDocumentFragment();
                    $fragment->appendXML($newHtml);
                    $textNode->parentNode->replaceChild($fragment, $textNode);
                }
            }
        }
        $content = $dom->saveHTML();
        // Nettoyage de l'encodage ajouté
        $content = preg_replace('/^<\?xml.+?\?>/', '', $content);
    }
    return $content;
}
