/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/065__id-123__wp-shortcode-banniereauto.php
 * Display name: WP_Shortcode_banniereauto
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 123
 * Online modified: 2025-06-04 11:39:31
 * Online revision: 9
 * Exact duplicate group: non
 * Version family: WP_Shortcode_banniereauto (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/065__id-123__wp-shortcode-banniereauto.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: shortcode
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: cl_ban_auto_shortcode
 * Shortcodes: ban_auto
 * Lignes / octets (brut): 43 / 1367
 * Hash code normalise (sha256): d1a16784f3f0fabff352037bd1f84545a6b420df5babc494b3438694b253abbb
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: INACTIVE__global__wp-shortcode-banniereauto__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__wp-shortcode-banniereauto__v1__src-wp_snippets_online_current.php
 * Resume fonctionnalites: shortcode WordPress, 1 fonction(s) clef
 * Features detectees: shortcode
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: cl_ban_auto_shortcode
 * Shortcodes: ban_auto
 * APIs WP detectees: add_shortcode
 * Signatures contenu: html-markup
 * Lignes / octets: 57 / 1971
 * Empreinte code (sha256): e09854686c5f01b763d77ea0cdd1b291024b592e02af8d11e0fef89e913e16de
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: INACTIVE__global__wp-shortcode-banniereauto__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__wp-shortcode-banniereauto__v1__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: INACTIVE
 * Cluster principal: shortcode_preview
 * Clusters secondaires: aucun
 * Domaine: shortcode
 * Confiance: low
 * Scores (top): shortcode_preview=5, shortcode_other=2
 * Raisons principales: shortcode
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

function cl_ban_auto_shortcode($atts, $content = null) {
    if (!$content) return '';

    $liens = array_filter(array_map('trim', explode("\n", trim($content))));

    $output = '<div style="display:flex; flex-wrap: wrap; gap: 10px;">';

    foreach ($liens as $url) {
        $host = parse_url($url, PHP_URL_HOST) ?: $url;

        $output .= '<div style="
            width: 280px; height: 120px; 
            background: #222; color: #fff; 
            display: flex; align-items: center; justify-content: center; 
            border-radius: 8px; 
            box-shadow: 0 2px 6px rgba(0,0,0,0.5);
            font-weight: bold; font-size: 18px;
            ">
            <a href="' . esc_url($url) . '" target="_blank" rel="noopener" style="color: #fff; text-decoration: none;">
                ' . esc_html($host) . '
            </a>
        </div>';
    }

    $output .= '</div>';

    return $output;
}

add_shortcode('ban_auto', 'cl_ban_auto_shortcode');
