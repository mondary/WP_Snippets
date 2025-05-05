add_action('wp_footer', function () {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        document.addEventListener('keydown', function (e) {
            const isMac = navigator.platform.toUpperCase().indexOf('MAC') >= 0;
            const isKPressed = e.key === 'k' || e.key === 'K';
            const isCmdK = isMac && e.metaKey && isKPressed;
            const isCtrlK = !isMac && e.ctrlKey && isKPressed;

            if (isCmdK || isCtrlK) {
                e.preventDefault();

                // Bouton qui ouvre le tiroir de recherche
                const searchButton = document.querySelector('button.search-toggle-open');

                if (searchButton) {
                    searchButton.click();

                    // Focus automatique après un petit délai pour laisser le tiroir s’ouvrir
                    setTimeout(() => {
                        const input = document.querySelector('#search-drawer .search-field');
                        if (input) {
                            input.focus();
                            input.select();
                        }
                    }, 300);
                } else {
                    console.warn("Bouton de recherche introuvable.");
                }
            }
        });
    });
    </script>
    <?php
});
