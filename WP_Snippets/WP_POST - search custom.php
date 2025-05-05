add_action('wp_head', function () {
    ?>
    <style>

    /* Nettoyage des couches internes */
    #search-drawer .drawer-inner-wrap,
    #search-drawer .search-form,
    #search-drawer .wp-block-search__inside-wrapper {
        all: unset !important;
        display: flex !important;
        align-items: center;
        justify-content: flex-start;
        width: 100%;
    }

    /* Champ de recherche géant, aligné à gauche */
    #search-drawer .search-field {
        all: unset !important;
        font-size: 10vw !important; /* Taille très grande */
        color: #ffffff !important;
        background: transparent !important;
        width: 100% !important;
        max-width: none !important;
        caret-color: white;
        font-weight: 700;
        text-align: left !important;
    }

    /* Suppression de l'icône de recherche */
    .kadence-svg-icon.kadence-search-svg {
        display: none !important;
    }


    </style>
    <?php
});
