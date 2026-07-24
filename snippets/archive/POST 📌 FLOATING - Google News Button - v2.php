<?php
/*
 * Display name: POST 📌 FLOATING - Google News Button - v2
 * Scope: global
 */

/*
 * Bouton flottant "Google News" - toujours visible sur tous les ecrans (frontend).
 * Lien : https://www.google.com/preferences/source?q=https://mondary.design
 *
 * v2 : Symetrie gauche/droite.
 *      Colonne gauche  : GTranslate (bottom 80px, se barre au scroll) + Google News (bottom 20px, fixe).
 *      Colonne droite  : back-to-top (bottom 80px) + newsletter (bottom 20px).
 *
 *      Le repositionnement du switcher GTranslate (.gt_switcher_wrapper) au-dessus du bouton
 *      Google News est gere ici. Le masquage au scroll (classe .gt-hidden) reste porte par
 *      le snippet "POST FOOTER - Scroll To Top - v2".
 */

// Styles inline injectes dans <head>.
add_action( 'wp_head', 'md_gnews_button_styles' );
function md_gnews_button_styles() {
    ?>
    <style>
        .md-gnews-btn {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 99998;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 18px 10px 14px;
            background: #ffffff;
            color: #3c4043;
            text-decoration: none;
            border-radius: 999px;
            font-family: 'Google Sans', 'Roboto', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 14px;
            font-weight: 500;
            line-height: 1.2;
            box-shadow: 0 4px 14px rgba(60, 64, 67, 0.25);
            border: 1px solid rgba(60, 64, 67, 0.08);
            transition: transform 0.25s ease, box-shadow 0.25s ease, color 0.25s ease;
            will-change: transform;
        }

        .md-gnews-btn:hover,
        .md-gnews-btn:focus-visible {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(60, 64, 67, 0.35);
            color: #1a73e8;
            outline: none;
        }

        .md-gnews-btn:focus-visible {
            box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.4), 0 8px 24px rgba(60, 64, 67, 0.35);
        }

        .md-gnews-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 22px;
            height: 22px;
        }

        .md-gnews-text {
            display: flex;
            flex-direction: column;
            line-height: 1.15;
        }

        .md-gnews-text small {
            font-size: 11px;
            color: #5f6368;
            font-weight: 400;
        }

        .md-gnews-text strong {
            font-size: 14px;
            color: #3c4043;
            font-weight: 600;
        }

        /* Point rouge "live" qui pulse pour attirer l'oeil. */
        .md-gnews-btn::before {
            content: '';
            position: absolute;
            top: 8px;
            right: 10px;
            width: 7px;
            height: 7px;
            background: #ea4335;
            border-radius: 50%;
            box-shadow: 0 0 0 0 rgba(234, 67, 53, 0.7);
            animation: md-gnews-pulse 2s infinite;
        }

        @keyframes md-gnews-pulse {
            0%   { box-shadow: 0 0 0 0 rgba(234, 67, 53, 0.7); }
            70%  { box-shadow: 0 0 0 8px rgba(234, 67, 53, 0); }
            100% { box-shadow: 0 0 0 0 rgba(234, 67, 53, 0); }
        }

        /* Repositionne le switcher GTranslate juste au-dessus du bouton Google News.
           Symetrie avec la colonne droite : back-to-top (bottom 80px) + newsletter (bottom 20px).
           Le masquage au scroll (.gt-hidden) reste gere par le snippet Scroll To Top v2. */
        .gt_switcher_wrapper {
            bottom: 80px !important;
            left: 20px !important;
        }

        /* Mobile : on compacte. */
        @media screen and (max-width: 600px) {
            .md-gnews-btn {
                bottom: 16px;
                left: 16px;
                padding: 8px 14px 8px 10px;
                font-size: 12px;
                gap: 8px;
            }
            .md-gnews-logo {
                width: 18px;
                height: 18px;
            }
            .md-gnews-text strong {
                font-size: 12px;
            }
            .md-gnews-text small {
                display: none;
            }
            .gt_switcher_wrapper {
                bottom: 68px !important;
                left: 16px !important;
            }
        }

        /* Respect des preferences "reduce motion". */
        @media (prefers-reduced-motion: reduce) {
            .md-gnews-btn::before { animation: none; }
            .md-gnews-btn:hover,
            .md-gnews-btn:focus-visible { transform: none; }
        }
    </style>
    <?php
}

// Bouton injecte en fin de <body>.
add_action( 'wp_footer', 'md_gnews_button_render' );
function md_gnews_button_render() {
    $url  = 'https://www.google.com/preferences/source?q=https://mondary.design';
    $logo = '<span class="md-gnews-logo" aria-hidden="true">'
          . '<svg viewBox="0 0 24 24" width="22" height="22" xmlns="http://www.w3.org/2000/svg">'
          . '<path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>'
          . '<path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>'
          . '<path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>'
          . '<path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>'
          . '</svg></span>';

    printf(
        '<a href="%1$s" class="md-gnews-btn" target="_blank" rel="noopener noreferrer" aria-label="%2$s">%3$s<span class="md-gnews-text"><small>%4$s</small><strong>%5$s</strong></span></a>',
        esc_url( $url ),
        esc_attr__( 'Lire mondary.design sur Google News', 'md-gnews' ),
        $logo,
        esc_html__( 'Lire mondary.design sur', 'md-gnews' ),
        esc_html__( 'Google News', 'md-gnews' )
    );
}
