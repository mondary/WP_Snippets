/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/037__id-89__post-external-links-conf.php
 * Display name: POST - External links conf
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 89
 * Online modified: 2025-03-12 13:38:23
 * Online revision: 6
 * Exact duplicate group: non
 * Version family: POST - External links conf (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/037__id-89__post-external-links-conf.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: head-injection, footer-injection
 * Dependances probables: jQuery
 * Hooks WP: admin_footer, wp_head
 * Fonctions clefs: add_external_link_settings, isExternalUrl, add_external_link_styles
 * Lignes / octets (brut): 148 / 6154
 * Hash code normalise (sha256): e2ae09f1414bb3cd502d7b62d0e4609499a93b0870acb3f81882036168d60389
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: INACTIVE__global__post-external-links-conf__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__post-external-links-conf__v1__src-wp_snippets_online_current.php
 * Resume fonctionnalites: customisation interface admin, integration Gutenberg, UI frontend (CSS/HTML), automatisation date/programmation, 2 hook(s) WP, 3 fonction(s) clef
 * Features detectees: gutenberg, admin-ui, scheduler-date, css-ui, footer-head-injection, svg-ui
 * Dependances probables: jQuery, Gutenberg JS
 * Hooks WP: admin_footer, wp_head
 * Fonctions clefs: add_external_link_settings, isExternalUrl, add_external_link_styles
 * Selecteurs / IDs: #wp-link, #wp-link-url, #wp-link-new_tab, #wp-link-ref_param, .link-target, #wp-link-nofollow, #wp-link-sponsored, #wp-link-ugc
 * APIs WP detectees: add_external_link_settings, add_action, add_external_link_styles
 * Signatures contenu: inline-style, inline-script, html-markup
 * Lignes / octets: 161 / 6803
 * Empreinte code (sha256): 7947f8dbb8cb5829481205035e464c43f7f25c328b41dd515752d63747781f13
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: INACTIVE__global__post-external-links-conf__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__post-external-links-conf__v1__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: INACTIVE
 * Cluster principal: admin_ui_settings
 * Clusters secondaires: scheduler_posts, gutenberg_editor, frontend_ui_widget, links_external, post_footer_ui
 * Domaine: admin
 * Confiance: medium
 * Scores (top): admin_ui_settings=8, scheduler_posts=8, gutenberg_editor=6, frontend_ui_widget=6, links_external=5, post_footer_ui=5
 * Raisons principales: admin-ui, settings
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

/*
Plugin Name: Configurable External Links
Description: Add configurable attributes to external links with WordPress link dialog options
Version: 1.0
Author: Mondary
*/

if (!defined('ABSPATH')) exit;

// Add settings to WordPress link dialog and Gutenberg
function add_external_link_settings() {
    global $pagenow;
    
    // For Classic Editor
    if ($pagenow !== 'post-new.php' && $pagenow !== 'post.php') {
        ?>
        <script>
        jQuery(document).ready(function($) {
            // Add custom panel to link dialog
            if (typeof wpLink !== 'undefined') {
                var $wpLinkDialog = $('#wp-link');
                var $settingsDiv = $('<div id="link-external-options" class="link-external-options">');
                var $settingsPanel = $('<div class="link-settings">');
                
                // Create toggle switches with default states for external links
                var toggles = {
                    'new_tab': 'Open in new tab',
                    'nofollow': 'Add "nofollow"',
                    'sponsored': 'Add "sponsored"',
                    'ugc': 'Add "ugc"',
                    'ref_param': 'Add "mondary.design" to link'
                };
                
                // Function to check if URL is external
                function isExternalUrl(url) {
                    if (!url) return false;
                    return url.startsWith('http') && !url.includes(window.location.hostname);
                }
                
                Object.entries(toggles).forEach(([key, label]) => {
                    var $toggle = $('<div class="link-setting">' +
                        '<label><input type="checkbox" id="wp-link-' + key + '" /> ' + label + '</label>' +
                        '</div>');
                    $settingsPanel.append($toggle);
                });
                
                // Add URL change listener to auto-check options for external links
                $('#wp-link-url').on('change input', function() {
                    var url = $(this).val();
                    if (isExternalUrl(url)) {
                        $('#wp-link-new_tab').prop('checked', true);
                        $('#wp-link-ref_param').prop('checked', true);
                    }
                });
                
                $settingsDiv.append($settingsPanel);
                $wpLinkDialog.find('.link-target').after($settingsDiv);
                
                // Style the options
                $('<style>').text(`.link-external-options {
                    padding: 10px 0;
                    border-top: 1px solid #ddd;
                }
                .link-setting {
                    margin: 5px 0;
                }
                .link-setting label {
                    display: flex;
                    align-items: center;
                    gap: 5px;
                }`).appendTo('head');
                
                // Handle link updates
                var originalUpdate = wpLink.update;
                wpLink.update = function() {
                    var result = originalUpdate.apply(this, arguments);
                    var $link = $(document.activeElement).closest('a');
                    if ($link.length && $link.attr('href') && $link.attr('href').startsWith('http') && 
                        !$link.attr('href').includes(window.location.hostname)) {
                        
                        // Apply selected options
                        if ($('#wp-link-new_tab').is(':checked')) {
                            $link.attr('target', '_blank');
                        }
                        
                        var relAttr = [];
                        if ($('#wp-link-nofollow').is(':checked')) relAttr.push('nofollow');
                        if ($('#wp-link-sponsored').is(':checked')) relAttr.push('sponsored');
                        if ($('#wp-link-ugc').is(':checked')) relAttr.push('ugc');
                        if ($('#wp-link-new_tab').is(':checked')) relAttr.push('noopener');
                        
                        if (relAttr.length) {
                            $link.attr('rel', relAttr.join(' '));
                        }
                        
                        if ($('#wp-link-ref_param').is(':checked')) {
                            var href = $link.attr('href');
                            var newHref = href + (href.includes('?') ? '&' : '?') + 'ref=mondary.design';
                            $link.attr('href', newHref);
                        }
                        
                        $link.addClass('external-link');
                    }
                    return result;
                };
            }
        });
        </script>
        <?php
    }
}
add_action('admin_footer', 'add_external_link_settings');

// Add frontend styles for external links
function add_external_link_styles() {
    ?>
    <style>
    .external-link {
        position: relative;
    }
    .external-link::after {
        content: "";
        display: inline-block;
        width: 12px;
        height: 12px;
        margin-left: 4px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6'%3E%3C/path%3E%3Cpolyline points='15 3 21 3 21 9'%3E%3C/polyline%3E%3Cline x1='10' y1='14' x2='21' y2='3'%3E%3C/line%3E%3C/svg%3E");
        background-size: contain;
        background-repeat: no-repeat;
        vertical-align: middle;
    }
    </style>
    <?php
}
add_action('wp_head', 'add_external_link_styles');