
// Ajout d'un script pour changer le background en blanc pour .gt_switcher_wrapper et #kt-scroll-up
function custom_styles_for_elements() {
    ?>
    <style>
        .gt_switcher_wrapper {
            background-color: #ffffff !important;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        .gt_switcher_wrapper.gt-hidden {
            opacity: 0;
            transform: translateY(8px);
            pointer-events: none;
        }
    </style>
    <?php
}
add_action('wp_head', 'custom_styles_for_elements');

// Masque le switcher GTranslate au scroll
function gt_hide_on_scroll_script() {
    ?>
    <script>
        (function() {
            function handleScroll() {
                var el = document.querySelector('.gt_switcher_wrapper');
                if (!el) return;
                if (window.scrollY > 10) {
                    el.classList.add('gt-hidden');
                } else {
                    el.classList.remove('gt-hidden');
                }
            }
            handleScroll();
            window.addEventListener('scroll', handleScroll, { passive: true });
        })();
    </script>
    <?php
}
add_action('wp_footer', 'gt_hide_on_scroll_script');
