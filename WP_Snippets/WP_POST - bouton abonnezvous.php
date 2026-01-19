// Masque le bouton Jetpack "Abonnez-vous" au scroll
function clm_hide_subscribe_button_on_scroll_styles() {
    ?>
    <style>
        .clm-subscribe-hidden {
            opacity: 0;
            transform: translateY(8px);
            pointer-events: none;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
    </style>
    <?php
}
add_action('wp_head', 'clm_hide_subscribe_button_on_scroll_styles');

function clm_hide_subscribe_button_on_scroll_script() {
    ?>
    <script>
        (function() {
            function handleScroll() {
                var btn = document.querySelector('button[name="jetpack_subscriptions_widget"]');
                if (!btn) return;
                if (window.scrollY > 10) {
                    btn.classList.add('clm-subscribe-hidden');
                } else {
                    btn.classList.remove('clm-subscribe-hidden');
                }
            }
            handleScroll();
            window.addEventListener('scroll', handleScroll, { passive: true });
        })();
    </script>
    <?php
}
add_action('wp_footer', 'clm_hide_subscribe_button_on_scroll_script');
