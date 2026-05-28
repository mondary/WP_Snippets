<?php
/*
 * Display name: 📊 SUPER TRACKER OPTIMISÉ
 * Source: WordPress (pulled)
 * Online ID: 248
 * Online modified: 2026-03-19 14:03:11
 * Scope: global
 * Active: oui
 */

/**
 * Snippet Name: 📊 SUPER TRACKER OPTIMISÉ - Corrigé
 * Snippet Description: Version corrigée du super tracker. Umami reste dans le snippet unique, mais avec un traitement dédié compatible avec son mode de fonctionnement.
 * Snippet Version: 1.1
 * Author: Claude AI
 *
 * OBJECTIF:
 * - Garder un seul snippet
 * - Préserver les gains de perf pour les trackers compatibles
 * - Restaurer le tracking Umami
 *
 * IMPORTANT:
 * - Désactivez l'ancien snippet Umami individuel si vous utilisez celui-ci
 * - Vérifiez les URLs réelles des autres trackers avant mise en prod
+ */

if (!defined('SUPER_TRACKER_LOADED')) {
    define('SUPER_TRACKER_LOADED', true);

    /**
     * Umami doit etre charge tot avec ses attributs obligatoires.
     * On le garde dans le super tracker, mais hors du chargeur generique.
     */
    function super_tracker_inject_umami() {
        if (is_admin()) {
            return;
        }

        echo '<script defer src="https://cloud.umami.is/script.js" data-website-id="18410156-63da-42cf-b3bb-474c0d61f208" id="umami-tracker"></script>' . "\n";
    }
    add_action('wp_head', 'super_tracker_inject_umami', 999);

    /**
     * Charge les autres trackers uniquement sur le front-end.
     */
    function super_tracker_load_all() {
        if (is_admin()) {
            return;
        }

        add_action('wp_footer', 'super_tracker_footer_scripts', 999);
    }
    add_action('wp', 'super_tracker_load_all');

    /**
     * Affiche les trackers compatibles avec un chargement differe.
     */
    function super_tracker_footer_scripts() {
        ?>
        <!-- SUPER TRACKER OPTIMISE v1.1 -->
        <script>
        (function() {
            'use strict';

            if (
                window.location.hostname === 'localhost' ||
                window.location.hostname.indexOf('.test') !== -1
            ) {
                console.log('Super Tracker: skipped on localhost/test');
                return;
            }

            function loadTrackerAsync(url, id, attributes, callback) {
                if (!url || document.getElementById(id)) {
                    return;
                }

                var script = document.createElement('script');
                script.async = true;
                script.defer = true;
                script.src = url;
                script.id = id;

                if (attributes) {
                    Object.keys(attributes).forEach(function(key) {
                        script.setAttribute(key, attributes[key]);
                    });
                }

                if (callback) {
                    script.onload = callback;
                    script.onerror = callback;
                }

                document.body.appendChild(script);
            }

            function initTrackers() {
                window.setTimeout(function() {
                    // ===== COUNTER =====
                    // Remplacez l'URL par votre vraie URL si necessaire.
                    loadTrackerAsync('https://cdn.counter.dev/script.js', 'counter-dev');

                    // ===== DATAPULSE =====
                    // Remplacez l'URL par votre vraie URL si necessaire.
                    loadTrackerAsync('https://datapulse.com/js/tracker.js', 'datapulse-tracker');

                    // ===== HISTOGRAM ANALYTICS =====
                    // Remplacez l'URL par votre vraie URL si necessaire.
                    loadTrackerAsync('https://histogram-analytics.com/js/tracker.js', 'histogram-tracker');

                    // ===== RYBBIT =====
                    // Remplacez l'URL par votre vraie URL si necessaire.
                    loadTrackerAsync('https://rybbit.com/js/tracker.js', 'rybbit-tracker');

                    // ===== SWILTY =====
                    // Remplacez l'URL par votre vraie URL si necessaire.
                    loadTrackerAsync('https://swilty.com/js/tracker.js', 'swilty-tracker');

                    console.log('Super Tracker: deferred trackers loaded');
                }, 500);
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initTrackers, { once: true });
            } else {
                initTrackers();
            }
        })();
        </script>
        <!-- FIN SUPER TRACKER OPTIMISE -->
        <?php
    }

    /**
     * Evenements personnalises optionnels.
     */
    function super_tracker_custom_events() {
        if (is_admin()) {
            return;
        }
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var downloadLinks = document.querySelectorAll('a[href$=".pdf"], a[href$=".zip"]');
            downloadLinks.forEach(function(link) {
                link.addEventListener('click', function() {
                    console.log('Download tracked:', this.href);
                });
            });

            var externalLinks = document.querySelectorAll(
                'a[href^="http"]:not([href*="' + window.location.hostname + '"])'
            );
            externalLinks.forEach(function(link) {
                link.addEventListener('click', function() {
                    console.log('External link tracked:', this.href);
                });
            });
        });
        </script>
        <?php
    }
    add_action('wp_footer', 'super_tracker_custom_events', 998);
}
