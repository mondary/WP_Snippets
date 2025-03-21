
// Ajout d'un script pour changer le background en blanc pour .gt_switcher_wrapper et #kt-scroll-up
function custom_styles_for_elements() {
    ?>
    <style>
        .gt_switcher_wrapper {
            background-color: #ffffff !important;
        }
    </style>
    <?php
}
add_action('wp_head', 'custom_styles_for_elements');