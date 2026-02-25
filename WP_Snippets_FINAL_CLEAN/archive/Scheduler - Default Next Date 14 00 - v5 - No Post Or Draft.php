/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/092__id-152__post-next-without-post-or-draft.php
 * Display name: POST - next without post or draft
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 152
 * Online modified: 2026-02-14 15:48:07
 * Online revision: 1
 * Exact duplicate group: oui (de0e6a1164a2…, 2 membres)
 * Canonical exact group ID: 168
 * Version family: DUP POST - next without post or draft (1 variantes)
 * Version: v2
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/092__id-152__post-next-without-post-or-draft.php
 * Is family latest: oui
 * Archive reasons: exact-duplicate
 * Features: search-ui
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_insert_post_data
 * Fonctions clefs: set_default_post_time_14
 * Lignes / octets (brut): 70 / 2496
 * Hash code normalise (sha256): de0e6a1164a2c5bb2f22c8a3a99bfbf08ee276453d867465299d6d3a3195e34e
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: post-next-without-post-or-draft__v002.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-next-without-post-or-draft__v002.php
 * Resume fonctionnalites: interface de recherche, automatisation date/programmation, 1 hook(s) WP, 1 fonction(s) clef
 * Features detectees: search-ui, scheduler-date
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_insert_post_data
 * Fonctions clefs: set_default_post_time_14
 * APIs WP detectees: add_filter, get_posts, get_gmt_from_date
 * Signatures contenu: aucune signature notable
 * Lignes / octets: 84 / 3179
 * Empreinte code (sha256): 1121131322bbbdb3b1a9d8ebee689a726eb0b3eafc0576ffa67ddfc69643e270
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: post-next-without-post-or-draft__v002.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/post-next-without-post-or-draft__v002.php
 * Bucket FINAL: archive
 * Statut: INACTIVE
 * Cluster principal: search_ui
 * Clusters secondaires: scheduler_posts
 * Domaine: post-front
 * Confiance: medium
 * Scores (top): search_ui=10, scheduler_posts=8
 * Raisons principales: search-ui, search
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

/**
   * Set default post time to 14:00 on next weekday with no post/draft
   */
  if (!function_exists('set_default_post_time_14')) {
      add_filter('wp_insert_post_data', 'set_default_post_time_14', 10, 2);

      function set_default_post_time_14($data, $postarr) {
          // Only for new posts
          if (!empty($postarr['ID'])) {
              return $data;
          }

          $check_date = current_time('Y-m-d');
          $check_datetime = null;

          // Search up to 45 days ahead
          for ($i = 0; $i < 45; $i++) {
              // Skip weekends (6 = Saturday, 7 = Sunday)
              $day_of_week = (int) date('N', strtotime($check_date));
              if ($day_of_week >= 6) {
                  $check_date = date('Y-m-d', strtotime($check_date . ' +1 day'));
                  continue;
              }

              // Check if ANY post/draft already exists on that day
              $existing_items = get_posts(array(
                  'post_type'      => 'post',
                  'post_status'    => array('future', 'publish', 'draft'),
                  'date_query'     => array(
                      array(
                          'year'  => (int) date('Y', strtotime($check_date)),
                          'month' => (int) date('m', strtotime($check_date)),
                          'day'   => (int) date('d', strtotime($check_date)),
                      ),
                  ),
                  'posts_per_page' => 1,
                  'fields'         => 'ids',
              ));

              // Day is free: schedule at 14:00
              if (empty($existing_items)) {
                  $check_datetime = $check_date . ' 14:00:00';
                  break;
              }

              $check_date = date('Y-m-d', strtotime($check_date . ' +1 day'));
          }

          // Apply only if a slot was found
          if ($check_datetime !== null) {
              $data['post_date'] = $check_datetime;
              $data['post_date_gmt'] = get_gmt_from_date($check_datetime);
          }

          return $data;
      }
  }

