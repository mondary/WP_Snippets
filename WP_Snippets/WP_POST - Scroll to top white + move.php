function custom_styles_and_js_for_elements() {
    ?>
    <style>
        .gt_switcher_wrapper,
        #kt-scroll-up {
            background-color: #ffffff !important;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scrollButton = document.querySelector('#kt-scroll-up');
            if (scrollButton) {
                scrollButton.style.position = 'fixed';
                scrollButton.style.bottom = '80px';
                scrollButton.style.right = '20px';
            }
        });
    </script>
    <?php
}
add_action('wp_head', 'custom_styles_and_js_for_elements');

