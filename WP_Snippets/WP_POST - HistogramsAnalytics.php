<?php
/**
 * Plugin Name: Umami Histograms Analytics
 * Description: Affiche un histogramme des 30 derniers jours (Umami share link) en bas de page ou via shortcode.
 * Version: 1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_footer', 'umami_histogram_render_footer');
add_shortcode('umami_histogram', 'umami_histogram_shortcode');

function umami_histogram_render_footer() {
    if (is_admin() || wp_doing_ajax()) {
        return;
    }
    echo umami_histogram_html();
}

function umami_histogram_shortcode($atts) {
    return umami_histogram_html($atts);
}

function umami_histogram_html($atts = array()) {
    $atts = shortcode_atts(
        array(
            'share_id' => 'MFVZW2UAgHyBSM3o',
            'region' => 'eu',
            'days' => 30,
            'metric' => 'sessions', // sessions = visites, pageviews = pages vues
            'height' => 30,
            'title' => '',
        ),
        $atts
    );

    $days = max(1, (int) $atts['days']);
    $metric = $atts['metric'] === 'pageviews' ? 'pageviews' : 'sessions';
    $metric_label = $metric === 'pageviews' ? 'Pages vues' : 'Visites';

    $cache_key = 'umami_histogram_' . md5($atts['share_id'] . $atts['region'] . $days . $metric);
    $cached = get_transient($cache_key);
    if ($cached !== false) {
        return $cached;
    }

    $share_url = 'https://cloud.umami.is/analytics/' . rawurlencode($atts['region']) . '/api/share/' . rawurlencode($atts['share_id']);
    $share_resp = wp_remote_get($share_url, array('timeout' => 10));

    if (is_wp_error($share_resp)) {
        return '<p>Histogramme indisponible.</p>';
    }

    $share_body = json_decode(wp_remote_retrieve_body($share_resp), true);
    if (empty($share_body['websiteId']) || empty($share_body['token'])) {
        return '<p>Histogramme indisponible.</p>';
    }

    $website_id = $share_body['websiteId'];
    $token = $share_body['token'];

    $end_at = (int) (microtime(true) * 1000);
    $start_at = $end_at - ($days * DAY_IN_SECONDS * 1000);

    $data_url = 'https://cloud.umami.is/analytics/' . rawurlencode($atts['region']) .
        '/api/websites/' . rawurlencode($website_id) .
        '/pageviews?startAt=' . $start_at . '&endAt=' . $end_at . '&unit=day';

    $data_resp = wp_remote_get(
        $data_url,
        array(
            'timeout' => 10,
            'headers' => array(
                'x-umami-share-token' => $token,
            ),
        )
    );

    if (is_wp_error($data_resp)) {
        return '<p>Histogramme indisponible.</p>';
    }

    $data_body = json_decode(wp_remote_retrieve_body($data_resp), true);
    $series = isset($data_body[$metric]) && is_array($data_body[$metric]) ? $data_body[$metric] : array();

    if (empty($series)) {
        return '<p>Aucune donnee disponible.</p>';
    }

    $max = 1;
    foreach ($series as $point) {
        $y = isset($point['y']) ? (int) $point['y'] : 0;
        if ($y > $max) {
            $max = $y;
        }
    }

    $title = esc_html($atts['title']);
    $height = max(80, (int) $atts['height']);

    ob_start();
    static $style_done = false;
    if (!$style_done) {
        $style_done = true;
        echo '<style>
.umami-histogram{position:fixed;left:50%;transform:translateX(-50%);bottom:0;width:90%;margin:0;padding:0;background:transparent;z-index:9999;}
.umami-histogram__title{display:none;}
.umami-histogram__bars{display:grid;grid-template-columns:repeat(auto-fit,minmax(6px,1fr));gap:4px;align-items:end;height:var(--umami-height);}
.umami-histogram__bar{display:block;width:100%;background:#3b82f6;border-radius:2px 2px 0 0;opacity:.2;transition:opacity .2s ease;position:relative;}
.umami-histogram__bar:hover{opacity:1;}
.umami-histogram__bar::after{content:attr(data-tooltip);position:absolute;bottom:100%;left:50%;transform:translate(-50%,-6px);background:rgba(0,0,0,.75);color:#fff;font-size:11px;line-height:1;padding:4px 6px;border-radius:4px;white-space:nowrap;opacity:0;pointer-events:none;transition:opacity .15s ease;z-index:5;}
.umami-histogram__bar:hover::after{opacity:1;}
</style>';
    }

    echo '<div class="umami-histogram" role="img" aria-label="">';
    echo '<div class="umami-histogram__bars" style="--umami-height:' . $height . 'px;">';

    $tz = wp_timezone();
    foreach ($series as $point) {
        $value = isset($point['y']) ? (int) $point['y'] : 0;
        $height_pct = (int) round(($value / $max) * 100);

        $label = '';
        if (!empty($point['x'])) {
            try {
                $dt = new DateTime($point['x'], new DateTimeZone('UTC'));
                $dt->setTimezone($tz);
                $label = $dt->format('Y-m-d');
            } catch (Exception $e) {
                $label = $point['x'];
            }
        }

        echo '<span class="umami-histogram__bar" style="height:' . $height_pct . '%" data-tooltip="' . esc_attr($label . ' : ' . $value . ' ' . $metric_label) . '"></span>';
    }

    echo '</div>';
    echo '</div>';

    $html = ob_get_clean();
    set_transient($cache_key, $html, 15 * MINUTE_IN_SECONDS);

    return $html;
}
