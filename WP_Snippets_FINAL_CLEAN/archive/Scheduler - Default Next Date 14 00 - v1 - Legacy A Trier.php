/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: A TRIER
 * Source path: A TRIER/WP_POST Default next date and hour 14.00/WP_POST Default next date and hour 14.00.php
 * Display name: WP_POST Default next date and hour 14.00
 * Scope: front-end
 * Online snippet: non
 * Exact duplicate group: oui (e23e0cade15f…, 2 membres)
 * Canonical exact group ID: 24
 * Version family: DUP WP_POST - Default hours 14.00 (1 variantes)
 * Version: v1
 * Recommended latest in family: A TRIER/WP_POST Default next date and hour 14.00/WP_POST Default next date and hour 14.00.php
 * Is family latest: oui
 * Archive reasons: exact-duplicate
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_insert_post_data
 * Fonctions clefs: set_default_post_time_14
 * Lignes / octets (brut): 46 / 1870
 * Hash code normalise (sha256): e23e0cade15f9af2496c28aae7415e9a6cf1bc394ad8aa0a48c5142f17be63df
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: post-default-hours-14__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-default-hours-14__v001.php
 * Resume fonctionnalites: automatisation date/programmation, 1 hook(s) WP, 1 fonction(s) clef
 * Features detectees: scheduler-date
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_insert_post_data
 * Fonctions clefs: set_default_post_time_14
 * APIs WP detectees: add_filter, get_posts, get_gmt_from_date
 * Signatures contenu: aucune signature notable
 * Lignes / octets: 69 / 2849
 * Empreinte code (sha256): 6bffb5fde3af8d8f56cec6b5ea41f24dc1c1badbd8aee895e2b47e67922f1f3a
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: post-default-hours-14__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-default-hours-14__v001.php
 * Bucket FINAL: archive
 * Statut: LOCAL
 * Cluster principal: scheduler_posts
 * Clusters secondaires: aucun
 * Domaine: post-front
 * Confiance: high
 * Scores (top): scheduler_posts=12
 * Raisons principales: scheduler-date, schedule, 14.00
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

/**
 * Set default post time to 14:00 on the next available empty day
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
                // Check if any posts (published, scheduled, draft, or pending) exist on this entire day
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
                    'posts_per_page' => 1,
                    'fields'         => 'ids',
                ));
                
                if (empty($existing_posts)) {
                    $found_slot = true;
                    break;
                }
                
                // Move to next day
                $check_date = date('Y-m-d', strtotime($check_date . ' +1 day'));
            }
            
            // Set the post date to 14:00 on the found slot
            $data['post_date'] = $check_date . ' 14:00:00';
            $data['post_date_gmt'] = get_gmt_from_date($data['post_date']);
        }
        return $data;
    }
}
