/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/020__id-34__post-external-links-newtab-ref-mondary.php
 * Display name: POST - External links +NewTab +ref=mondary
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 34
 * Online modified: 2025-03-07 14:42:57
 * Online revision: 6
 * Exact duplicate group: non
 * Version family: POST - External links +NewTab +ref=mondary (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/020__id-34__post-external-links-newtab-ref-mondary.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: the_content, widget_text, widget_text_content
 * Fonctions clefs: ajouter_ref_aux_liens_externes
 * Lignes / octets (brut): 45 / 1887
 * Hash code normalise (sha256): f41d0489c85fac89600ec489d215d3e26d3e8bcd713b5216561dff748da003db
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: post-external-links-newtab-ref-mondary__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-external-links-newtab-ref-mondary__v001.php
 * Resume fonctionnalites: 3 hook(s) WP, 1 fonction(s) clef
 * Features detectees: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: the_content, widget_text, widget_text_content
 * Fonctions clefs: ajouter_ref_aux_liens_externes
 * APIs WP detectees: home_url, add_filter
 * Signatures contenu: html-markup
 * Lignes / octets: 57 / 2388
 * Empreinte code (sha256): 45bd8ec7dba983d25729b12e822a132a29f8949b17c489f099b081b9ec48c43f
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: post-external-links-newtab-ref-mondary__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-external-links-newtab-ref-mondary__v001.php
 * Bucket FINAL: archive
 * Statut: INACTIVE
 * Cluster principal: links_external
 * Clusters secondaires: aucun
 * Domaine: post-front
 * Confiance: high
 * Scores (top): links_external=15, frontend_ui_widget=2
 * Raisons principales: external links, newtab, ref
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

function ajouter_ref_aux_liens_externes($content) {
    // Utilise une expression régulière pour trouver tous les liens <a> dans le contenu
    $pattern = '/<a[^>]+href=["\'](http[^"\']+)["\'][^>]*>/i';

    // Fonction de rappel pour ajouter le paramètre 'ref' aux liens externes
    $content = preg_replace_callback($pattern, function($matches) {
        $href = $matches[1];

        // Vérifie si l'URL est externe
        if (strpos($href, home_url()) === false) {
            // Ajoute le paramètre ref à l'URL
            $separator = strpos($href, '?') === false ? '?' : '&';
            $href .= $separator . 'ref=mondary.design';

            // Ouvre le lien dans un nouvel onglet et ajoute "noopener"
            $new_link = str_replace($matches[1], $href, $matches[0]);
            $new_link = preg_replace('/<a([^>]*)>/i', '<a$1 target="_blank" rel="noopener">', $new_link);

            return $new_link;
        }

        return $matches[0]; // Retourne le lien tel quel s'il est interne
    }, $content);

    return $content;
}

// Ajoute ce filtre au contenu avant qu'il soit affiché
add_filter('the_content', 'ajouter_ref_aux_liens_externes');
add_filter('widget_text', 'ajouter_ref_aux_liens_externes');
add_filter('widget_text_content', 'ajouter_ref_aux_liens_externes');
