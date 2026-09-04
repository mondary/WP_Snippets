<?php
/*
 * Display name: TOOL 📊 TRACKING - Super Tracker - 2026.09.1
 * Scope: global
 */

if (!defined('SUPER_TRACKER_LOADED')) {
    define('SUPER_TRACKER_LOADED', true);

    /**
     * Umami — chargé en wp_head avec attributs obligatoires.
     * Open-source, self-hostable, privacy-friendly.
     */
    function super_tracker_inject_umami() {
        if (is_admin()) return;
        echo '<script defer src="https://cloud.umami.is/script.js" data-website-id="18410156-63da-42cf-b3bb-474c0d61f208" id="umami-tracker"></script>' . "\n";
    }
    add_action('wp_head', 'super_tracker_inject_umami', 999);

    /**
     * Trackers différés en footer — uniquement des services vérifiés.
     * Supprimé: datapulse.com (redirect suspect), histogram-analytics.com (404), swilty.com (mort)
     */
    function super_tracker_load_all() {
        if (is_admin()) return;
        add_action('wp_footer', 'super_tracker_footer_scripts', 999);
    }
    add_action('wp', 'super_tracker_load_all');

    function super_tracker_footer_scripts() {
        ?>
        <!-- SUPER TRACKER v2026.09.1 (audit: 3 trackers suspects supprimés) -->
        <script>
        (function() {
            'use strict';
            if (window.location.hostname === 'localhost' || window.location.hostname.indexOf('.test') !== -1) return;

            function loadTrackerAsync(url, id, attrs) {
                if (!url || document.getElementById(id)) return;
                var s = document.createElement('script');
                s.async = true; s.defer = true; s.src = url; s.id = id;
                if (attrs) Object.keys(attrs).forEach(function(k) { s.setAttribute(k, attrs[k]); });
                document.body.appendChild(s);
            }

            function initTrackers() {
                window.setTimeout(function() {
                    // Counter.dev — free analytics, legit
                    loadTrackerAsync('https://cdn.counter.dev/script.js', 'counter-dev');
                    // Rybbit — open-source analytics, legit
                    loadTrackerAsync('https://rybbit.com/js/tracker.js', 'rybbit-tracker');
                }, 500);
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initTrackers, { once: true });
            } else {
                initTrackers();
            }
        })();
        </script>
        <?php
    }
}
