/* CLM-CREATED-AT: 2026-02-25 */
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/active/global/010__id-17__opti-utf8.php
 * Display name: OPTI - UTF8
 * Scope: global
 * Online snippet: oui
 * Online active: oui
 * Online ID: 17
 * Online modified: 2024-12-06 16:39:39
 * Online revision: 3
 * Exact duplicate group: oui (70ec5474b78f…, 2 membres)
 * Canonical exact group ID: 83
 * Version family: DUP OPTI - UTF8 (1 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets_Online_Current/active/global/010__id-17__opti-utf8.php
 * Is family latest: oui
 * Canonical reasons: exact-group-canonical, protected-online-active
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 34 / 1519
 * Hash code normalise (sha256): 70ec5474b78f949c993a775d79a334b1ee918a008632053a2b317f56b2d03a35
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: ACTIVE__global__opti-utf8__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__opti-utf8__v2__src-wp_snippets_online_current.php
 * Resume fonctionnalites: tracking / analytics
 * Features detectees: tracking-analytics
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: aucun
 * Signatures contenu: html-markup
 * Lignes / octets: 48 / 2133
 * Empreinte code (sha256): 9c5184d3da1af964e95a17257aeb307db8f1dc71406a7ce5683c499169f7bd91
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: ACTIVE__global__opti-utf8__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/ACTIVE__global__opti-utf8__v2__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: ACTIVE
 * Cluster principal: performance_optimization
 * Clusters secondaires: tracking_analytics
 * Domaine: global
 * Confiance: high
 * Scores (top): performance_optimization=10, tracking_analytics=6
 * Raisons principales: opti, utf8
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

<!-- il manque une déclaration explicite de l'encodage des caractères (souvent UTF-8) -->
<meta charset="UTF-8">

<!-- Configuration CSP ajoutée pour sécuriser le site -->
<!-- 
'self' : Autorise les images depuis ton domaine principal.
https://i0.wp.com : Autorise les images servies via le CDN de WordPress.
data: : Autorise les images en base64, souvent utilisées dans les plugins ou les icônes.
'unsafe-inline' : Autorise tous les styles inline, mais expose ton site à des vulnérabilités XSS.
-->

<meta http-equiv="Content-Security-Policy" content="
    default-src 'self';
    script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://www.googletagmanager.com https://www.google-analytics.com https://secure.gravatar.com https://s0.wp.com https://www.gstatic.com;
    style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://s0.wp.com;
    img-src 'self' https://i0.wp.com https://secure.gravatar.com data:;
    font-src 'self' https://fonts.gstatic.com;
    connect-src 'self' https://www.google-analytics.com https://api.wordpress.org;
    frame-src 'none';
    object-src 'none';
">