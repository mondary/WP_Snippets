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
