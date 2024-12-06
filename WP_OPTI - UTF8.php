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