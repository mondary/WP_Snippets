<?php
/*
 * FRONTEND 📊 TRACKING - Umami - v1
 * Injection du script Umami self-hosté dans wp_head.
 */

add_action( 'wp_head', function () {
    echo '<script data-no-optimize="1" data-no-defer="1" defer src="https://mondary.design/umami/script.js" data-website-id="56ca5f24-5fe0-4450-aae8-ea15d33a83e7" data-domains="mondary.design,www.mondary.design"></script>' . "\n";
}, 1 );
