/* CLM-CREATED-AT: 2026-02-25 */
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/active/global/011__id-18__opti-cls.php
 * Display name: OPTI - CLS
 * Scope: global
 * Online snippet: oui
 * Online active: oui
 * Online ID: 18
 * Online modified: 2024-12-06 16:41:56
 * Online revision: 2
 * Exact duplicate group: oui (0531e3ae7c8f…, 2 membres)
 * Canonical exact group ID: 84
 * Version family: DUP OPTI - CLS (1 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets_Online_Current/active/global/011__id-18__opti-cls.php
 * Is family latest: oui
 * Canonical reasons: exact-group-canonical, protected-online-active
 * Features: gtranslate
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 81 / 3108
 * Hash code normalise (sha256): 0531e3ae7c8fc65b52c9d32321372d33e698b09f1efcd18dca175f84bdb903b1
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: ACTIVE__global__opti-cls__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__opti-cls__v2__src-wp_snippets_online_current.php
 * Resume fonctionnalites: UI frontend (CSS/HTML)
 * Features detectees: css-ui
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: aucun
 * Signatures contenu: inline-style, inline-script, html-markup
 * Lignes / octets: 95 / 3718
 * Empreinte code (sha256): f4c1e0679474d0e387394759745d2d17277a111b5acdf0b65a7ebd4dacd0421f
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: ACTIVE__global__opti-cls__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__opti-cls__v2__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: ACTIVE
 * Cluster principal: performance_optimization
 * Clusters secondaires: aucun
 * Domaine: global
 * Confiance: high
 * Scores (top): performance_optimization=10, frontend_ui_widget=2
 * Raisons principales: opti, cls
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

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

