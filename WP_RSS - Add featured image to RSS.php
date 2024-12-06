// Permet d'ajouter les features images dans le flux RSS, a combiner avec l'extension RSS Feed Styles


function featuredtoRSS($content) {
    global $post;

    if (has_post_thumbnail($post->ID)) {
        // Utiliser 'full' pour la taille de l'image
        $content = '<div style="width: 100%;"><div>' . get_the_post_thumbnail($post->ID, 'full', array('style' => 'width: 100%; height: auto; margin-bottom: 15px;')) . '</div></div>' . $content;
    }

    return $content;
}

add_filter('the_excerpt_rss', 'featuredtoRSS');
add_filter('the_content_feed', 'featuredtoRSS');