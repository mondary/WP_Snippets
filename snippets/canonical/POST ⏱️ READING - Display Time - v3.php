<?php
/*
 * POST ⏱️ READING - Display Time - v3
 * Affiche « ⏱️ Temps de lecture estimé » en tête des articles.
 */

add_filter( 'the_content', 'clm_reading_time_display', 20 );

function clm_reading_time_display( $content ) {
    if ( ! is_singular( 'post' ) || is_feed() ) {
        return $content;
    }

    $reading_speed = 200; // 200 words per minute
    $post_content  = get_post_field( 'post_content', get_the_id() );
    $word_count    = str_word_count( strip_tags( $post_content ) );

    if ( $word_count < 1 ) {
        return $content;
    }

    $reading_time = (int) ceil( $word_count / $reading_speed );

    return '<p class="clm-reading-time" style="opacity:.75;font-size:.95em;">⏱️ Temps de lecture estimé : '
        . absint( $reading_time ) . ' ' . _n( 'minute', 'minutes', $reading_time ) . '</p>'
        . $content;
}
