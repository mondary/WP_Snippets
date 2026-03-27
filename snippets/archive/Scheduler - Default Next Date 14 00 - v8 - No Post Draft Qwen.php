/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/094__id-154__post-default-next-date-without-post-draft-and-hour-14-00-qwen.php
 * Display name: POST - Default next date without post + draft and hour 14:00 🟢  qwen
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 154
 * Online modified: 2026-02-16 16:23:31
 * Online revision: 1
 * Exact duplicate group: non
 * Version family: POST - Default next date without post + draft and hour 14:00 🟢  qwen (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/094__id-154__post-default-next-date-without-post-draft-and-hour-14-00-qwen.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_insert_post_data
 * Fonctions clefs: set_default_post_time_14
 * Lignes / octets (brut): 59 / 2377
 * Hash code normalise (sha256): cd72f7088add0e9e366e13da0f79dcec0b969c4830694fe399ea45e57c8e97f5
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: post-default-next-date-without-post-draft-and-hour-14-00-qwen__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-default-next-date-without-post-draft-and-hour-14-00-qwen__v001.php
 * Resume fonctionnalites: automatisation date/programmation, 1 hook(s) WP, 1 fonction(s) clef
 * Features detectees: scheduler-date
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_insert_post_data
 * Fonctions clefs: set_default_post_time_14
 * APIs WP detectees: add_filter, get_posts, get_gmt_from_date
 * Signatures contenu: aucune signature notable
 * Lignes / octets: 72 / 3094
 * Empreinte code (sha256): 9463dce3952a6ec86a1f215f65bb464dc6210f148ccd87602d14b46cadd5aa61
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: post-default-next-date-without-post-draft-and-hour-14-00-qwen__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-default-next-date-without-post-draft-and-hour-14-00-qwen__v001.php
 * Bucket FINAL: archive
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