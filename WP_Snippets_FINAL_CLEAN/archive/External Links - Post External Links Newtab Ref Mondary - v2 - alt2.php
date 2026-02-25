/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets
 * Source path: WP_Snippets/WP_POST - External links +NewTab +ref=mondary.php
 * Display name: WP_POST - External links +NewTab +ref=mondary
 * Scope: front-end
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: WP_POST - External links +NewTab +ref=mondary (1 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets/WP_POST - External links +NewTab +ref=mondary.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 28 / 1088
 * Hash code normalise (sha256): 86ddd7e77418efcddc037e1ef53cb917e201922b3cb2b74eab5b48928689f6d2
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: LOCAL__front-end__wp-post-external-links-newtab-ref-mondary__v1__src-wp_snippets.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/LOCAL__front-end__wp-post-external-links-newtab-ref-mondary__v1__src-wp_snippets.php
 * Resume fonctionnalites: UI frontend (CSS/HTML)
 * Features detectees: css-ui
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: aucun
 * Signatures contenu: inline-script, html-markup
 * Lignes / octets: 50 / 1930
 * Empreinte code (sha256): f63330942982d3183ad9d2a7c06715fba88974d4b0a0d057ab8556313ac3d019
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: LOCAL__front-end__wp-post-external-links-newtab-ref-mondary__v1__src-wp_snippets.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/LOCAL__front-end__wp-post-external-links-newtab-ref-mondary__v1__src-wp_snippets.php
 * Bucket FINAL: canonical
 * Statut: LOCAL
 * Cluster principal: links_external
 * Clusters secondaires: aucun
 * Domaine: post-front
 * Confiance: high
 * Scores (top): links_external=15, frontend_ui_widget=2
 * Raisons principales: external links, newtab, ref
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sélectionne tous les liens sur la page
    var links = document.querySelectorAll('a');

    links.forEach(function(link) {
        var href = link.getAttribute('href');

        // Vérifie si le lien est externe
        if (href && href.startsWith('http') && !href.includes(window.location.hostname)) {
            // Ajoute le paramètre ref à l'URL
            var newHref = href + (href.includes('?') ? '&' : '?') + 'ref=mondary.design';
            link.setAttribute('href', newHref);

            // Ouvre le lien dans un nouvel onglet
            link.setAttribute('target', '_blank');

            // Ajoute "noopener" pour la sécurité
            var rel = link.getAttribute('rel');
            link.setAttribute('rel', rel ? rel + ' noopener' : 'noopener');

            // Ajoute la classe CSS "external-link"
            var className = link.getAttribute('class');
            link.setAttribute('class', className ? className + ' external-link' : 'external-link');
        }
    });
});
</script>
