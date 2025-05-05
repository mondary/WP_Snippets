add_action('wp_footer', function () {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        let searchAlreadyOpened = false;

        document.addEventListener('keydown', function (e) {
            // Ignore si une touche spéciale est pressée (Ctrl, Alt, Meta)
            if (e.ctrlKey || e.metaKey || e.altKey) return;

            // Ignore si on tape dans un champ (input, textarea, select, contentEditable)
            const tag = document.activeElement.tagName.toLowerCase();
            const isTypingInField = ['input', 'textarea', 'select'].includes(tag) || document.activeElement.isContentEditable;
            if (isTypingInField) return;

            // Ignore si ce n'est pas une lettre, chiffre ou symbole "classique"
            if (e.key.length !== 1) return;

            // Empêche de réouvrir si déjà ouvert
            if (searchAlreadyOpened) return;

            // Ouvre la recherche
            const searchButton = document.querySelector('button.search-toggle-open');
            if (searchButton) {
                searchAlreadyOpened = true;
                searchButton.click();

                setTimeout(() => {
                    const input = document.querySelector('#search-drawer .search-field');
                    if (input) {
                        input.focus();
                        input.value = e.key; // préremplir avec la première lettre tapée
                        // Simule un événement input pour certains thèmes
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                }, 300);

                // Remet à zéro après quelques secondes
                setTimeout(() => { searchAlreadyOpened = false; }, 2000);
            }
        });
    });
    </script>
    <?php
});
