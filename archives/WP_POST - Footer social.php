<?php
/* 
Title: Footer Social Links
Description: Custom footer with social links, Ko-fi button, and custom icons
Code Type: PHP
*/

// Add custom CSS to style the footer
add_action('wp_head', function() {
    ?>
    <style>
        .custom-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #f8f9fa;
            margin-top: 30px;
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
    </style>
    <?php
});

// Footer content function
function render_custom_footer() {
    ?>
    <footer class="custom-footer">
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
    </footer>
    <?php
}

// Add Font Awesome for icons
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
});

// Hook the footer to display it
add_action('wp_footer', 'render_custom_footer');