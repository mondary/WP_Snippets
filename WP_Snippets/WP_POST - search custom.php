add_action('wp_head', function () {
    ?>
    <style>

    /* Nettoyage des couches internes */
    #search-drawer .drawer-inner-wrap,
    #search-drawer .search-form,
    #search-drawer .wp-block-search__inside-wrapper
	    {
        all: unset !important;
        display: flex !important;
        align-items: center;
        justify-content: flex-start;
        width: 100%;
    }    
    /* Champ de recherche géant, aligné à gauche */
			#search-drawer .search-field {
        all: unset !important;
        font-size: 15vw !important; /* Taille très grande */
        color: #ffffff !important;
        width: 100% !important;
        max-width: none !important;
    }
    </style>
    <?php
});
