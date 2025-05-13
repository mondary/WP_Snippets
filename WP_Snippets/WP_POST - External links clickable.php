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
