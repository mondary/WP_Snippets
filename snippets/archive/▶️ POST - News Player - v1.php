<?php
/*
 * Display name: ▶️ POST - News Player - v1
 * Scope: global
 */

if (!defined('ABSPATH')) exit;

/*
 * Title: Floating Play Button -> Fullscreen News Diaporama
 * Description: Ajoute un bouton flottant "play". Au clic, ouvre un diaporama
 *              plein ecran des derniers articles : image vedette en fond,
 *              texte en fondu en bas, miniatures cliquables (visionneuse),
 *              navigation fleches / clavier / swipe.
 * Hooks WP: wp_head, wp_footer, rest_api_init
 * Fonctions clefs: news_player_get_posts, news_player_render_markup,
 *                 news_player_print_styles, news_player_print_script
 */

// -----------------------------------------------------------------------------
// 1) Endpoint REST : renvoie les derniers articles avec images
// -----------------------------------------------------------------------------
add_action('rest_api_init', function () {
    register_rest_route('news-player/v1', '/posts', array(
        'methods'             => 'GET',
        'callback'            => 'news_player_get_posts',
        'permission_callback' => '__return_true',
    ));
});

function news_player_get_posts(WP_REST_Request $request) {
    $args = array(
        'post_type'           => 'post',
        'post_status'         => 'publish',
        'posts_per_page'      => 20,
        'orderby'             => 'date',
        'order'               => 'DESC',
        'ignore_sticky_posts' => true,
    );

    $query  = new WP_Query($args);
    $result = array();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();

            // Image vedette (taille large)
            $featured = '';
            if (has_post_thumbnail($post_id)) {
                $featured = get_the_post_thumbnail_url($post_id, 'large');
            }

            // Extraction des URLs d'images du contenu
            $raw_content     = get_the_content();
            $content_images  = array();
            if (preg_match_all('/<img[^>]+src="([^">]+)"/i', $raw_content, $m)) {
                $content_images = array_values(array_unique($m[1]));
            }

            // Contenu nettoye (sans balises img) - integralement affiche
            $filtered = apply_filters('the_content', $raw_content);
            $filtered = preg_replace('/<img[^>]*>/i', '', $filtered);
            $text     = wp_strip_all_tags($filtered);

            $result[] = array(
                'id'       => $post_id,
                'title'    => get_the_title(),
                'excerpt'  => wp_strip_all_tags(get_the_excerpt()),
                'content'  => $text,
                'link'     => get_permalink($post_id),
                'date'     => get_the_date(),
                'author'   => get_the_author(),
                'featured' => $featured,
                'images'   => $content_images,
            );
        }
        wp_reset_postdata();
    }

    return rest_ensure_response($result);
}

// -----------------------------------------------------------------------------
// 2) Styles (injectes dans wp_head)
// -----------------------------------------------------------------------------
add_action('wp_head', 'news_player_print_styles');
function news_player_print_styles() {
    ?>
    <style>
        /* ---------- Floating Action Button ---------- */
        #np-fab {
            position: fixed;
            bottom: 24px;
            left: 50%;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #4d7cff;
            border: none;
            box-shadow: 0 6px 22px rgba(77, 124, 255, 0.45);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            z-index: 99998;
            transition: transform 0.22s ease, box-shadow 0.22s ease;
            transform: translateX(-50%) translateY(20px);
            opacity: 0;
            animation: np-fab-in 0.5s ease 0.4s forwards;
        }
        #np-fab:hover {
            transform: translateX(-50%) translateY(0) scale(1.08);
            box-shadow: 0 12px 30px rgba(77, 124, 255, 0.6);
        }
        #np-fab svg { margin-left: 3px; }
        @keyframes np-fab-in {
            to { transform: translateX(-50%) translateY(0); opacity: 1; }
        }

        /* ---------- Overlay plein ecran ---------- */
        #np-overlay {
            position: fixed;
            inset: 0;
            background: #000;
            z-index: 99999;
            display: none;
            opacity: 0;
            transition: opacity 0.4s ease;
            overflow: hidden;
        }
        #np-overlay.is-open {
            display: block;
            opacity: 1;
        }

        #np-stage { position: absolute; inset: 0; }

        #np-bg {
            position: absolute;
            inset: 0;
            background-color: #111;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            transition: opacity 0.45s ease;
        }
        #np-bg::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to top,
                rgba(0, 0, 0, 0.96) 0%,
                rgba(0, 0, 0, 0.55) 30%,
                rgba(0, 0, 0, 0.10) 60%,
                rgba(0, 0, 0, 0.35) 100%
            );
        }

        /* ---------- Panneau bas (fondu) ---------- */
        #np-bottom {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            padding: 40px 8% 32px;
            color: #fff;
        }
        #np-meta {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 2px;
            opacity: 0.7;
            margin-bottom: 10px;
        }
        #np-title {
            font-size: clamp(24px, 4vw, 42px);
            line-height: 1.15;
            margin: 0 0 14px;
            font-weight: 800;
            color: #4d7cff;
            text-shadow: 0 0 24px rgba(77, 124, 255, 0.55), 0 2px 18px rgba(0, 0, 0, 0.7);
        }
        #np-text {
            font-size: 15px;
            line-height: 1.65;
            opacity: 0.94;
            max-height: 220px;
            overflow-y: auto;
            margin-bottom: 16px;
            padding-right: 8px;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
        }
        #np-text::-webkit-scrollbar { width: 6px; }
        #np-text::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }
        #np-text p { margin: 0 0 12px; }
        #np-text p:last-child { margin-bottom: 0; }

        /* ---------- Bande de miniatures ---------- */
        #np-thumbs {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 8px;
            margin-bottom: 16px;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
        }
        #np-thumbs::-webkit-scrollbar { height: 6px; }
        #np-thumbs::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }
        #np-thumbs img {
            width: 72px;
            height: 72px;
            object-fit: cover;
            border-radius: 6px;
            cursor: pointer;
            flex-shrink: 0;
            border: 2px solid rgba(255, 255, 255, 0.15);
            transition: border-color 0.2s ease, transform 0.2s ease;
        }
        #np-thumbs img:hover {
            border-color: #fff;
            transform: translateY(-3px);
        }

        #np-readmore {
            display: inline-block;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            padding: 10px 18px;
            border: 1px solid rgba(255, 255, 255, 0.45);
            border-radius: 30px;
            transition: background 0.2s ease, border-color 0.2s ease;
        }
        #np-readmore:hover {
            background: rgba(255, 255, 255, 0.18);
            border-color: #fff;
        }

        /* ---------- Boutons ---------- */
        #np-close, #np-prev, #np-next {
            position: absolute;
            background: rgba(255, 255, 255, 0.10);
            border: none;
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            transition: background 0.2s ease, transform 0.2s ease;
            z-index: 2;
        }
        #np-close:hover, #np-prev:hover, #np-next:hover {
            background: rgba(255, 255, 255, 0.25);
        }
        #np-close {
            top: 24px;
            right: 24px;
            width: 44px;
            height: 44px;
            border-radius: 50%;
        }
        #np-prev, #np-next {
            top: 50%;
            transform: translateY(-50%);
            width: 52px;
            height: 52px;
            border-radius: 50%;
        }
        #np-prev { left: 24px; }
        #np-next { right: 24px; }
        #np-prev:hover { transform: translateY(-50%) scale(1.06); }
        #np-next:hover { transform: translateY(-50%) scale(1.06); }

        /* ---------- Visionneuse image ---------- */
        #np-lightbox {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.96);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 3;
            padding: 40px;
        }
        #np-lightbox.is-open { display: flex; }
        #np-lightbox img {
            max-width: 90vw;
            max-height: 90vh;
            object-fit: contain;
            border-radius: 4px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6);
        }
        #np-lightbox-close {
            position: absolute;
            top: 24px;
            right: 24px;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ---------- Responsive ---------- */
        @media (max-width: 768px) {
            #np-prev { left: 8px; }
            #np-next { right: 8px; }
            #np-close { top: 14px; right: 14px; }
            #np-bottom { padding: 30px 22px 22px; }
            #np-thumbs img { width: 58px; height: 58px; }
            #np-text { max-height: 160px; }
        }
    </style>
    <?php
}

// -----------------------------------------------------------------------------
// 3) Markup HTML (injecte dans wp_footer)
// -----------------------------------------------------------------------------
add_action('wp_footer', 'news_player_render_markup');
function news_player_render_markup() {
    $rest_url = esc_url_raw(rest_url('news-player/v1/posts'));
    ?>
    <button id="np-fab" type="button" aria-label="Lire les articles en plein ecran">
        <svg viewBox="0 0 24 24" width="28" height="28" aria-hidden="true">
            <path fill="currentColor" d="M8 5v14l11-7z"/>
        </svg>
    </button>

    <div id="np-overlay" role="dialog" aria-modal="true" aria-hidden="true" aria-label="Diaporama d'articles">
        <button id="np-close" type="button" aria-label="Fermer">
            <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true">
                <path fill="currentColor" d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
            </svg>
        </button>

        <button id="np-prev" type="button" aria-label="Article precedent">
            <svg viewBox="0 0 24 24" width="32" height="32" aria-hidden="true">
                <path fill="currentColor" d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
            </svg>
        </button>

        <button id="np-next" type="button" aria-label="Article suivant">
            <svg viewBox="0 0 24 24" width="32" height="32" aria-hidden="true">
                <path fill="currentColor" d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
            </svg>
        </button>

        <div id="np-stage">
            <div id="np-bg"></div>
            <div id="np-bottom">
                <div id="np-meta"></div>
                <h2 id="np-title"></h2>
                <div id="np-text"></div>
                <div id="np-thumbs"></div>
                <a id="np-readmore" href="#" target="_blank" rel="noopener noreferrer">Lire l'article complet &rarr;</a>
            </div>
        </div>

        <div id="np-lightbox" aria-hidden="true">
            <button id="np-lightbox-close" type="button" aria-label="Fermer l'image">
                <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true">
                    <path fill="currentColor" d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                </svg>
            </button>
            <img id="np-lightbox-img" src="" alt="" />
        </div>
    </div>

    <script>
    (function () {
        var REST_URL = <?php echo wp_json_encode($rest_url); ?>;

        var fab          = document.getElementById('np-fab');
        var overlay      = document.getElementById('np-overlay');
        var closeBtn     = document.getElementById('np-close');
        var prevBtn      = document.getElementById('np-prev');
        var nextBtn      = document.getElementById('np-next');
        var bg           = document.getElementById('np-bg');
        var metaEl       = document.getElementById('np-meta');
        var titleEl      = document.getElementById('np-title');
        var textEl       = document.getElementById('np-text');
        var thumbsEl     = document.getElementById('np-thumbs');
        var readmore     = document.getElementById('np-readmore');
        var lightbox     = document.getElementById('np-lightbox');
        var lightboxImg  = document.getElementById('np-lightbox-img');
        var lightboxClose= document.getElementById('np-lightbox-close');

        var posts  = [];
        var current = 0;
        var loaded  = false;

        function fetchPosts() {
            if (loaded) return Promise.resolve();
            return fetch(REST_URL, { credentials: 'same-origin' })
                .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                .then(function (data) { posts = data || []; loaded = true; });
        }

        function render() {
            if (!posts.length) return;
            var p = posts[current];

            // Transition de fondu sur le fond
            bg.style.opacity = '0';
            setTimeout(function () {
                bg.style.backgroundImage = p.featured ? 'url("' + p.featured + '")' : 'none';
                bg.style.opacity = '1';
            }, 250);

            // Meta + titre
            metaEl.textContent  = (p.date || '') + (p.author ? '   ·   ' + p.author : '');
            titleEl.textContent = p.title || '';

            // Texte (extrait + contenu tronque)
            var html = '';
            if (p.excerpt) html += '<p>' + p.excerpt + '</p>';
            if (p.content) html += '<p>' + p.content + '</p>';
            textEl.innerHTML = html;

            // Lien "lire la suite"
            readmore.setAttribute('href', p.link || '#');

            // Miniatures : image vedette + images du contenu
            thumbsEl.innerHTML = '';
            var firstImg = null;
            if (p.featured) {
                firstImg = p.featured;
                thumbsEl.appendChild(buildThumb(p.featured));
            }
            (p.images || []).forEach(function (url) {
                if (!firstImg) firstImg = url;
                thumbsEl.appendChild(buildThumb(url));
            });
        }

        function buildThumb(url) {
            var img = document.createElement('img');
            img.src = url;
            img.alt = '';
            img.loading = 'lazy';
            img.addEventListener('click', function () { openLightbox(url); });
            return img;
        }

        function openLightbox(url) {
            lightboxImg.src = url;
            lightbox.classList.add('is-open');
            lightbox.setAttribute('aria-hidden', 'false');
        }
        function closeLightbox() {
            lightbox.classList.remove('is-open');
            lightbox.setAttribute('aria-hidden', 'true');
        }

        function open() {
            fetchPosts().then(function () {
                if (!posts.length) { alert('Aucun article a afficher.'); return; }
                current = 0;
                render();
                overlay.classList.add('is-open');
                overlay.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            }).catch(function () {
                alert('Impossible de charger les articles.');
            });
        }
        function close() {
            overlay.classList.remove('is-open');
            overlay.setAttribute('aria-hidden', 'true');
            closeLightbox();
            document.body.style.overflow = '';
        }
        function next() { if (posts.length) { current = (current + 1) % posts.length; render(); } }
        function prev() { if (posts.length) { current = (current - 1 + posts.length) % posts.length; render(); } }

        fab.addEventListener('click', open);
        closeBtn.addEventListener('click', close);
        nextBtn.addEventListener('click', next);
        prevBtn.addEventListener('click', prev);
        lightboxClose.addEventListener('click', closeLightbox);
        lightbox.addEventListener('click', function (e) {
            if (e.target === lightbox) closeLightbox();
        });

        // Navigation clavier
        document.addEventListener('keydown', function (e) {
            if (!overlay.classList.contains('is-open')) return;
            if (e.key === 'Escape') {
                if (lightbox.classList.contains('is-open')) closeLightbox();
                else close();
            } else if (e.key === 'ArrowRight' && !lightbox.classList.contains('is-open')) {
                next();
            } else if (e.key === 'ArrowLeft' && !lightbox.classList.contains('is-open')) {
                prev();
            }
        });

        // Navigation tactile (swipe)
        var startX = 0, startY = 0;
        overlay.addEventListener('touchstart', function (e) {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
        }, { passive: true });
        overlay.addEventListener('touchend', function (e) {
            var dx = e.changedTouches[0].clientX - startX;
            var dy = e.changedTouches[0].clientY - startY;
            if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy)) {
                if (dx < 0) next(); else prev();
            }
        }, { passive: true });
    })();
    </script>
    <?php
}
