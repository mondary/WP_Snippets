/**
 * Affiche le temps de lecture estimé d'un article.
 *
 * Calcule le temps de lecture en fonction du nombre de mots et d'une vitesse de lecture par défaut.
 * Affiche ensuite le temps de lecture estimé dans un paragraphe.
 */
 
 $reading_speed = 200; // 200 words per minute
$content       = get_post_field( 'post_content', get_the_id() );
$word_count    = str_word_count( strip_tags( $content ) );
$reading_time  = ceil( $word_count / $reading_speed );

echo '<p>Temps de lecture estimé : ' . absint( $reading_time ) . ' ' . _n( 'minute', 'minutes', $reading_time ) . '</p>';