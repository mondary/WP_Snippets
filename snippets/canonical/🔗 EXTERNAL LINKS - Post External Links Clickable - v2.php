/* CLM-CREATED-AT: 2026-02-25 */
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/active/front-end/051__id-109__post-external-links-clickable.php
 * Display name: POST - External links clickable
 * Scope: front-end
 * Online snippet: oui
 * Online active: oui
 * Online ID: 109
 * Online modified: 2025-05-13 09:55:02
 * Online revision: 4
 * Exact duplicate group: oui (64905f21b650…, 2 membres)
 * Canonical exact group ID: 80
 * Version family: DUP POST - External links clickable (1 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets_Online_Current/active/front-end/051__id-109__post-external-links-clickable.php
 * Is family latest: oui
 * Canonical reasons: exact-group-canonical, protected-online-active
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: the_content
 * Fonctions clefs: rendre_liens_https_cliquables
 * Lignes / octets (brut): 43 / 1696
 * Hash code normalise (sha256): 64905f21b650579cd3c740603f47131e100e236d162da0c80156ca9424097bee
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: ACTIVE__front-end__post-external-links-clickable__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__front-end__post-external-links-clickable__v2__src-wp_snippets_online_current.php
 * Resume fonctionnalites: 1 hook(s) WP, 1 fonction(s) clef
 * Features detectees: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: the_content
 * Fonctions clefs: rendre_liens_https_cliquables
 * APIs WP detectees: add_filter, is_singular
 * Signatures contenu: html-markup
 * Lignes / octets: 57 / 2408
 * Empreinte code (sha256): 2751c41288e3839f5478acfd6538d3d17232805586a80ea5775fbeac721ef957
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: ACTIVE__front-end__post-external-links-clickable__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__front-end__post-external-links-clickable__v2__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: ACTIVE
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
