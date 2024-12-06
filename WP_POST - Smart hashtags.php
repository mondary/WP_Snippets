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