<?php
/*
 * Display name: FRONTEND 🌸 FAB - Hub Flottant - v16
 * Scope: global
 */

if (!defined('ABSPATH')) exit;

/*
 * Hub Flottant v7 : FAB lateral en STACK. Un bouton flottant (droite par
 * defaut) qui, au survol (ou tap / focus clavier), deploie une colonne
 * d'items a largeur unique (aspect v4 conserve) :
 *
 *   1. Google News          (lien externe, URL reprise de Google News Button v3)
 *   2. Articles programmés  (ligne cliquable -> /?future_site=1 : le futur
 *                            site. Jauge 18px + nombre + date ; l'accès et le
 *                            paywall sont gérés par Futur Menubar v4)
 *   3. Statistiques         (lien /statistiques/ = page du snippet Site Stats Page,
 *                            + vues annee + compteur live #fab-live-count)
 *   4. Ko-fi                (lien externe, handle F1F31908HD repris de Social Ego v2)
 *   5. Newsletter           (lien /newsletter/, la page dediee du site)
 *   6. Lire en diaporama    (declenche l'overlay de News Diaporama v3 ; son
 *                            bouton play #np-fab est masque par ce snippet)
 *   7. Traduction           (switcher .gt_switcher_wrapper adopte dans la stack,
 *                            fond blanc du contrat "GTranslate White v2")
 * Poignee ⇄ collee au burger (picto fa-right-left) : bascule droite <-> gauche,
 * memorisee en localStorage. Pas de drag & drop.
 *
 * Toast promotionnel : carte large (min 300px, texte sur 1-2 lignes max) a
 * pastille coloree (label + texte), au-dessus de tout le reste dans le hub
 * (z-index > wrapper GTranslate) ; tirage sans repetition (Fisher-Yates
 * persiste) ; cliquable, auto-masquee apres 10s, fermable (silence 30 min).
 * Burger : les icônes officielles iconmonstr layer-multiple-alt-filled et
 * cube-filled (téléchargées, licence iconmonstr libre) se MORPHENT via
 * GSAP MorphSVGPlugin (gratuit sur cdnjs depuis GSAP 3.12.5) quand le menu
 * s'ouvre — vrai morphing de path, avec repli statique si GSAP absent.
 *
 * DELEGATION : chaque element reste porte par son script dedie, le hub agrege :
 *   - POST ▶️ PLAYER - News Diaporama v3 : RESTE ACTIF (overlay plein ecran +
 *     endpoint news-player/v1/posts). Son bouton play bleu #np-fab est masque
 *     ici et remplace par l'item "Lire en diaporama" (clic programme sur le
 *     bouton masque). Si v3 est inactive, l'item se masque tout seul.
 *   - POST 🦶 FOOTER - Scroll To Top v2 : RESTE ACTIF (#kt-scroll-up Kadence
 *     repositionne par lui ; PLUS de masquage dans ce snippet)
 * Historique : v16 = burger en icônes officielles iconmonstr
 * (layer-multiple-alt-filled <-> cube-filled) avec vrai morphing via GSAP
 * MorphSVGPlugin (CDN) ; v15 = icônes burger 46px + losanges redessinés
 * (remplacés ici par les vraies icônes) ; v14 = republication de la v13
 * modifiée après installation (burger animé stack->cube, toast élargi,
 * z-index GT/petals, fond blanc GT supprimé) ; v13 = burger animé
 * stack->cube (géométrie d'après iconmonstr
 * layer-multiple-alt-filled / cube-filled) ; v12 = toast redessiné +
 * tirage sans répétition ; v11 = toast promotionnel + poignée ⇄ collée au
 * burger ; v10 = ligne "Articles programmés" vers le futur site ; v9 =
 * alignements GTranslate/jauge ; v8 = masquage
 * .wppk-floating-newsletter-btn ; v7 = item diaporama + masquage #np-fab ;
 * v6 = Newsletter vers /newsletter/ ; v5 = rebasage scripts dédiés.
 *
 * ACTIVATION : activer ce snippet PUIS desactiver uniquement :
 *   - POST 📌 FLOATING - Google News Button v3 (son bouton flottant bas
 *     gauche est remplace par l'item "Suivre sur Google News")
 *
 * Hooks WP: wp_enqueue_scripts, wp_head, wp_footer
 * Fonctions clefs: mdhub_enqueue_icons, mdhub_print_styles, mdhub_render
 */


// -----------------------------------------------------------------------------
// 2) Styles
// -----------------------------------------------------------------------------
function mdhub_enqueue_icons() {
    wp_enqueue_style('mdhub-font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');
    wp_enqueue_script('gsap-core', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/gsap.min.js', array(), '3.13.0', true);
    wp_enqueue_script('gsap-morphsvg', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/MorphSVGPlugin.min.js', array('gsap-core'), '3.13.0', true);
}

add_action('wp_head', 'mdhub_print_styles');
function mdhub_print_styles() {
    if (is_admin()) return;
    ?>
    <style>
        /* ================= STACK FAB ================= */
        #mdhub {
            position: fixed;
            right: 14px;
            bottom: max(14px, calc(env(safe-area-inset-bottom) + 10px));
            z-index: 99998;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            transition: right 0.35s ease, left 0.35s ease;
        }
        #mdhub.mdhub-left { right: auto; left: 14px; }

        /* ---- Colonne d'items deployee au survol (hors flux : pas de zone fantome) ---- */
        #mdhub-petals {
            position: absolute;
            z-index: 10;    /* la stack deployee passe toujours au-dessus du toast */
            bottom: calc(100% + 8px);
            right: 0;
            display: flex;
            flex-direction: column;
            align-items: stretch;
            width: max-content;
            max-width: calc(100vw - 88px);
            padding: 5px;
            border: 1px solid rgba(15, 23, 42, 0.10);
            border-radius: 13px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.18);
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            pointer-events: none;
            transition: opacity 0.25s ease, transform 0.3s ease, visibility 0s 0.35s;
        }
        #mdhub.mdhub-left #mdhub-petals { right: auto; left: 0; }
        #mdhub.is-open #mdhub-petals {
            opacity: 1;
            visibility: visible;
            transform: none;
            pointer-events: auto;
            transition-delay: 0s;
        }

        /* ---- FAB + poignee chevron (bascule gauche/droite) ---- */
        #mdhub-dock {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        #mdhub.mdhub-left #mdhub-dock { flex-direction: row-reverse; }
        #mdhub-main {
            width: 52px;
            height: 52px;
            border: 1px solid rgba(15, 23, 42, 0.10);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.18);
            color: #1f2937;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s ease;
        }
        #mdhub-main:hover, #mdhub-main:focus-visible { background: #f1f5f9; outline: none; }
        /* ---- Burger morph GSAP : couches (ferme) <-> cube (ouvert) ---- */
        .mdhub-main-icons { position: relative; display: block; flex: 0 0 auto; width: 46px; height: 46px; }
        .mdhub-main-icon-svg { display: block; width: 100%; height: 100%; }
        #mdhub-flip {
            width: 26px;
            height: 26px;
            border: 1px solid rgba(15, 23, 42, 0.10);
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.14);
            color: #475569;
            font-size: 11px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s ease;
            margin-right: -8px;   /* colle la poignee au burger */
            z-index: 1;
        }
        #mdhub-flip:hover, #mdhub-flip:focus-visible { background: #f1f5f9; outline: none; }
        #mdhub-flip i { transition: transform 0.3s ease; }
        #mdhub.mdhub-left #mdhub-flip { margin-right: 0; margin-left: -8px; }
        #mdhub.mdhub-left #mdhub-flip i { transform: rotate(180deg); }

        /* ---- Items : aspect v4 conserve, largeur unique ---- */
        .mdhub-item {
            width: 100%;
            min-height: 40px;
            margin: 0;
            padding: 7px 10px;
            gap: 7px;
            border: 0;
            border-radius: 8px;
            background: transparent;
            color: #1f2937;
            cursor: pointer;
            display: flex;
            align-items: center;
            text-align: left;
            text-decoration: none;
            font: inherit;
            font-size: 12px;
            font-weight: 650;
            line-height: 1.25;
            box-sizing: border-box;
            white-space: nowrap;
        }
        .mdhub-item:hover, .mdhub-item:focus-visible {
            background: #f1f5f9;
            color: var(--c, #1f2937);
            outline: none;
        }
        .mdhub-item .mdhub-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            flex: 0 0 18px;
            color: var(--c, #475569);
            font-size: 14px;
        }
        .mdhub-label {
            position: static;
            display: block;
            min-width: 0;
            color: inherit;
            white-space: nowrap;
        }
        .mdhub-label .mdhub-live {
            color: #16803c;
            font-variant-numeric: tabular-nums;
        }

        /* ---- Switcher GTranslate integre a la stack ---- */
        .gt_switcher_wrapper.mdhub-gt {
            position: static !important;
            top: auto !important;
            right: auto !important;
            bottom: auto !important;
            left: auto !important;
            width: 100% !important;
            margin: 0 !important;
            box-sizing: border-box;
            /* plus de fond blanc : la stack fournit deja la carte blanche,
               et le strip masquait le toast promotionnel */
        }
        /* Ligne du switcher : meme geometrie qu'un .mdhub-item
           (padding 10px, colonne icone 18px, gap 7px -> texte aligne a +35px) */
        .mdhub-gt .gt_switcher-popup {
            display: flex;
            align-items: center;
            gap: 7px;
            width: 100%;
            min-height: 40px;
            padding: 7px 10px;
            border-radius: 8px;
            box-sizing: border-box;
            font: inherit;
            font-size: 12px;
            font-weight: 650;
            line-height: 1.25;
            color: #1f2937;
            text-decoration: none;
            cursor: pointer;
        }
        .mdhub-gt .gt_switcher-popup:hover, .mdhub-gt .gt_switcher-popup:focus-visible {
            background: #f1f5f9;
            outline: none;
        }
        .mdhub-gt .gt_switcher-popup img {
            display: block;
            width: 18px;
            height: 18px;
            flex: 0 0 18px;
            object-fit: contain;
        }
        .mdhub-gt .gt_switcher-popup > span {
            color: inherit;
            font-size: 12px;
            font-weight: 650;
            line-height: 1.25;
            white-space: nowrap;
        }

        /* ---- Ligne info "Articles programmés" : cliquable vers le futur
           site (/?future_site=1, acces/paywall geres par Futur Menubar v4) ---- */
        .mdhub-gauge-mini {
            position: relative;
            width: 18px;
            height: 18px;
            flex: 0 0 18px;
            color: var(--c, #3182CE);
        }
        .mdhub-gauge-mini svg { width: 100%; height: 100%; transform: rotate(-90deg); display: block; }
        .mdhub-gauge-mini b {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 8px;
            font-weight: 800;
            color: var(--c, #3182CE);
            font-variant-numeric: tabular-nums;
        }
        .mdhub-label .mdhub-sched { color: var(--c, #3182CE); }

        /* Retour en haut : laisse le bouton #kt-scroll-up au script dedie
           POST 🦶 FOOTER - Scroll To Top v2 (aucun masquage ici). */

        /* ---- News Diaporama v3 : bouton play masque, l'item de la stack
           declenche son overlay (fonctionnement de v3 intact) ---- */
        #np-fab { display: none !important; }

        /* ---- Bouton flottant "Newsletter" (.wppk-floating-newsletter-btn) :
           HTML+CSS injecte cote WP hors de ce repo (pas Jetpack). Remplace
           par l'item Newsletter de la stack. A supprimer cote WP quand la
           source (snippet/element Kadence) aura ete retrouvee. ---- */
        .wppk-floating-newsletter-btn { display: none !important; }

        /* ---- Toast promotionnel (a cote du FAB, cote interieur) ---- */
        .mdhub-toast {
            position: absolute;
            bottom: 4px;
            right: calc(100% + 12px);
            z-index: 5; /* au-dessus du wrapper GTranslate (z-index inline 999999 herite) */
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 300px;
            max-width: 340px;
            padding: 10px 6px 10px 10px;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.16), 0 2px 8px rgba(15, 23, 42, 0.08);
            opacity: 0;
            visibility: hidden;
            transform: translateY(8px) scale(0.96);
            transform-origin: bottom right;
            pointer-events: none;
            transition: opacity 0.25s ease, transform 0.3s cubic-bezier(0.34, 1.4, 0.64, 1), visibility 0s 0.35s;
        }
        #mdhub.mdhub-left .mdhub-toast {
            right: auto;
            left: calc(100% + 12px);
            transform-origin: bottom left;
        }
        .mdhub-toast.is-visible {
            opacity: 1;
            visibility: visible;
            transform: none;
            pointer-events: auto;
            transition-delay: 0s;
        }
        .mdhub-toast__icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            flex: 0 0 36px;
            border-radius: 11px;
            background: var(--c, #4d7cff);
            color: #ffffff;
            font-size: 15px;
        }
        .mdhub-toast__body {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 0;
            padding: 2px 0;
            text-decoration: none;
        }
        .mdhub-toast__label {
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--c, #4d7cff);
        }
        .mdhub-toast__text {
            font-size: 12px;
            font-weight: 650;
            line-height: 1.35;
            color: #1f2937;
        }
        .mdhub-toast__body:hover .mdhub-toast__text, .mdhub-toast__body:focus-visible .mdhub-toast__text {
            color: var(--c, #4d7cff);
        }
        .mdhub-toast__close {
            align-self: flex-start;
            appearance: none;
            border: 0;
            background: transparent;
            font-size: 13px;
            line-height: 1;
            padding: 2px 4px;
            color: #cbd5e1;
            cursor: pointer;
        }
        .mdhub-toast__close:hover { color: #475569; }

        /* ---- Mobile ---- */
        @media screen and (max-width: 600px) {
            #mdhub { right: 10px; bottom: max(10px, calc(env(safe-area-inset-bottom) + 8px)); }
            #mdhub.mdhub-left { right: auto; left: 10px; }
            #mdhub-petals { max-width: calc(100vw - 84px); border-radius: 10px; }
            .mdhub-item { min-height: 42px; padding: 7px 9px; font-size: 12px; }
            .mdhub-gt .gt_switcher-popup { min-height: 42px; padding: 7px 9px; }
            .mdhub-item .mdhub-label { overflow: hidden; text-overflow: ellipsis; }
            .mdhub-toast { min-width: 260px; max-width: 300px; }
            #mdhub-main { width: 48px; height: 48px; }
            .mdhub-main-icons { width: 40px; height: 40px; }
        }

        @media (prefers-reduced-motion: reduce) {
            #mdhub, #mdhub-petals, #mdhub-main, #mdhub-flip, #mdhub-flip i,
            .mdhub-item { transition: none; }
        }

    </style>
    <?php
}

// -----------------------------------------------------------------------------
// 3) Markup + scripts
// -----------------------------------------------------------------------------
add_action('wp_footer', 'mdhub_render');
function mdhub_render() {
    if (is_admin()) return;

    // ---- Donnees : articles programmés (1 seule requete) ----
    $sched = new WP_Query(array(
        'post_type'   => 'post',
        'post_status' => 'future',
        'posts_per_page' => 1,
        'orderby'      => 'date',
        'order'        => 'DESC',
    ));
    $sched_count = (int) $sched->found_posts;
    $sched_date  = $sched->have_posts()
        ? date_i18n('j F Y', strtotime($sched->posts[0]->post_date))
        : '';
    wp_reset_postdata();

    // ---- Donnees : vues de l'annee (meme source que l'ancien badge #clm-live) ----
    global $wpdb;
    $year_views = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT SUM(views) FROM {$wpdb->prefix}site_views WHERE YEAR(view_date)=%d",
        intval(current_time('Y'))
    ));

    // ---- Jauge du panneau ----
    $gauge_r      = 16;
    $gauge_circ   = 2 * M_PI * $gauge_r;
    $gauge_pct    = ($sched_count > 0) ? (($sched_count % 100 === 0) ? 1 : ($sched_count % 100) / 100) : 0;
    $gauge_offset = $gauge_circ * (1 - $gauge_pct);

    $gnews_url  = 'https://www.google.com/preferences/source?q=https://mondary.design';
    $news_url   = home_url('/newsletter/'); // page dediee Newsletter du site
    $stats_url  = home_url('/statistiques/');
    $kofi_url   = 'https://ko-fi.com/F1F31908HD';

    // ---- Burger morph : icons officielles iconmonstr (licence iconmonstr) ----
    $burger_layer = 'm2.394 15.759s7.554 4.246 9.09 5.109c.165.093.333.132.492.132.178 0 .344-.049.484-.127 1.546-.863 9.155-5.113 9.155-5.113.246-.138.385-.393.385-.656 0-.566-.614-.934-1.116-.654 0 0-7.052 3.958-8.539 4.77-.211.115-.444.161-.722.006-1.649-.928-8.494-4.775-8.494-4.775-.502-.282-1.117.085-1.117.653 0 .262.137.517.382.655zm0-3.113s7.554 4.246 9.09 5.109c.165.093.333.132.492.132.178 0 .344-.049.484-.127 1.546-.863 9.155-5.113 9.155-5.113.246-.138.385-.393.385-.656 0-.566-.614-.934-1.116-.654 0 0-7.052 3.958-8.539 4.77-.211.115-.444.161-.722.006-1.649-.928-8.494-4.775-8.494-4.775-.502-.282-1.117.085-1.117.653 0 .262.137.517.382.655zm10.271-9.455c-.246-.128-.471-.191-.692-.191-.223 0-.443.065-.675.191l-8.884 5.005c-.276.183-.414.444-.414.698 0 .256.139.505.414.664l8.884 5.006c.221.133.447.203.678.203.223 0 .452-.065.689-.203l8.884-5.006c.295-.166.451-.421.451-.68 0-.25-.145-.503-.451-.682z';
    $burger_cube  = 'm21 7.702-8.5 4.62v9.678c1.567-.865 6.379-3.517 7.977-4.399.323-.177.523-.519.523-.891zm-9.5 4.619-8.5-4.722v9.006c0 .37.197.708.514.887 1.59.898 6.416 3.623 7.986 4.508zm-8.079-5.629 8.579 4.763 8.672-4.713s-6.631-3.738-8.186-4.614c-.151-.085-.319-.128-.486-.128-.168 0-.335.043-.486.128-1.555.876-8.093 4.564-8.093 4.564z';

    // ---- Toasts promotionnels : une feature tiree au hasard par page ----
    $toasts = array(
        array(
            'label' => 'Google News',
            'icon'  => 'fa-brands fa-google', 'c' => '#4285F4',
            'text'  => 'Suivez le site au quotidien sur Google News',
            'href'  => $gnews_url,
        ),
        array(
            'label' => 'Futur site',
            'icon'  => 'fa-solid fa-calendar-days', 'c' => '#3182CE',
            'text'  => $sched_count > 0 ? $sched_count . ' article' . ($sched_count > 1 ? 's' : '') . ' en réserve — jetez un œil au futur' : 'Un aperçu du futur site vous attend',
            'href'  => home_url('/?future_site=1'),
        ),
        array(
            'label' => 'Newsletter',
            'icon'  => 'fa-solid fa-envelope', 'c' => '#d94f8a',
            'text'  => 'Le meilleur du site, chaque jour à 14h30',
            'href'  => $news_url,
        ),
        array(
            'label' => 'Diaporama',
            'icon'  => 'fa-solid fa-images', 'c' => '#4d7cff',
            'text'  => 'Lisez les derniers articles en plein écran',
            'player' => true,
        ),
        array(
            'label' => 'Ko-fi',
            'icon'  => 'fa-solid fa-mug-hot', 'c' => '#FF5E5B',
            'text'  => 'Le site est gratuit — soutenez-le sur Ko-fi',
            'href'  => $kofi_url,
        ),
    );

    // ---- Definitions du panneau (ordre de lecture) ----
    $items = array(
        array(
            'label' => 'Suivre sur Google News',
            'href'  => $gnews_url,
            'blank' => true,
            'c'     => '#4285F4',
            'icon'  => 'fa-brands fa-google',
        ),
        array(
            'label' => 'Articles programmés',
            'href'  => home_url('/?future_site=1'),
            'info'  => true,
            'c'     => '#3182CE',
        ),
        array(
            'label' => 'Statistiques · <span class="mdhub-live"><span id="fab-live-count">1</span> en ligne</span> · ' . number_format_i18n($year_views) . ' vues',
            'href'  => $stats_url,
            'blank' => false,
            'c'     => '#0f9d58',
            'icon'  => 'fa-solid fa-chart-column',
        ),
        array(
            'label' => 'Soutenir sur Ko-fi',
            'href'  => $kofi_url,
            'blank' => true,
            'c'     => '#FF5E5B',
            'icon'  => 'fa-solid fa-mug-hot',
        ),
        array(
            'label' => 'Newsletter',
            'href'  => $news_url,
            'blank' => false,
            'c'     => '#d94f8a',
            'icon'  => 'fa-solid fa-envelope',
        ),
        array(
            'label' => 'Lire en diaporama',
            'action'=> 'player',
            'c'     => '#4d7cff',
            'icon'  => 'fa-solid fa-play',
        ),
    );
    ?>

    <nav id="mdhub" aria-label="Menu du site">
        <div id="mdhub-petals">
            <?php foreach ($items as $i => $it) :
                if (!empty($it['info'])) : ?>
            <a class="mdhub-item mdhub-info" href="<?php echo esc_url($it['href']); ?>" style="--i:<?php echo $i; ?>;--c:<?php echo $it['c']; ?>">
                <span class="mdhub-gauge-mini" aria-hidden="true">
                    <svg viewBox="0 0 40 40">
                        <circle fill="none" stroke="#E2E8F0" stroke-width="6" cx="20" cy="20" r="16"></circle>
                        <circle fill="none" stroke="currentColor" stroke-width="6" stroke-linecap="round"
                                stroke-dasharray="<?php echo esc_attr($gauge_circ); ?>"
                                stroke-dashoffset="<?php echo esc_attr($gauge_offset); ?>"
                                cx="20" cy="20" r="16"></circle>
                    </svg>
                    <b><?php echo number_format_i18n($sched_count); ?></b>
                </span>
                <span class="mdhub-label">Articles programmés · <?php echo $sched_date ? '<span class="mdhub-sched">en réserve · jusqu&rsquo;au ' . esc_html($sched_date) . '</span>' : 'aucun en attente'; ?></span>
                <span class="screen-reader-short">Articles programmés : <?php echo $sched_count > 0 ? number_format_i18n($sched_count) . ' en réserve, jusqu&rsquo;au ' . esc_html($sched_date) : 'aucun en attente'; ?>. Ouvrir le futur site.</span>
            </a>
            <?php else :
                $tag = isset($it['href']) ? 'a' : 'button';
                $url = isset($it['href']) ? sprintf(' href="%s"', esc_url($it['href'])) : ' type="button" data-action="' . esc_attr($it['action']) . '"';
                $blk = (!empty($it['blank'])) ? ' target="_blank" rel="noopener noreferrer"' : '';
            ?>
            <<?php echo $tag; ?> class="mdhub-item" style="--i:<?php echo $i; ?>;--c:<?php echo $it['c']; ?>"<?php echo $url . $blk; ?>>
                <span class="mdhub-icon" aria-hidden="true"><i class="<?php echo esc_attr($it['icon']); ?>"></i></span>
                <span class="mdhub-label"><?php echo $it['label']; ?></span>
                <span class="screen-reader-short"><?php echo wp_strip_all_tags($it['label']); ?></span>
            </<?php echo $tag; ?>>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <div id="mdhub-dock">
            <button id="mdhub-flip" type="button" aria-label="Déplacer la stack vers la gauche">
                <i class="fa-solid fa-right-left" aria-hidden="true"></i>
            </button>
            <button id="mdhub-main" type="button" aria-expanded="false" aria-controls="mdhub-petals" aria-label="Ouvrir le menu du site">
            <span class="mdhub-main-icons" aria-hidden="true">
                <svg class="mdhub-main-icon-svg" viewBox="0 0 24 24" clip-rule="evenodd" fill-rule="evenodd" stroke-linejoin="round" stroke-miterlimit="2" focusable="false">
                    <path id="mdhub-burger" fill-rule="nonzero" d="<?php echo esc_attr($burger_layer); ?>"/>
                </svg>
            </span>
            </button>

        <div id="mdhub-toast" class="mdhub-toast" role="status" hidden>
            <span class="mdhub-toast__icon" aria-hidden="true"><i class="fa-solid fa-star"></i></span>
            <a class="mdhub-toast__body" href="#">
                <span class="mdhub-toast__label"></span>
                <span class="mdhub-toast__text"></span>
            </a>
            <button class="mdhub-toast__close" type="button" aria-label="Masquer l'astuce">×</button>
        </div>
    </nav>

    <style>
        .screen-reader-short{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap}
    </style>

    <script>
    (function () {
        /* ============ STACK FAB ============ */
        var hub        = document.getElementById('mdhub');
        var mainBtn    = document.getElementById('mdhub-main');
        var flipBtn    = document.getElementById('mdhub-flip');
        var petals     = document.getElementById('mdhub-petals');
        var closeTimer = null;

        function stackOpen() { return hub.classList.contains('is-open'); }

        /* Burger : morph GSAP entre les icônes iconmonstr layer <-> cube */
        var burgerPath = document.getElementById('mdhub-burger');
        var BURGER_LAYER = <?php echo wp_json_encode($burger_layer); ?>;
        var BURGER_CUBE  = <?php echo wp_json_encode($burger_cube); ?>;
        if (window.gsap && window.MorphSVGPlugin) gsap.registerPlugin(MorphSVGPlugin);

        function morphBurger(open) {
            if (!burgerPath || !window.gsap || !window.MorphSVGPlugin) return;
            gsap.to(burgerPath, {
                duration: open ? 0.55 : 0.4,
                ease: open ? 'back.out(1.6)' : 'power2.inOut',
                morphSVG: open ? BURGER_CUBE : BURGER_LAYER
            });
        }

        function setStack(open) {
            hub.classList.toggle('is-open', open);
            mainBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
            morphBurger(open);
        }
        /* Survol (desktop) : ouvre immediatement, referme apres un court delai */
        hub.addEventListener('mouseenter', function () {
            clearTimeout(closeTimer);
            setStack(true);
        });
        hub.addEventListener('mouseleave', function () {
            clearTimeout(closeTimer);
            closeTimer = setTimeout(function () { setStack(false); }, 350);
        });
        /* Tactile / clavier */
        mainBtn.addEventListener('click', function () { setStack(!stackOpen()); });
        hub.addEventListener('focusin', function () {
            clearTimeout(closeTimer);
            setStack(true);
        });

        /* Clic a l'exterieur ou Echap : on referme */
        document.addEventListener('click', function (e) {
            if (stackOpen() && !hub.contains(e.target)) setStack(false);
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') setStack(false);
        });
        window.addEventListener('scroll', function () {
            if (stackOpen()) setStack(false);
        }, { passive: true });

        /* ---- Toast promotionnel : une feature au hasard par page ---- */
        (function () {
            var toast = document.getElementById('mdhub-toast');
            if (!toast) return;

            var pool = <?php echo wp_json_encode($toasts); ?>;
            var body = toast.querySelector('.mdhub-toast__body');
            var iconI = toast.querySelector('.mdhub-toast__icon i');
            var labelEl = toast.querySelector('.mdhub-toast__label');
            var textEl = toast.querySelector('.mdhub-toast__text');
            var hideTimer = null;

            var muted = 0;
            try { muted = parseInt(localStorage.getItem('mdhubToastMuted') || '0', 10) || 0; } catch (err) {}
            if (Date.now() - muted < 30 * 60 * 1000) return; /* ferme recemment : silence */

            /* Tirage sans repetition : ordre de Fisher-Yates persiste, on
               avance d'un cran a chaque page (les 5 features defilent avant
               qu'un nouveau cycle melange recommence). */
            var ORDER_KEY = 'mdhubToastOrder';
            var CURSOR_KEY = 'mdhubToastCursor';

            function nextPick() {
                var order = null, cursor = 0;
                try {
                    order = JSON.parse(localStorage.getItem(ORDER_KEY) || 'null');
                    cursor = parseInt(localStorage.getItem(CURSOR_KEY) || '0', 10) || 0;
                } catch (err) { order = null; }

                var valid = Array.isArray(order) && order.length === pool.length &&
                    order.every(function (i) { return i >= 0 && i < pool.length; }) &&
                    new Set(order).size === pool.length;

                if (!valid) {
                    order = pool.map(function (_, i) { return i; });
                    for (var i = order.length - 1; i > 0; i--) {
                        var j = Math.floor(Math.random() * (i + 1));
                        var tmp = order[i]; order[i] = order[j]; order[j] = tmp;
                    }
                    cursor = 0;
                }

                var pick = pool[order[cursor % order.length]];
                try {
                    localStorage.setItem(ORDER_KEY, JSON.stringify(order));
                    localStorage.setItem(CURSOR_KEY, String((cursor + 1) % order.length));
                } catch (err) {}

                return pick;
            }

            var pick = nextPick();

            function hideToast(mute) {
                toast.classList.remove('is-visible');
                clearTimeout(hideTimer);
                if (mute) {
                    try { localStorage.setItem('mdhubToastMuted', String(Date.now())); } catch (err) {}
                }
                setTimeout(function () { toast.hidden = true; }, 400);
            }

            body.addEventListener('click', function (e) {
                if (body.getAttribute('data-player')) {
                    e.preventDefault();
                    hideToast(false);
                    var npf = document.getElementById('np-fab');
                    if (npf) npf.click();
                    return;
                }
                hideToast(false); /* lien : la navigation suit */
            });
            toast.querySelector('.mdhub-toast__close').addEventListener('click', function () {
                hideToast(true);
            });

            setTimeout(function () {
                iconI.className = pick.icon;
                toast.style.setProperty('--c', pick.c);
                labelEl.textContent = pick.label;
                textEl.textContent = pick.text;
                if (pick.href) {
                    body.setAttribute('href', pick.href);
                    body.removeAttribute('data-player');
                } else {
                    body.setAttribute('href', '#');
                    body.setAttribute('data-player', '1');
                }
                toast.hidden = false;
                setTimeout(function () { toast.classList.add('is-visible'); }, 30);
                hideTimer = setTimeout(function () { hideToast(false); }, 10000);
            }, 3500);
        })();

        /* ---- Bascule gauche/droite (chevron, memorisee en localStorage) ---- */
        function setSide(left, save) {
            document.body.classList.toggle('mdhub-side-left', left);
            hub.classList.toggle('mdhub-left', left);
            flipBtn.setAttribute('aria-label', left ? 'Déplacer la stack vers la droite' : 'Déplacer la stack vers la gauche');
            if (save) {
                try { localStorage.setItem('mdhubSide', left ? 'left' : 'right'); } catch (err) {}
            }
        }
        var savedSide = 'right';
        try { savedSide = localStorage.getItem('mdhubSide') || 'right'; } catch (err) {}
        setSide(savedSide === 'left', false);
        flipBtn.addEventListener('click', function () {
            /* la stack quitte le pointeur pendant la bascule : on la referme volontairement */
            setStack(false);
            setSide(!document.body.classList.contains('mdhub-side-left'), true);
        });

        /* ---- Items cliquables de la stack ---- */
        Array.prototype.forEach.call(hub.querySelectorAll('.mdhub-item'), function (it) {
            if (it.tagName !== 'A' && it.tagName !== 'BUTTON') return; /* lignes d'info */
            it.addEventListener('click', function (e) {
                if (it.getAttribute('data-action') === 'player') {
                    e.preventDefault();
                    setStack(false);
                    var npf = document.getElementById('np-fab');
                    if (npf) npf.click(); /* declenche le bouton play (masque) de News Diaporama v3 */
                    return;
                }
                setStack(false);
            });
        });

        /* Item diaporama masque si News Diaporama v3 est inactive */
        if (!document.getElementById('np-fab')) {
            var playerRow = hub.querySelector('[data-action="player"]');
            if (playerRow) playerRow.style.display = 'none';
        }

        /* ---- GTranslate : adopte le switcher dans la stack des qu'il existe ---- */
        (function () {
            var tries = 0;
            (function adopt() {
                var gt = document.querySelector('.gt_switcher_wrapper');
                if (gt) {
                    if (!petals.contains(gt)) petals.appendChild(gt);
                    gt.classList.add('mdhub-gt');
                    return;
                }
                if (tries++ < 20) setTimeout(adopt, 500);
            })();
        })();

    })();
    </script>
    <?php
}
