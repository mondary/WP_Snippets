<?php
/* CLM-CREATED-AT: 2026-02-25 */
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/094__id-154__post-default-next-date-without-post-draft-and-hour-14-00-qwen.php
 * Display name: POST - Default next date without post + draft and hour 10-14 🟢  priority
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 154
 * Online modified: 2026-02-16 16:23:31
 * Online revision: 1
 * Exact duplicate group: non
 * Version family: POST - Default next date without post + draft and hour 10-14 🟢  priority (1 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/094__id-154__post-default-next-date-without-post-draft-and-hour-14-00-qwen.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_insert_post_data
 * Fonctions clefs: set_default_post_time_priority
 * Changelog v2: Priorité horaire 10h/14h puis 11h/12h/13h pour meilleure répartition
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_insert_post_data
 * Fonctions clefs: set_default_post_time_priority
 * Lignes / octets (brut): 72 / 3200
 * Hash code normalise (sha256): cd72f7088add0e9e366e13da0f79dcec0b969c4830694fe399ea45e57c8e97f5
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/**
 * Set default post time with priority: 10h, 14h, puis 11h, 12h, 13h
 */
if (!function_exists('set_default_post_time_priority')) {
    add_filter('wp_insert_post_data', 'set_default_post_time_priority', 10, 2);

    function set_default_post_time_priority($data, $postarr) {
        // Only modify if this is a new post
        if (empty($postarr['ID'])) {
            // Start with today's date
            $check_date = current_time('Y-m-d');
            $found_slot = false;

            // Look up to 45 days ahead to find an available slot
            for ($i = 0; $i < 45; $i++) {
                // Check how many posts exist on this day
                $existing_posts = get_posts(array(
                    'post_type'   => 'post',
                    'post_status' => array('publish', 'future', 'draft', 'pending'),
                    'date_query'  => array(
                        array(
                            'year'  => date('Y', strtotime($check_date)),
                            'month' => date('m', strtotime($check_date)),
                            'day'   => date('d', strtotime($check_date)),
                        ),
                    ),
                    'posts_per_page' => -1, // Get ALL posts for this day
                    'fields'         => 'ids',
                ));

                $post_count = count($existing_posts);

                // Priority order: 10h, 14h, 11h, 12h, 13h
                $priority_hours = array(10, 14, 11, 12, 13);

                // Find first available hour (or use 10h as default if all 5 are taken)
                $assigned_hour = 10; // Default fallback
                foreach ($priority_hours as $hour) {
                    $hour_is_taken = false;
                    foreach ($existing_posts as $post_id) {
                        $post_date = get_post_field('post_date', $post_id);
                        $post_hour = date('H', strtotime($post_date));
                        if ($post_hour == sprintf('%02d', $hour)) {
                            $hour_is_taken = true;
                            break;
                        }
                    }

                    if (!$hour_is_taken) {
                        $assigned_hour = $hour;
                        break;
                    }
                }

                // Set the post date to the assigned hour on the found slot
                $data['post_date'] = $check_date . ' ' . sprintf('%02d', $assigned_hour) . ':00:00';
                $data['post_date_gmt'] = get_gmt_from_date($data['post_date']);
                $found_slot = true;
                break;
            }
        }
        return $data;
    }
}
