/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/005__id-10__post-default-next-date-and-hour-14-00.php
 * Display name: POST - Default next date and hour 14:00 🟡
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 10
 * Online modified: 2026-02-16 16:23:06
 * Online revision: 7
 * Exact duplicate group: oui (5527709d4aad…, 2 membres)
 * Canonical exact group ID: 120
 * Version family: DUP POST - Default next date and hour 14:00 🟡 (1 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/005__id-10__post-default-next-date-and-hour-14-00.php
 * Is family latest: oui
 * Canonical reasons: exact-group-canonical
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_insert_post_data
 * Fonctions clefs: set_default_post_time_14
 * Lignes / octets (brut): 71 / 2794
 * Hash code normalise (sha256): 5527709d4aadb5a033507b0af4ad6a0e509a30b963a6537f4a55823215118c90
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: INACTIVE__global__post-default-next-date-and-hour-14-00__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__post-default-next-date-and-hour-14-00__v2__src-wp_snippets_online_current.php
 * Resume fonctionnalites: automatisation date/programmation, 1 hook(s) WP, 1 fonction(s) clef
 * Features detectees: scheduler-date
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_insert_post_data
 * Fonctions clefs: set_default_post_time_14
 * APIs WP detectees: add_filter, get_posts, get_gmt_from_date
 * Signatures contenu: aucune signature notable
 * Lignes / octets: 84 / 3379
 * Empreinte code (sha256): 7d9a5356c8a30121fe4ce1ee7588d10f9e4b31094327a18a521bc45363068661
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: INACTIVE__global__post-default-next-date-and-hour-14-00__v2__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__post-default-next-date-and-hour-14-00__v2__src-wp_snippets_online_current.php
 * Bucket FINAL: canonical
 * Statut: INACTIVE
 * Cluster principal: scheduler_posts
 * Clusters secondaires: aucun
 * Domaine: post-front
 * Confiance: high
 * Scores (top): scheduler_posts=12
 * Raisons principales: scheduler-date, schedule, 14:00
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

/**
 * Set default post time to 14:00 on the next available weekday
 */
if (!function_exists('set_default_post_time_14')) {
    add_filter('wp_insert_post_data', 'set_default_post_time_14', 10, 2);

    function set_default_post_time_14($data, $postarr) {
        // Only modify if this is a new post
        if (empty($postarr['ID'])) {
            // Start with today's date
            $check_date = current_time('Y-m-d');
            $found_slot = false;
            
            // Look up to 45 days ahead to find an available slot
            for ($i = 0; $i < 45; $i++) {
                // Skip weekends
                $day_of_week = date('N', strtotime($check_date));
                if ($day_of_week >= 6) {
                    $check_date = date('Y-m-d', strtotime($check_date . ' +1 day'));
                    continue;
                }

                // Create the full datetime string for 14:00
                $check_datetime = $check_date . ' 14:00:00';
                
                // Check if any posts are scheduled for this time
                $existing_posts = get_posts(array(
                    'post_type' => 'post',
                    'post_status' => array('future', 'publish'),
                    'date_query' => array(
                        array(
                            'year' => date('Y', strtotime($check_datetime)),
                            'month' => date('m', strtotime($check_datetime)),
                            'day' => date('d', strtotime($check_datetime)),
                            'hour' => 14,
                            'minute' => 0,
                        ),
                    ),
                    'posts_per_page' => 1,
                ));
                
                if (empty($existing_posts)) {
                    $found_slot = true;
                    break;
                }
                
                // Move to next day
                $check_date = date('Y-m-d', strtotime($check_date . ' +1 day'));
            }
            
            // Set the post date to the found slot
            $data['post_date'] = $check_datetime;
            $data['post_date_gmt'] = get_gmt_from_date($data['post_date']);
        }
        return $data;
    }
}
