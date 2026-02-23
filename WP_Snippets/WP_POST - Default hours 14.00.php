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
