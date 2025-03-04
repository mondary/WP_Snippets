<?php
/* 
Title: Custom Gradient Footer
Description: Adds a custom footer with gradient effect and large text
Code Type: Universal Footer
*/

// Add the CSS styles
add_action('wp_head', function() {
    ?>
    <style>
        .custom-footer {
            padding: 0;
            text-align: center;
            background: linear-gradient(to top, rgba(255,255,255,1) 0%, rgba(255,255,255,0) 100%);
            position: fixed;
            bottom: 0;
            width: 100%;
            height: 100px;
            overflow: hidden;
            position: relative;
        }

        .footer-mask {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to top, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0) 100%);
            z-index: 1;
        }

        .footer-text {
            color:rgb(168, 164, 164);
            font-size: 80px;
            font-weight: bold;
            letter-spacing: 2px;
            position: absolute;
            bottom: -40px;
            width: 100%;
            opacity: 0.5;
        }
    </style>
    <?php
});

// Add the footer HTML
function custom_gradient_footer() {
    ?>
    <footer class="custom-footer">
        <div class="footer-mask"></div>
        <div class="footer-text">© 2025 Clement MONDARY</div>
    </footer>
    <?php
}

// Hook the footer to display at the bottom of the page
add_action('wp_footer', 'custom_gradient_footer');