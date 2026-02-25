/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: WP_Snippets_Online_Current
 * Source path: WP_Snippets_Online_Current/inactive/global/093__id-153__post-default-next-date-without-post-draft-and-hour-14-00.php
 * Display name: POST - Default next date without post + draft and hour 14:00 🟢
 * Scope: global
 * Online snippet: oui
 * Online active: non
 * Online ID: 153
 * Online modified: 2026-02-16 16:14:23
 * Online revision: 1
 * Exact duplicate group: non
 * Version family: POST - Default next date without post + draft and hour 14:00 🟢 (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets_Online_Current/inactive/global/093__id-153__post-default-next-date-without-post-draft-and-hour-14-00.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_insert_post_data
 * Fonctions clefs: gg_find_next_free_business_day_14h, gg_force_post_slot_14h
 * Lignes / octets (brut): 110 / 3765
 * Hash code normalise (sha256): d59486aae913630420b98afb047ee82bf3908fd5dd14793b4fd72f85d7b74478
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURES-DESCRIPTION:START
 * Fichier: INACTIVE__global__post-default-next-date-without-post-draft-and-hour-14-00__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__post-default-next-date-without-post-draft-and-hour-14-00__v1__src-wp_snippets_online_current.php
 * Resume fonctionnalites: automatisation date/programmation, 1 hook(s) WP, 2 fonction(s) clef
 * Features detectees: scheduler-date
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_insert_post_data
 * Fonctions clefs: gg_find_next_free_business_day_14h, gg_force_post_slot_14h
 * APIs WP detectees: wp_timezone, get_var, add_filter, wp_is_post_revision, get_gmt_from_date
 * Signatures contenu: aucune signature notable
 * Lignes / octets: 122 / 4398
 * Empreinte code (sha256): 31aa0e766884d28d72f81eea458f2e537cb6ec998f25bfbd16c6a072feea7f19
 * Description generee le (UTC): 2026-02-24T16:39:50+00:00
 * CLM-FEATURES-DESCRIPTION:END */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: INACTIVE__global__post-default-next-date-without-post-draft-and-hour-14-00__v1__src-wp_snippets_online_current.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/INACTIVE__global__post-default-next-date-without-post-draft-and-hour-14-00__v1__src-wp_snippets_online_current.php
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
   * Force les articles à 14:00 (heure WP) au prochain jour ouvré
   * sans aucun article/brouillon ce jour-là.
   */
  if (!function_exists('gg_find_next_free_business_day_14h')) {
      function gg_find_next_free_business_day_14h($exclude_post_id = 0) {
          global $wpdb;

          $tz  = wp_timezone();
          $now = new DateTimeImmutable('now', $tz);

          $candidate = $now->setTime(14, 0, 0);
          if ($candidate <= $now) {
              $candidate = $candidate->modify('+1 day');
          }

          $statuses = array('publish', 'future', 'draft', 'pending', 'private', 'auto-draft');
          $in_sql   = implode(',', array_fill(0, count($statuses), '%s'));

          for ($i = 0; $i < 180; $i++) {
              // 1=lundi ... 7=dimanche
              if ((int) $candidate->format('N') >= 6) {
                  $candidate = $candidate->modify('+1 day');
                  continue;
              }

              $day_start = $candidate->setTime(0, 0, 0)->format('Y-m-d H:i:s');
              $day_end   = $candidate->setTime(23, 59, 59)->format('Y-m-d H:i:s');

              $sql = "
                  SELECT ID
                  FROM {$wpdb->posts}
                  WHERE post_type = 'post'
                    AND post_status IN ($in_sql)
                    AND post_date BETWEEN %s AND %s
              ";

              $params = array_merge($statuses, array($day_start, $day_end));

              if ($exclude_post_id > 0) {
                  $sql .= " AND ID <> %d";
                  $params[] = $exclude_post_id;
              }

              $sql .= " LIMIT 1";
              $found = $wpdb->get_var($wpdb->prepare($sql, $params));

              if (empty($found)) {
                  return $candidate->setTime(14, 0, 0);
              }

              $candidate = $candidate->modify('+1 day');
          }

          return null;
      }
  }

  if (!function_exists('gg_force_post_slot_14h')) {
      add_filter('wp_insert_post_data', 'gg_force_post_slot_14h', 9999, 2);

      function gg_force_post_slot_14h($data, $postarr) {
          // Seulement les posts
          if (($data['post_type'] ?? '') !== 'post') {
              return $data;
          }

          // Ignore autosave/révisions
          if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
              return $data;
          }
          $post_id = !empty($postarr['ID']) ? (int) $postarr['ID'] : 0;
          if ($post_id && wp_is_post_revision($post_id)) {
              return $data;
          }

          $slot = gg_find_next_free_business_day_14h($post_id);
          if (!$slot) {
              return $data;
          }

          $slot_local = $slot->format('Y-m-d H:i:s');
          $data['post_date'] = $slot_local;
          $data['post_date_gmt'] = get_gmt_from_date($slot_local);

          // Si action de publication et créneau futur => future (pas immédiat)
          if (in_array(($data['post_status'] ?? ''), array('publish', 'future'), true)) {
              $now_local = current_time('timestamp');
              if (strtotime($slot_local) > $now_local) {
                  $data['post_status'] = 'future';
              }
          }

          return $data;
      }
  }