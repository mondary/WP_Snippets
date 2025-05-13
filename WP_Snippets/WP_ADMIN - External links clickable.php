add_filter('the_content', 'rendre_liens_https_cliquables');

function rendre_liens_https_cliquables($content) {
    // Vérifie que l'on est bien dans une page ou un article WordPress
    if (is_singular(['post', 'page'])) {
        // Expression régulière pour détecter les URL commençant par https://
        $pattern = '/(?<!href=["\'])\b(https:\/\/[^\s<>"\']+)/i';
        $replacement = '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>';
        $content = preg_replace($pattern, $replacement, $content);
    }
    return $content;
}
