<!-- Optimisation des performances et réduction du Cumulative Layout Shift (CLS) -->

<!-- Configuration CLS avec Lazy Load -->
<style>
    /* Fixer les dimensions par défaut des images pour éviter les décalages de mise en page */
    img {
        width: 100%;
        max-width: 600px; /* Ajuste cette valeur en fonction de la taille maximale souhaitée */
        height: auto; /* Conserver les proportions */
    }

    /* Assurer que les conteneurs des images vedettes maintiennent la mise en page stable */
    .post-thumbnail-inner {
        width: 100% !important; /* Garantir que l'élément occupe toute la largeur disponible */
    }

    /* Préserver les proportions des images vedettes sans les forcer à une taille spécifique */
    .post-thumbnail-inner img {
        width: auto !important; /* Empêche le redimensionnement automatique */
        max-width: 100% !important; /* Limite la largeur à celle du conteneur */
        height: auto !important; /* Maintient les proportions */
    }

    /* Fixer les dimensions des iframes pour prévenir les décalages lors du chargement */
    iframe {
        width: 100%;
        min-height: 315px; /* Ajuste cette valeur selon la hauteur requise pour tes iframes */
    }

    /* Améliorer la fluidité des transitions pour les changements visuels non critiques */
    * {
        transition-property: opacity, background-color; /* Limite la transition à des propriétés légères */
        transition-duration: 0.3s; /* Rend la transition plus douce */
    }
</style>

<!-- Script pour appliquer le Lazy Load aux images afin d'améliorer les performances -->
<script>
    // Ajout de l'attribut "loading='lazy'" aux images n'ayant pas cet attribut
    document.querySelectorAll('img').forEach(function(img) {
        if (!img.hasAttribute('loading')) {
            img.setAttribute('loading', 'lazy'); // Active le Lazy Loading
        }
    });
</script>

<!-- Reduit la taille des drapeaux de l'extension de traduction -->
<style>
    .notranslate.nturl.glink.gt_switcher-popup > [src] {
        height: 16px;
        width: auto;
        object-fit: contain;
    }

    /* Cibler et ajuster la taille des éléments dans .gt_languages */
    .gt_languages img {
        height: 16px; /* Ajuste selon tes besoins */
        width: auto;  /* Maintient les proportions */
        object-fit: contain; /* Empêche la déformation */
    }

    /* Cibler les drapeaux dans .gt_languages pour réduire leur taille */
    .gt_languages > a.nturl.glink > [src] {
        height: 16px; /* Ajuste la hauteur à ta convenance */
        width: auto;  /* Maintient les proportions */
        object-fit: contain; /* Évite les déformations */
    }
</style>

