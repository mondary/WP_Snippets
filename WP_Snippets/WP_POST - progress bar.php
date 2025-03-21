<?php
// Ajouter la barre de progression dans le header
add_action('wp_body_open', 'add_reading_progress_bar');
function add_reading_progress_bar() {
    echo '<div class="reading-progress-bar"></div>';
}

// Ajouter le CSS
add_action('wp_head', 'add_progress_bar_styles');
function add_progress_bar_styles() {
    ?>
    <style>
        .reading-progress-bar {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 4px;
            background: #007bff;
            z-index: 9999;
            transition: width 0.2s ease-in-out;
        }
    </style>
    <?php
}

// Ajouter le JavaScript
add_action('wp_footer', 'add_progress_bar_script');
function add_progress_bar_script() {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const progressBar = document.querySelector('.reading-progress-bar');
            
            window.addEventListener('scroll', function() {
                const windowHeight = document.documentElement.clientHeight;
                const documentHeight = document.documentElement.scrollHeight - windowHeight;
                const scrolled = window.scrollY;
                
                const progress = (scrolled / documentHeight) * 100;
                progressBar.style.width = progress + '%';
            });
        });
    </script>
    <?php
}
?>