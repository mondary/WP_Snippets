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
