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
            margin: 0 300px 20px 300px;
        }

        @media screen and (max-width: 1024px) {
            .social-footer {
                margin: 0 100px 20px 100px;
            }
        }

        @media screen and (max-width: 768px) {
            .social-footer {
                flex-direction: column;
                margin: 0 20px 20px 20px;
                gap: 20px;
            }

            .footer-section {
                justify-content: center;
            }

            .social-links, .custom-icons {
                justify-content: center;
            }
        }

        .footer-section {
            flex: 1;
            display: flex;
            align-items: center;
        }

        .social-links {
            justify-content: flex-start;
        }

        .kofi-section {
            justify-content: center;
        }

        .custom-icons {
            justify-content: flex-end;
        }

        .social-links a, .custom-icons a {
            margin: 0 10px;
            color: #333;
            text-decoration: none;
            transition: color 0.3s ease;
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
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
});

// Combined footer content function
function render_combined_footer() {
    ?>
    <div class="custom-footer-container">
        <!-- Social Footer Section -->
        <div class="social-footer">
            <!-- Left section - Social links -->
            <div class="footer-section social-links">
                <a href="<?php echo esc_url('https://linkedin.com/your-profile'); ?>" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-linkedin"></i>
                </a>
                <a href="<?php echo esc_url('https://facebook.com/your-page'); ?>" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-facebook"></i>
                </a>
                <a href="<?php echo esc_url('https://twitter.com/your-profile'); ?>" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="<?php echo esc_url('https://instagram.com/your-profile'); ?>" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-instagram"></i>
                </a>
            </div>

            <!-- Center section - Ko-fi button -->
            <div class="footer-section kofi-section">
                <a href='https://ko-fi.com/F1F31908HD' target='_blank'>
                    <img height='36' style='border:0px;height:36px;' src='https://storage.ko-fi.com/cdn/kofi6.png?v=6' border='0' alt='Buy Me a Coffee at ko-fi.com' />
                </a>
            </div>

            <!-- Right section - Custom icons -->
            <div class="footer-section custom-icons">
                <a href="<?php echo esc_url('https://github.com/your-profile'); ?>" target="_blank" rel="noopener noreferrer">
                    <i class="fab fa-github"></i>
                </a>
                <a href="<?php echo esc_url('https://your-rss-feed.com'); ?>" target="_blank" rel="noopener noreferrer">
                    <i class="fas fa-rss"></i>
                </a>
                <a href="<?php echo esc_url('https://your-custom-link.com'); ?>" target="_blank" rel="noopener noreferrer">
                    <i class="fas fa-link"></i>
                </a>
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