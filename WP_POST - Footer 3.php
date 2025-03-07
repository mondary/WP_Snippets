<?php
/* 
Title: Combined Footer with Social Links and Copyright
Description: Combines social links and gradient copyright footer into one unified design
Code Type: Universal Footer
*/

// Add the CSS styles
add_action('wp_head', function() {
    ?>
    <style>
        .custom-footer-container {
            position: relative;
            width: 100%;
            margin-top: 30px;
        }

        .social-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #f8f9fa;
            max-width: var(--global-content-width);
            margin: 0 auto 20px auto;
            width: calc(100% - 40px);
        }

        @media screen and (max-width: 1024px) {
            .social-footer {
                width: calc(100% - 40px);
                margin: 0 auto 20px auto;
            }
        }

        @media screen and (max-width: 768px) {
            .social-footer {
                flex-direction: column;
                margin: 0 auto 20px auto;
                gap: 20px;
                width: calc(100% - 40px);
            }

            .footer-section {
                justify-content: center;
                width: 100%;
            }

            .social-links, .custom-icons {
                justify-content: center;
                flex-wrap: wrap;
            }
        }

        .footer-section {
            flex: 1;
            display: flex;
            align-items: center;
        }

        .social-links {
            justify-content: flex-start;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .custom-icons {
            justify-content: flex-end;
        }

        .social-links a, .custom-icons a {
            margin: 0 8px;
            padding: 8px;
            color: #333;
            text-decoration: none;
            transition: color 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5em;
        }

        .social-links a svg, .custom-icons a svg, .custom-icons a img {
            width: 1.8em;
            height: 1.8em;
        }

        .social-links a:hover, .custom-icons a:hover {
            color: #0066cc;
        }

        .copyright-footer {
            padding: 0;
            text-align: center;
            background: linear-gradient(to top, rgba(255,255,255,1) 0%, rgba(255,255,255,0) 100%);
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
            color: rgb(168, 164, 164);
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

// Add Font Awesome for icons
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
});

// Combined footer content function
function render_combined_footer() {
    ?>
    <div class="custom-footer-container">
        <!-- Social Footer Section -->
        <div class="social-footer">
            <!-- Left section - Social links -->
            <div class="footer-section social-links">
                <a href="<?php echo esc_url('https://www.linkedin.com/in/clementmondary/'); ?>" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-linkedin"></i>
                </a>
                <a href="<?php echo esc_url('https://www.facebook.com/clementmondary/'); ?>" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-facebook"></i>
                </a>
                <a href="<?php echo esc_url('https://x.com/Clement_mondary'); ?>" target="_blank" rel="noopener noreferrer">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" style="width: 1em; height: 1em;"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"/></svg>
                </a>
                <a href="<?php echo esc_url('https://www.instagram.com/clementmondary'); ?>" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="<?php echo esc_url('https://github.com/mondary'); ?>" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-github"></i>
                </a>
                <a href="<?php echo esc_url('https://bsky.app/profile/pouark.bsky.social'); ?>" target="_blank" rel="noopener noreferrer">
                    <i class="fas fa-cloud"></i>
                </a>
                <a href="<?php echo esc_url('https://mastodon.social/@pouark'); ?>" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-mastodon"></i>
                </a>
                <a href="<?php echo esc_url('https://www.threads.net/@clementmondary'); ?>" target="_blank" rel="noopener noreferrer">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" style="width: 1em; height: 1em;"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M331.5 235.7c2.2 .9 4.2 1.9 6.3 2.8c29.2 14.1 50.6 35.2 61.8 61.4c15.7 36.5 17.2 95.8-30.3 143.2c-36.2 36.2-80.3 52.5-142.6 53h-.3c-70.2-.5-124.1-24.1-160.4-70.2c-32.3-41-48.9-98.1-49.5-169.6V256v-.2C17 184.3 33.6 127.2 65.9 86.2C102.2 40.1 156.2 16.5 226.4 16h.3c70.3 .5 124.9 24 162.3 69.9c18.4 22.7 32 50 40.6 81.7l-40.4 10.8c-7.1-25.8-17.8-47.8-32.2-65.4c-29.2-35.8-73-54.2-130.5-54.6c-57 .5-100.1 18.8-128.2 54.4C72.1 146.1 58.5 194.3 58 256c.5 61.7 14.1 109.9 40.3 143.3c28 35.6 71.2 53.9 128.2 54.4c51.4-.4 85.4-12.6 113.7-40.9c32.3-32.2 31.7-71.8 21.4-95.9c-6.1-14.2-17.1-26-31.9-34.9c-3.7 26.9-11.8 48.3-24.7 64.8c-17.1 21.8-41.4 33.6-72.7 35.3c-23.6 1.3-46.3-4.4-63.9-16c-20.8-13.8-33-34.8-34.3-59.3c-2.5-48.3 35.7-83 95.2-86.4c21.1-1.2 40.9-.3 59.2 2.8c-2.4-14.8-7.3-26.6-14.6-35.2c-10-11.7-25.6-17.7-46.2-17.8H227c-16.6 0-39 4.6-53.3 26.3l-34.4-23.6c19.2-29.1 50.3-45.1 87.8-45.1h.8c62.6 .4 99.9 39.5 103.7 107.7l-.2 .2zm-156 68.8c1.3 25.1 28.4 36.8 54.6 35.3c25.6-1.4 54.6-11.4 59.5-73.2c-13.2-2.9-27.8-4.4-43.4-4.4c-4.8 0-9.6 .1-14.4 .4c-42.9 2.4-57.2 23.2-56.2 41.8l-.1 .1z"/></svg>
                </a>
                <a href="<?php echo esc_url('https://cmondary.tumblr.com/'); ?>" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-tumblr"></i>
                </a>
                <a href="<?php echo esc_url('https://mondary.design/feed/'); ?>" target="_blank" rel="noopener noreferrer">
                    <i class="fas fa-rss"></i>
                </a>
            </div>

            <!-- Right section - Ko-fi button and custom icons -->
            <div class="footer-section custom-icons">
                <script type='text/javascript' src='https://storage.ko-fi.com/cdn/widget/Widget_2.js'></script>
                <script type='text/javascript'>kofiwidget2.init('Buy me a coffee !', '#FE7676', 'F1F31908HD');kofiwidget2.draw();</script>
            </div>
        </div>

        <!-- Copyright Footer Section -->
        <footer class="copyright-footer">
            <div class="footer-mask"></div>
            <div class="footer-text">© 2025 Clement MONDARY</div>
        </footer>
    </div>
    <?php
}

// Hook the combined footer to display at the bottom of the page
add_action('wp_footer', 'render_combined_footer');