<?php
/**
 * PKpremium admin.
 */

if (!defined('ABSPATH')) {
    exit;
}

function pkpremium_get_default_settings() {
    return array(
        'paypal_sandbox_enabled' => 1,
        'subscription_page_url' => home_url('/abonnement/'),
        'member_login_slug' => 'connexion',
        'member_register_slug' => 'inscription',
        'member_lostpassword_slug' => 'mot-de-passe-oublie',
        'block_default_wp_login' => 1,
        'paypal_live_client_id' => '',
        'paypal_live_secret' => '',
        'paypal_sandbox_client_id' => '',
        'paypal_sandbox_secret' => '',
        'paypal_live_webhook_id' => '',
        'paypal_sandbox_webhook_id' => '',
        'paypal_last_test' => array(),
        'paypal_last_verification' => array(),
    );
}

function pkpremium_register_default_settings() {
    $settings = get_option(PKPREMIUM_OPTION_KEY, array());
    $settings = wp_parse_args(is_array($settings) ? $settings : array(), pkpremium_get_default_settings());
    update_option(PKPREMIUM_OPTION_KEY, $settings);
}

function pkpremium_get_settings() {
    $settings = get_option(PKPREMIUM_OPTION_KEY, array());

    return wp_parse_args(is_array($settings) ? $settings : array(), pkpremium_get_default_settings());
}

function pkpremium_update_settings($new_settings) {
    $settings = wp_parse_args($new_settings, pkpremium_get_settings());
    update_option(PKPREMIUM_OPTION_KEY, $settings);
}

function pkpremium_get_rest_webhook_url() {
    return rest_url('pkpremium/v1/paypal/webhook');
}

function pkpremium_get_subscription_url() {
    $settings = pkpremium_get_settings();

    return !empty($settings['subscription_page_url']) ? esc_url_raw($settings['subscription_page_url']) : home_url('/');
}

function pkpremium_get_member_urls() {
    $settings = pkpremium_get_settings();
    $login_slug = sanitize_title($settings['member_login_slug']);
    $register_slug = sanitize_title($settings['member_register_slug']);
    $lostpassword_slug = sanitize_title($settings['member_lostpassword_slug']);

    return array(
        'login' => home_url('/' . $login_slug . '/'),
        'register' => home_url('/' . $register_slug . '/'),
        'lostpassword' => home_url('/' . $lostpassword_slug . '/'),
    );
}

function pkpremium_get_paypal_mode($settings = null) {
    $settings = is_array($settings) ? $settings : pkpremium_get_settings();

    return !empty($settings['paypal_sandbox_enabled']) ? 'sandbox' : 'live';
}

function pkpremium_get_paypal_base_url($mode = null) {
    $mode = $mode ?: pkpremium_get_paypal_mode();

    return $mode === 'sandbox' ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';
}

function pkpremium_get_paypal_credentials($settings = null, $mode = null) {
    $settings = is_array($settings) ? $settings : pkpremium_get_settings();
    $mode = $mode ?: pkpremium_get_paypal_mode($settings);

    if ($mode === 'sandbox') {
        return array(
            'client_id' => (string) $settings['paypal_sandbox_client_id'],
            'secret' => (string) $settings['paypal_sandbox_secret'],
            'webhook_id' => (string) $settings['paypal_sandbox_webhook_id'],
        );
    }

    return array(
        'client_id' => (string) $settings['paypal_live_client_id'],
        'secret' => (string) $settings['paypal_live_secret'],
        'webhook_id' => (string) $settings['paypal_live_webhook_id'],
    );
}

function pkpremium_get_paypal_webhook_events() {
    return array(
        array('name' => 'BILLING.SUBSCRIPTION.ACTIVATED'),
        array('name' => 'BILLING.SUBSCRIPTION.CANCELLED'),
        array('name' => 'BILLING.SUBSCRIPTION.SUSPENDED'),
        array('name' => 'BILLING.SUBSCRIPTION.EXPIRED'),
        array('name' => 'PAYMENT.SALE.COMPLETED'),
    );
}

function pkpremium_paypal_request_access_token($force_refresh = false, $settings = null, $mode = null) {
    $settings = is_array($settings) ? $settings : pkpremium_get_settings();
    $mode = $mode ?: pkpremium_get_paypal_mode($settings);
    $credentials = pkpremium_get_paypal_credentials($settings, $mode);
    $transient_key = 'pkpremium_paypal_access_token_' . $mode;

    if (!$force_refresh) {
        $cached = get_transient($transient_key);
        if (is_array($cached) && !empty($cached['token'])) {
            return $cached;
        }
    }

    if ($credentials['client_id'] === '' || $credentials['secret'] === '') {
        return new WP_Error('pkpremium_missing_paypal_credentials', 'Identifiants PayPal manquants.');
    }

    $response = wp_remote_post(
        pkpremium_get_paypal_base_url($mode) . '/v1/oauth2/token',
        array(
            'timeout' => 30,
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode($credentials['client_id'] . ':' . $credentials['secret']),
                'Accept' => 'application/json',
                'Accept-Language' => 'en_US',
            ),
            'body' => array(
                'grant_type' => 'client_credentials',
            ),
        )
    );

    if (is_wp_error($response)) {
        return $response;
    }

    $code = (int) wp_remote_retrieve_response_code($response);
    $body = json_decode((string) wp_remote_retrieve_body($response), true);

    if ($code < 200 || $code >= 300 || !is_array($body) || empty($body['access_token'])) {
        return new WP_Error('pkpremium_paypal_token_failed', 'Impossible de recuperer le token PayPal.', array(
            'http_code' => $code,
            'body' => $body,
        ));
    }

    $token = array(
        'token' => $body['access_token'],
        'expires_in' => isset($body['expires_in']) ? (int) $body['expires_in'] : 0,
        'scope' => isset($body['scope']) ? (string) $body['scope'] : '',
        'mode' => $mode,
        'fetched_at' => current_time('mysql'),
    );

    $ttl = max(60, ((int) $token['expires_in']) - 120);
    set_transient($transient_key, $token, $ttl);

    return $token;
}

function pkpremium_paypal_api_request($method, $path, $payload = null, $settings = null, $mode = null) {
    $token = pkpremium_paypal_request_access_token(false, $settings, $mode);
    if (is_wp_error($token)) {
        return $token;
    }

    $args = array(
        'method' => strtoupper($method),
        'timeout' => 30,
        'headers' => array(
            'Authorization' => 'Bearer ' . $token['token'],
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ),
    );

    if ($payload !== null) {
        $args['body'] = wp_json_encode($payload);
    }

    $response = wp_remote_request(pkpremium_get_paypal_base_url($mode ?: $token['mode']) . $path, $args);
    if (is_wp_error($response)) {
        return $response;
    }

    return array(
        'code' => (int) wp_remote_retrieve_response_code($response),
        'body' => json_decode((string) wp_remote_retrieve_body($response), true),
    );
}

function pkpremium_sync_paypal_webhook($settings = null, $mode = null) {
    $settings = is_array($settings) ? $settings : pkpremium_get_settings();
    $mode = $mode ?: pkpremium_get_paypal_mode($settings);
    $credentials = pkpremium_get_paypal_credentials($settings, $mode);
    $webhook_url = pkpremium_get_rest_webhook_url();

    $list_response = pkpremium_paypal_api_request('GET', '/v1/notifications/webhooks', null, $settings, $mode);
    if (is_wp_error($list_response)) {
        return $list_response;
    }

    $existing_webhook = null;
    if (!empty($list_response['body']['webhooks']) && is_array($list_response['body']['webhooks'])) {
        foreach ($list_response['body']['webhooks'] as $webhook) {
            if (!empty($webhook['url']) && untrailingslashit($webhook['url']) === untrailingslashit($webhook_url)) {
                $existing_webhook = $webhook;
                break;
            }
        }
    }

    if ($existing_webhook) {
        $webhook_id = isset($existing_webhook['id']) ? (string) $existing_webhook['id'] : '';
        $update_response = pkpremium_paypal_api_request(
            'PATCH',
            '/v1/notifications/webhooks/' . rawurlencode($webhook_id),
            array(
                array(
                    'op' => 'replace',
                    'path' => '/event_types',
                    'value' => pkpremium_get_paypal_webhook_events(),
                ),
            ),
            $settings,
            $mode
        );

        if (is_wp_error($update_response)) {
            return $update_response;
        }

        if ($update_response['code'] < 200 || $update_response['code'] >= 300) {
            return new WP_Error('pkpremium_paypal_webhook_update_failed', 'Impossible de mettre a jour le webhook PayPal.', $update_response);
        }

        return array(
            'action' => 'updated',
            'webhook_id' => $webhook_id,
            'response' => $update_response,
        );
    }

    $create_response = pkpremium_paypal_api_request(
        'POST',
        '/v1/notifications/webhooks',
        array(
            'url' => $webhook_url,
            'event_types' => pkpremium_get_paypal_webhook_events(),
        ),
        $settings,
        $mode
    );

    if (is_wp_error($create_response)) {
        return $create_response;
    }

    if ($create_response['code'] < 200 || $create_response['code'] >= 300 || empty($create_response['body']['id'])) {
        return new WP_Error('pkpremium_paypal_webhook_create_failed', 'Impossible de creer le webhook PayPal.', $create_response);
    }

    return array(
        'action' => 'created',
        'webhook_id' => (string) $create_response['body']['id'],
        'response' => $create_response,
    );
}

function pkpremium_find_email_in_array($value) {
    if (!is_array($value)) {
        return '';
    }

    foreach ($value as $key => $item) {
        if (is_string($key) && in_array($key, array('email', 'email_address'), true) && is_string($item) && is_email($item)) {
            return $item;
        }

        if (is_array($item)) {
            $email = pkpremium_find_email_in_array($item);
            if ($email !== '') {
                return $email;
            }
        }
    }

    return '';
}

function pkpremium_get_paypal_event_email($payload) {
    if (!is_array($payload)) {
        return '';
    }

    return pkpremium_find_email_in_array($payload);
}

function pkpremium_get_processed_webhook_ids() {
    $ids = get_option('pkpremium_processed_webhook_ids', array());
    return is_array($ids) ? $ids : array();
}

function pkpremium_mark_webhook_processed($event_id) {
    $event_id = (string) $event_id;
    if ($event_id === '') {
        return;
    }

    $ids = pkpremium_get_processed_webhook_ids();
    $ids[$event_id] = current_time('mysql');
    if (count($ids) > 200) {
        $ids = array_slice($ids, -200, null, true);
    }
    update_option('pkpremium_processed_webhook_ids', $ids, false);
}

function pkpremium_is_webhook_processed($event_id) {
    $ids = pkpremium_get_processed_webhook_ids();
    return $event_id !== '' && isset($ids[$event_id]);
}

function pkpremium_grant_premium_to_user(WP_User $user) {
    $user->add_role(PKPREMIUM_FUTURE_SITE_PREMIUM_ROLE);
    if (defined('PKPREMIUM_FUTURE_SITE_DEFAULT_CAP')) {
        $user->add_cap(PKPREMIUM_FUTURE_SITE_DEFAULT_CAP);
    }
}

function pkpremium_revoke_premium_from_user(WP_User $user) {
    $user->remove_role(PKPREMIUM_FUTURE_SITE_PREMIUM_ROLE);
    if (defined('PKPREMIUM_FUTURE_SITE_DEFAULT_CAP')) {
        $user->remove_cap(PKPREMIUM_FUTURE_SITE_DEFAULT_CAP);
    }
}

function pkpremium_apply_webhook_event_to_user($payload, $verified = false) {
    if (!is_array($payload)) {
        return array('status' => 'ignored', 'reason' => 'payload_invalide');
    }

    $event_id = isset($payload['id']) ? (string) $payload['id'] : '';
    $event_type = isset($payload['event_type']) ? (string) $payload['event_type'] : '';
    $email = pkpremium_get_paypal_event_email($payload);

    if ($event_id !== '' && pkpremium_is_webhook_processed($event_id)) {
        return array('status' => 'duplicate', 'event_id' => $event_id, 'event_type' => $event_type, 'email' => $email);
    }

    if (!$verified) {
        return array('status' => 'ignored', 'reason' => 'non_verifie', 'event_id' => $event_id, 'event_type' => $event_type, 'email' => $email);
    }

    if ($email === '') {
        return array('status' => 'ignored', 'reason' => 'email_introuvable', 'event_id' => $event_id, 'event_type' => $event_type);
    }

    $user = get_user_by('email', $email);
    if (!$user instanceof WP_User) {
        return array('status' => 'ignored', 'reason' => 'utilisateur_introuvable', 'event_id' => $event_id, 'event_type' => $event_type, 'email' => $email);
    }

    $grant_events = array(
        'BILLING.SUBSCRIPTION.ACTIVATED',
        'PAYMENT.SALE.COMPLETED',
    );
    $revoke_events = array(
        'BILLING.SUBSCRIPTION.CANCELLED',
        'BILLING.SUBSCRIPTION.SUSPENDED',
        'BILLING.SUBSCRIPTION.EXPIRED',
    );

    $action = 'ignored';
    if (in_array($event_type, $grant_events, true)) {
        pkpremium_grant_premium_to_user($user);
        $action = 'granted';
    } elseif (in_array($event_type, $revoke_events, true)) {
        pkpremium_revoke_premium_from_user($user);
        $action = 'revoked';
    }

    if ($event_id !== '') {
        pkpremium_mark_webhook_processed($event_id);
    }

    return array(
        'status' => $action,
        'event_id' => $event_id,
        'event_type' => $event_type,
        'email' => $email,
        'user_id' => $user->ID,
    );
}

function pkpremium_verify_paypal_webhook($headers, $payload, $settings = null) {
    $settings = is_array($settings) ? $settings : pkpremium_get_settings();
    $mode = pkpremium_get_paypal_mode($settings);
    $credentials = pkpremium_get_paypal_credentials($settings, $mode);

    if ($credentials['webhook_id'] === '') {
        return new WP_Error('pkpremium_missing_webhook_id', 'Webhook ID PayPal manquant pour ce mode.');
    }

    $header_map = array_change_key_case((array) $headers, CASE_LOWER);
    $required = array(
        'paypal-transmission-id',
        'paypal-transmission-time',
        'paypal-transmission-sig',
        'paypal-auth-algo',
        'paypal-cert-url',
    );

    foreach ($required as $required_header) {
        if (empty($header_map[$required_header])) {
            return new WP_Error('pkpremium_missing_webhook_header', 'Header PayPal manquant : ' . $required_header);
        }
    }

    $verification_payload = array(
        'auth_algo' => is_array($header_map['paypal-auth-algo']) ? (string) reset($header_map['paypal-auth-algo']) : (string) $header_map['paypal-auth-algo'],
        'cert_url' => is_array($header_map['paypal-cert-url']) ? (string) reset($header_map['paypal-cert-url']) : (string) $header_map['paypal-cert-url'],
        'transmission_id' => is_array($header_map['paypal-transmission-id']) ? (string) reset($header_map['paypal-transmission-id']) : (string) $header_map['paypal-transmission-id'],
        'transmission_sig' => is_array($header_map['paypal-transmission-sig']) ? (string) reset($header_map['paypal-transmission-sig']) : (string) $header_map['paypal-transmission-sig'],
        'transmission_time' => is_array($header_map['paypal-transmission-time']) ? (string) reset($header_map['paypal-transmission-time']) : (string) $header_map['paypal-transmission-time'],
        'webhook_id' => $credentials['webhook_id'],
        'webhook_event' => $payload,
    );

    $response = pkpremium_paypal_api_request('POST', '/v1/notifications/verify-webhook-signature', $verification_payload, $settings, $mode);
    if (is_wp_error($response)) {
        return $response;
    }

    return array(
        'mode' => $mode,
        'code' => $response['code'],
        'body' => $response['body'],
        'verified' => $response['code'] >= 200 && $response['code'] < 300 && isset($response['body']['verification_status']) && $response['body']['verification_status'] === 'SUCCESS',
    );
}

function pkpremium_get_future_preview_source() {
    $file = PKPREMIUM_DIR . 'includes/future-preview.php';

    if (!file_exists($file) || !is_readable($file)) {
        return '';
    }

    $contents = file_get_contents($file);

    return is_string($contents) ? $contents : '';
}

function pkpremium_is_future_preview_managed_by_plugin() {
    return function_exists('pkpremium_future_site_preview_requested');
}

function pkpremium_user_has_future_access($user_id) {
    if (!function_exists('pkpremium_future_site_user_can_access')) {
        return false;
    }

    return pkpremium_future_site_user_can_access((int) $user_id);
}

function pkpremium_mask_secret($value) {
    $value = (string) $value;

    if ($value === '') {
        return 'Non defini';
    }

    if (strlen($value) <= 8) {
        return str_repeat('•', strlen($value));
    }

    return substr($value, 0, 4) . str_repeat('•', max(4, strlen($value) - 8)) . substr($value, -4);
}

function pkpremium_get_webhook_status_label($mode, $settings) {
    $mode = $mode === 'live' ? 'live' : 'sandbox';
    $client_key = $mode === 'live' ? 'paypal_live_client_id' : 'paypal_sandbox_client_id';
    $secret_key = $mode === 'live' ? 'paypal_live_secret' : 'paypal_sandbox_secret';
    $webhook_key = $mode === 'live' ? 'paypal_live_webhook_id' : 'paypal_sandbox_webhook_id';

    if (empty($settings[$client_key]) || empty($settings[$secret_key])) {
        return 'Identifiants API manquants.';
    }

    if (empty($settings[$webhook_key])) {
        return 'Webhook non enregistre pour le moment.';
    }

    return 'Webhook enregistre : ' . $settings[$webhook_key];
}

function pkpremium_handle_settings_actions() {
    if (!is_admin() || !current_user_can('manage_options')) {
        return;
    }

    if (!isset($_POST['pkpremium_action'])) {
        return;
    }

    check_admin_referer('pkpremium_settings_action');

    $action = sanitize_key(wp_unslash($_POST['pkpremium_action']));
    $redirect_url = add_query_arg(
        array(
            'page' => 'pkpremium',
            'tab' => isset($_POST['pkpremium_tab']) ? sanitize_key(wp_unslash($_POST['pkpremium_tab'])) : 'overview',
        ),
        admin_url('admin.php')
    );

    if ($action === 'save_general') {
        $settings = pkpremium_get_settings();
        $settings['paypal_sandbox_enabled'] = isset($_POST['paypal_sandbox_enabled']) ? 1 : 0;
        $settings['subscription_page_url'] = isset($_POST['subscription_page_url']) ? esc_url_raw(wp_unslash($_POST['subscription_page_url'])) : '';
        $settings['member_login_slug'] = isset($_POST['member_login_slug']) ? sanitize_title(wp_unslash($_POST['member_login_slug'])) : 'connexion';
        $settings['member_register_slug'] = isset($_POST['member_register_slug']) ? sanitize_title(wp_unslash($_POST['member_register_slug'])) : 'inscription';
        $settings['member_lostpassword_slug'] = isset($_POST['member_lostpassword_slug']) ? sanitize_title(wp_unslash($_POST['member_lostpassword_slug'])) : 'mot-de-passe-oublie';
        $settings['block_default_wp_login'] = isset($_POST['block_default_wp_login']) ? 1 : 0;
        pkpremium_update_settings($settings);
        flush_rewrite_rules(false);
        $redirect_url = add_query_arg('pkpremium_notice', 'general_saved', $redirect_url);
    } elseif ($action === 'save_paypal_api') {
        $settings = pkpremium_get_settings();
        $settings['paypal_live_client_id'] = isset($_POST['paypal_live_client_id']) ? sanitize_text_field(wp_unslash($_POST['paypal_live_client_id'])) : '';
        $settings['paypal_live_secret'] = isset($_POST['paypal_live_secret']) ? sanitize_text_field(wp_unslash($_POST['paypal_live_secret'])) : '';
        $settings['paypal_sandbox_client_id'] = isset($_POST['paypal_sandbox_client_id']) ? sanitize_text_field(wp_unslash($_POST['paypal_sandbox_client_id'])) : '';
        $settings['paypal_sandbox_secret'] = isset($_POST['paypal_sandbox_secret']) ? sanitize_text_field(wp_unslash($_POST['paypal_sandbox_secret'])) : '';
        $settings['paypal_live_webhook_id'] = isset($_POST['paypal_live_webhook_id']) ? sanitize_text_field(wp_unslash($_POST['paypal_live_webhook_id'])) : '';
        $settings['paypal_sandbox_webhook_id'] = isset($_POST['paypal_sandbox_webhook_id']) ? sanitize_text_field(wp_unslash($_POST['paypal_sandbox_webhook_id'])) : '';
        pkpremium_update_settings($settings);
        delete_transient('pkpremium_paypal_access_token_live');
        delete_transient('pkpremium_paypal_access_token_sandbox');
        $redirect_url = add_query_arg('pkpremium_notice', 'api_saved', $redirect_url);
    } elseif ($action === 'test_paypal_api') {
        $settings = pkpremium_get_settings();
        $token = pkpremium_paypal_request_access_token(true, $settings);
        $settings['paypal_last_test'] = array(
            'tested_at' => current_time('mysql'),
            'mode' => pkpremium_get_paypal_mode($settings),
            'success' => !is_wp_error($token),
            'details' => is_wp_error($token) ? $token->get_error_message() : 'Token OAuth recupere',
        );
        pkpremium_update_settings($settings);
        $redirect_url = add_query_arg('pkpremium_notice', !is_wp_error($token) ? 'paypal_test_success' : 'paypal_test_error', $redirect_url);
    } elseif ($action === 'clear_paypal_token_cache') {
        delete_transient('pkpremium_paypal_access_token_live');
        delete_transient('pkpremium_paypal_access_token_sandbox');
        $redirect_url = add_query_arg('pkpremium_notice', 'token_cache_cleared', $redirect_url);
    } elseif ($action === 'delete_webhooks') {
        $settings = pkpremium_get_settings();
        $settings['paypal_live_webhook_id'] = '';
        $settings['paypal_sandbox_webhook_id'] = '';
        pkpremium_update_settings($settings);
        $redirect_url = add_query_arg('pkpremium_notice', 'webhooks_deleted', $redirect_url);
    } elseif ($action === 'sync_paypal_webhook') {
        $settings = pkpremium_get_settings();
        $mode = pkpremium_get_paypal_mode($settings);
        $result = pkpremium_sync_paypal_webhook($settings, $mode);

        if (is_wp_error($result)) {
            $settings['paypal_last_verification'] = array(
                'verified_at' => current_time('mysql'),
                'mode' => $mode,
                'success' => false,
                'details' => $result->get_error_message(),
            );
            pkpremium_update_settings($settings);
            $redirect_url = add_query_arg('pkpremium_notice', 'webhook_sync_error', $redirect_url);
        } else {
            if ($mode === 'sandbox') {
                $settings['paypal_sandbox_webhook_id'] = $result['webhook_id'];
            } else {
                $settings['paypal_live_webhook_id'] = $result['webhook_id'];
            }
            $settings['paypal_last_verification'] = array(
                'verified_at' => current_time('mysql'),
                'mode' => $mode,
                'success' => true,
                'details' => $result,
            );
            pkpremium_update_settings($settings);
            $redirect_url = add_query_arg('pkpremium_notice', 'webhook_sync_success', $redirect_url);
        }
    } elseif ($action === 'verify_last_webhook') {
        $settings = pkpremium_get_settings();
        $last_webhook = get_option('pkpremium_last_paypal_webhook', array());
        $verification = pkpremium_verify_paypal_webhook(
            isset($last_webhook['headers']) ? $last_webhook['headers'] : array(),
            isset($last_webhook['payload']) ? $last_webhook['payload'] : array(),
            $settings
        );
        $settings['paypal_last_verification'] = array(
            'verified_at' => current_time('mysql'),
            'mode' => pkpremium_get_paypal_mode($settings),
            'success' => !is_wp_error($verification) && !empty($verification['verified']),
            'details' => is_wp_error($verification) ? $verification->get_error_message() : $verification,
        );
        pkpremium_update_settings($settings);
        $redirect_url = add_query_arg('pkpremium_notice', !is_wp_error($verification) && !empty($verification['verified']) ? 'webhook_verified' : 'webhook_not_verified', $redirect_url);
    } elseif ($action === 'process_last_webhook') {
        $last_webhook = get_option('pkpremium_last_paypal_webhook', array());
        $processed = pkpremium_apply_webhook_event_to_user(
            isset($last_webhook['payload']) ? $last_webhook['payload'] : array(),
            !empty($last_webhook['verification']['verified'])
        );
        if (is_array($last_webhook)) {
            $last_webhook['processed'] = $processed;
            update_option('pkpremium_last_paypal_webhook', $last_webhook, false);
        }
        $redirect_url = add_query_arg('pkpremium_notice', 'webhook_processed', $redirect_url);
    } elseif ($action === 'grant_premium_access') {
        $user = pkpremium_find_user_from_request();

        if (!$user) {
            $redirect_url = add_query_arg('pkpremium_notice', 'user_not_found', $redirect_url);
        } else {
            pkpremium_grant_premium_to_user($user);
            $redirect_url = add_query_arg(
                array(
                    'pkpremium_notice' => 'premium_granted',
                    'user_id' => $user->ID,
                ),
                $redirect_url
            );
        }
    } elseif ($action === 'revoke_premium_access') {
        $user = pkpremium_find_user_from_request();

        if (!$user) {
            $redirect_url = add_query_arg('pkpremium_notice', 'user_not_found', $redirect_url);
        } else {
            pkpremium_revoke_premium_from_user($user);
            $redirect_url = add_query_arg(
                array(
                    'pkpremium_notice' => 'premium_revoked',
                    'user_id' => $user->ID,
                ),
                $redirect_url
            );
        }
    }

    wp_safe_redirect($redirect_url);
    exit;
}

add_action('admin_init', 'pkpremium_handle_settings_actions');

function pkpremium_find_user_from_request() {
    $user_id = isset($_POST['target_user_id']) ? absint($_POST['target_user_id']) : 0;
    $user_email = isset($_POST['target_user_email']) ? sanitize_email(wp_unslash($_POST['target_user_email'])) : '';

    if ($user_id > 0) {
        $user = get_user_by('id', $user_id);
        if ($user instanceof WP_User) {
            return $user;
        }
    }

    if ($user_email !== '') {
        $user = get_user_by('email', $user_email);
        if ($user instanceof WP_User) {
            return $user;
        }
    }

    return null;
}

function pkpremium_render_settings_notice() {
    if (!isset($_GET['page']) || sanitize_key(wp_unslash($_GET['page'])) !== 'pkpremium') {
        return;
    }

    if (empty($_GET['pkpremium_notice'])) {
        return;
    }

    $notice = sanitize_key(wp_unslash($_GET['pkpremium_notice']));
    $messages = array(
        'general_saved' => 'Reglages generaux enregistres.',
        'api_saved' => 'Identifiants PayPal enregistres.',
        'token_cache_cleared' => 'Cache du token PayPal vide.',
        'webhooks_deleted' => 'IDs de webhooks supprimes localement.',
        'premium_granted' => 'Acces premium ajoute.',
        'premium_revoked' => 'Acces premium retire.',
        'user_not_found' => 'Utilisateur introuvable.',
        'paypal_test_success' => 'Connexion PayPal OK.',
        'paypal_test_error' => 'Echec du test PayPal.',
        'webhook_sync_success' => 'Webhook PayPal synchronise.',
        'webhook_sync_error' => 'Echec de la synchronisation du webhook PayPal.',
        'webhook_verified' => 'Dernier webhook PayPal verifie avec succes.',
        'webhook_not_verified' => 'Le dernier webhook n’a pas pu etre verifie.',
        'webhook_processed' => 'Dernier webhook traite.',
    );

    if (!isset($messages[$notice])) {
        return;
    }

    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($messages[$notice]) . '</p></div>';
}

add_action('admin_notices', 'pkpremium_render_settings_notice');

function pkpremium_register_admin_menu() {
    add_menu_page(
        'WP PK premium',
        'WP PK premium',
        'manage_options',
        'pkpremium',
        'pkpremium_render_admin_page',
        'dashicons-star-filled',
        58
    );
}

add_action('admin_menu', 'pkpremium_register_admin_menu');

function pkpremium_get_current_admin_tab() {
    $allowed_tabs = array('overview', 'future-site', 'general', 'member-journey', 'subscription', 'paypal-api', 'paypal-webhooks', 'users', 'diagnostics');
    $tab = isset($_GET['tab']) ? sanitize_key(wp_unslash($_GET['tab'])) : 'overview';

    if (!in_array($tab, $allowed_tabs, true)) {
        $tab = 'general';
    }

    return $tab;
}

function pkpremium_render_admin_tabs($current_tab) {
    $tabs = array(
        'overview' => 'Vue d’ensemble',
        'future-site' => 'Futur site',
        'general' => 'General',
        'member-journey' => 'Parcours membre',
        'subscription' => 'Abonnement',
        'paypal-api' => 'API PayPal',
        'paypal-webhooks' => 'Webhooks PayPal',
        'users' => 'Utilisateurs',
        'diagnostics' => 'Diagnostic',
    );

    echo '<h2 class="nav-tab-wrapper">';
    foreach ($tabs as $tab => $label) {
        $url = add_query_arg(
            array(
                'page' => 'pkpremium',
                'tab' => $tab,
            ),
            admin_url('admin.php')
        );

        $class = $current_tab === $tab ? 'nav-tab nav-tab-active' : 'nav-tab';
        echo '<a class="' . esc_attr($class) . '" href="' . esc_url($url) . '">' . esc_html($label) . '</a>';
    }
    echo '</h2>';
}

function pkpremium_render_overview_tab() {
    ?>
    <div class="notice notice-info inline">
        <p>WP PK premium centralise la logique premium de Mondary. Le plugin reprend deja l’aperçu futur du site, gere le role <code>premium</code>, prepare les reglages PayPal et servira a automatiser l’acces premium par abonnement.</p>
    </div>

    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">Version de l’extension</th>
                <td><code><?php echo esc_html(PKPREMIUM_VERSION); ?></code></td>
            </tr>
            <tr>
                <th scope="row">Ce que fait l’extension</th>
                <td>
                    <ul style="list-style:disc;padding-left:18px;margin:0;">
                        <li>reprend le systeme <code>?future_site=1</code></li>
                        <li>gere l’acces administrateur + premium</li>
                        <li>affiche le bandeau premium et le bouton <code>Quitter le futur</code></li>
                        <li>prepare une base de reglages PayPal et webhooks</li>
                    </ul>
                </td>
            </tr>
            <tr>
                <th scope="row">Lien direct Futur site</th>
                <td>
                    <?php if (function_exists('pkpremium_future_site_preview_url')) : ?>
                        <a class="button button-secondary" href="<?php echo esc_url(pkpremium_future_site_preview_url()); ?>">Ouvrir Futur site</a>
                    <?php else : ?>
                        <span>Le module Futur site n’est pas charge.</span>
                    <?php endif; ?>
                </td>
            </tr>
        </tbody>
    </table>
    <?php
}

function pkpremium_render_future_site_tab() {
    $source = pkpremium_get_future_preview_source();
    ?>
    <p>Cette section regroupe le module Futur site embarque dans l’extension, avec son lien direct et son code source actuel.</p>

    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">Lien direct</th>
                <td>
                    <?php if (function_exists('pkpremium_future_site_preview_url')) : ?>
                        <a class="button button-secondary" href="<?php echo esc_url(pkpremium_future_site_preview_url()); ?>">Ouvrir Futur site</a>
                    <?php else : ?>
                        <span>Le module Futur site n’est pas charge.</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th scope="row">Description</th>
                <td>
                    <p>Le module Futur site permet a un administrateur ou a un utilisateur premium de visualiser le site avec les articles publies et planifies, tout en conservant le theme courant du site.</p>
                </td>
            </tr>
            <tr>
                <th scope="row">Fichier source</th>
                <td><code><?php echo esc_html(PKPREMIUM_DIR . 'includes/future-preview.php'); ?></code></td>
            </tr>
        </tbody>
    </table>

    <h2>Code du module Futur site</h2>
    <?php if ($source !== '') : ?>
        <textarea readonly rows="32" style="width:100%;max-width:1200px;font-family:monospace;"><?php echo esc_textarea($source); ?></textarea>
    <?php else : ?>
        <p>Impossible de lire le fichier source.</p>
    <?php endif; ?>
    <?php
}

function pkpremium_render_general_tab($settings) {
    $member_urls = pkpremium_get_member_urls();
    ?>
    <form method="post">
        <?php wp_nonce_field('pkpremium_settings_action'); ?>
        <input type="hidden" name="pkpremium_action" value="save_general">
        <input type="hidden" name="pkpremium_tab" value="general">

        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">Activer le mode Sandbox ou Test</th>
                    <td>
                        <label>
                            <input type="checkbox" name="paypal_sandbox_enabled" value="1" <?php checked(!empty($settings['paypal_sandbox_enabled'])); ?>>
                            Active cette option pour tester les paiements en mode test. Laisse-la decochee en production.
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="subscription_page_url">URL page abonnement</label></th>
                    <td><input type="url" class="regular-text" id="subscription_page_url" name="subscription_page_url" value="<?php echo esc_attr($settings['subscription_page_url']); ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="member_login_slug">Slug connexion</label></th>
                    <td>
                        <input type="text" class="regular-text" id="member_login_slug" name="member_login_slug" value="<?php echo esc_attr($settings['member_login_slug']); ?>">
                        <p class="description"><a href="<?php echo esc_url($member_urls['login']); ?>" target="_blank" rel="noreferrer"><?php echo esc_html($member_urls['login']); ?></a></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="member_register_slug">Slug inscription</label></th>
                    <td>
                        <input type="text" class="regular-text" id="member_register_slug" name="member_register_slug" value="<?php echo esc_attr($settings['member_register_slug']); ?>">
                        <p class="description"><a href="<?php echo esc_url($member_urls['register']); ?>" target="_blank" rel="noreferrer"><?php echo esc_html($member_urls['register']); ?></a></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="member_lostpassword_slug">Slug mot de passe perdu</label></th>
                    <td>
                        <input type="text" class="regular-text" id="member_lostpassword_slug" name="member_lostpassword_slug" value="<?php echo esc_attr($settings['member_lostpassword_slug']); ?>">
                        <p class="description"><a href="<?php echo esc_url($member_urls['lostpassword']); ?>" target="_blank" rel="noreferrer"><?php echo esc_html($member_urls['lostpassword']); ?></a></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Bloquer les URLs WordPress par defaut</th>
                    <td>
                        <label>
                            <input type="checkbox" name="block_default_wp_login" value="1" <?php checked(!empty($settings['block_default_wp_login'])); ?>>
                            Redirige les acces publics directs a <code>wp-login.php</code> et <code>action=register</code> vers les nouvelles URLs.
                        </label>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php submit_button('Enregistrer'); ?>
    </form>
    <?php
}

function pkpremium_render_member_journey_tab() {
    $urls = pkpremium_get_member_urls();
    ?>
    <h2>Parcours membre</h2>
    <p>Cette section rassemble les URLs natives WordPress ou personnalisées utilisées pour la connexion, l’inscription et la récupération du mot de passe.</p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr><th scope="row">Connexion</th><td><a href="<?php echo esc_url($urls['login']); ?>" target="_blank" rel="noreferrer"><?php echo esc_html($urls['login']); ?></a></td></tr>
            <tr><th scope="row">Inscription</th><td><a href="<?php echo esc_url($urls['register']); ?>" target="_blank" rel="noreferrer"><?php echo esc_html($urls['register']); ?></a></td></tr>
            <tr><th scope="row">Mot de passe oublié</th><td><a href="<?php echo esc_url($urls['lostpassword']); ?>" target="_blank" rel="noreferrer"><?php echo esc_html($urls['lostpassword']); ?></a></td></tr>
        </tbody>
    </table>
    <?php
}

function pkpremium_render_subscription_tab($settings) {
    ?>
    <h2>Abonnement</h2>
    <p>L’URL publique d’abonnement est celle utilisée par le bandeau public premium et par les appels à l’action de l’extension.</p>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">URL actuelle</th>
                <td><a href="<?php echo esc_url($settings['subscription_page_url']); ?>" target="_blank" rel="noreferrer"><?php echo esc_html($settings['subscription_page_url']); ?></a></td>
            </tr>
            <tr>
                <th scope="row">Flux cible</th>
                <td>1. inscription WordPress, 2. paiement PayPal, 3. webhook verifié, 4. role premium attribue automatiquement.</td>
            </tr>
        </tbody>
    </table>
    <?php
}

function pkpremium_get_login_route_map() {
    $settings = pkpremium_get_settings();
    $login_slug = sanitize_title($settings['member_login_slug']);
    $register_slug = sanitize_title($settings['member_register_slug']);
    $lostpassword_slug = sanitize_title($settings['member_lostpassword_slug']);

    return array(
        $login_slug => 'login',
        $register_slug => 'register',
        $lostpassword_slug => 'lostpassword',
    );
}

function pkpremium_register_login_rewrites() {
    add_rewrite_tag('%pkpremium_login_action%', '([^&]+)');

    foreach (pkpremium_get_login_route_map() as $slug => $action) {
        if ($slug === '') {
            continue;
        }

        add_rewrite_rule('^' . preg_quote($slug, '#') . '/?$', 'index.php?pkpremium_login_action=' . $action, 'top');
    }
}

add_action('init', 'pkpremium_register_login_rewrites', 20);

function pkpremium_render_custom_login_routes() {
    $action = get_query_var('pkpremium_login_action');
    if (!in_array($action, array('login', 'register', 'lostpassword'), true)) {
        return;
    }

    if ($action === 'register') {
        $_REQUEST['action'] = 'register';
        $_GET['action'] = 'register';
    } elseif ($action === 'lostpassword') {
        $_REQUEST['action'] = 'lostpassword';
        $_GET['action'] = 'lostpassword';
    }

    define('PKPREMIUM_CUSTOM_LOGIN_ROUTE', true);
    require ABSPATH . 'wp-login.php';
    exit;
}

add_action('template_redirect', 'pkpremium_render_custom_login_routes', 0);

function pkpremium_filter_login_url($login_url, $redirect, $force_reauth) {
    unset($redirect, $force_reauth);
    $urls = pkpremium_get_member_urls();
    return $urls['login'];
}

function pkpremium_filter_register_url($register_url) {
    unset($register_url);
    $urls = pkpremium_get_member_urls();
    return $urls['register'];
}

function pkpremium_filter_lostpassword_url($lostpassword_url, $redirect) {
    unset($redirect);
    $urls = pkpremium_get_member_urls();
    return $urls['lostpassword'];
}

add_filter('login_url', 'pkpremium_filter_login_url', 20, 3);
add_filter('register_url', 'pkpremium_filter_register_url', 20, 1);
add_filter('lostpassword_url', 'pkpremium_filter_lostpassword_url', 20, 2);

function pkpremium_block_default_wp_login_access() {
    if (defined('PKPREMIUM_CUSTOM_LOGIN_ROUTE') && PKPREMIUM_CUSTOM_LOGIN_ROUTE) {
        return;
    }

    $settings = pkpremium_get_settings();
    if (empty($settings['block_default_wp_login'])) {
        return;
    }

    if (is_user_logged_in() && current_user_can('manage_options')) {
        return;
    }

    $action = isset($_REQUEST['action']) ? sanitize_key(wp_unslash($_REQUEST['action'])) : 'login';
    $urls = pkpremium_get_member_urls();

    if ($action === 'register') {
        wp_safe_redirect($urls['register']);
        exit;
    }

    if ($action === 'lostpassword' || $action === 'retrievepassword' || $action === 'rp' || $action === 'resetpass') {
        wp_safe_redirect($urls['lostpassword']);
        exit;
    }

    wp_safe_redirect($urls['login']);
    exit;
}

add_action('login_init', 'pkpremium_block_default_wp_login_access', 0);

function pkpremium_render_paypal_api_tab($settings) {
    $last_test = isset($settings['paypal_last_test']) && is_array($settings['paypal_last_test']) ? $settings['paypal_last_test'] : array();
    ?>
    <div class="notice notice-info inline">
        <p>Configure les identifiants API PayPal pour le mode live et sandbox. Le cache du token sera vide automatiquement apres enregistrement.</p>
    </div>

    <form method="post">
        <?php wp_nonce_field('pkpremium_settings_action'); ?>
        <input type="hidden" name="pkpremium_action" value="save_paypal_api">
        <input type="hidden" name="pkpremium_tab" value="paypal-api">

        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row"><label for="paypal_live_client_id">Live Client ID</label></th>
                    <td><input name="paypal_live_client_id" id="paypal_live_client_id" type="text" class="regular-text" value="<?php echo esc_attr($settings['paypal_live_client_id']); ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="paypal_live_secret">Cle utilisateur secrete</label></th>
                    <td><input name="paypal_live_secret" id="paypal_live_secret" type="password" class="regular-text" value="<?php echo esc_attr($settings['paypal_live_secret']); ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="paypal_sandbox_client_id">Boite a sable ID Client</label></th>
                    <td><input name="paypal_sandbox_client_id" id="paypal_sandbox_client_id" type="text" class="regular-text" value="<?php echo esc_attr($settings['paypal_sandbox_client_id']); ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="paypal_sandbox_secret">Cle secrete Sandbox</label></th>
                    <td><input name="paypal_sandbox_secret" id="paypal_sandbox_secret" type="password" class="regular-text" value="<?php echo esc_attr($settings['paypal_sandbox_secret']); ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="paypal_live_webhook_id">Webhook ID Live</label></th>
                    <td><input name="paypal_live_webhook_id" id="paypal_live_webhook_id" type="text" class="regular-text" value="<?php echo esc_attr($settings['paypal_live_webhook_id']); ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="paypal_sandbox_webhook_id">Webhook ID Sandbox</label></th>
                    <td><input name="paypal_sandbox_webhook_id" id="paypal_sandbox_webhook_id" type="text" class="regular-text" value="<?php echo esc_attr($settings['paypal_sandbox_webhook_id']); ?>"></td>
                </tr>
            </tbody>
        </table>

        <?php submit_button('Enregistrer'); ?>
    </form>

    <form method="post" style="margin-top:12px;display:inline-block;margin-right:8px;">
        <?php wp_nonce_field('pkpremium_settings_action'); ?>
        <input type="hidden" name="pkpremium_action" value="clear_paypal_token_cache">
        <input type="hidden" name="pkpremium_tab" value="paypal-api">
        <?php submit_button('Effacer le Cache du Token', 'secondary', 'submit', false); ?>
    </form>
    <form method="post" style="margin-top:12px;display:inline-block;">
        <?php wp_nonce_field('pkpremium_settings_action'); ?>
        <input type="hidden" name="pkpremium_action" value="test_paypal_api">
        <input type="hidden" name="pkpremium_tab" value="paypal-api">
        <?php submit_button('Tester la connexion PayPal', 'primary', 'submit', false); ?>
    </form>

    <?php if (!empty($last_test)) : ?>
        <h3>Dernier test API</h3>
        <p><?php echo esc_html($last_test['tested_at'] ?? ''); ?> | <?php echo esc_html($last_test['mode'] ?? ''); ?> | <?php echo !empty($last_test['success']) ? 'OK' : 'Erreur'; ?></p>
        <p><?php echo esc_html(is_string($last_test['details'] ?? '') ? ($last_test['details'] ?? '') : wp_json_encode($last_test['details'] ?? array(), JSON_UNESCAPED_SLASHES)); ?></p>
    <?php endif; ?>
    <?php
}

function pkpremium_render_paypal_webhooks_tab($settings) {
    $sandbox_status = pkpremium_get_webhook_status_label('sandbox', $settings);
    $live_status = pkpremium_get_webhook_status_label('live', $settings);
    $last_verification = isset($settings['paypal_last_verification']) && is_array($settings['paypal_last_verification']) ? $settings['paypal_last_verification'] : array();
    ?>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">Statut du webhook Live</th>
                <td>
                    <p><?php echo esc_html($live_status); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">Statut du webhook Test</th>
                <td>
                    <p><?php echo esc_html($sandbox_status); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row">URL du webhook PKpremium</th>
                <td>
                    <code><?php echo esc_html(pkpremium_get_rest_webhook_url()); ?></code>
                    <p class="description">Cette URL servira a recevoir les notifications PayPal quand on branchera la creation et la verification automatiques.</p>
                </td>
            </tr>
            <tr>
                <th scope="row">Resume des identifiants</th>
                <td>
                    <p>Live Client ID : <code><?php echo esc_html(pkpremium_mask_secret($settings['paypal_live_client_id'])); ?></code></p>
                    <p>Live Secret : <code><?php echo esc_html(pkpremium_mask_secret($settings['paypal_live_secret'])); ?></code></p>
                    <p>Sandbox Client ID : <code><?php echo esc_html(pkpremium_mask_secret($settings['paypal_sandbox_client_id'])); ?></code></p>
                    <p>Sandbox Secret : <code><?php echo esc_html(pkpremium_mask_secret($settings['paypal_sandbox_secret'])); ?></code></p>
                </td>
            </tr>
        </tbody>
    </table>

    <form method="post">
        <?php wp_nonce_field('pkpremium_settings_action'); ?>
        <input type="hidden" name="pkpremium_action" value="delete_webhooks">
        <input type="hidden" name="pkpremium_tab" value="paypal-webhooks">
        <?php submit_button('Supprimer les Webhooks', 'secondary', 'submit', false); ?>
    </form>

    <form method="post" style="display:inline-block;margin-top:12px;margin-right:8px;">
        <?php wp_nonce_field('pkpremium_settings_action'); ?>
        <input type="hidden" name="pkpremium_action" value="sync_paypal_webhook">
        <input type="hidden" name="pkpremium_tab" value="paypal-webhooks">
        <?php submit_button('Creer / synchroniser le webhook', 'primary', 'submit', false); ?>
    </form>

    <form method="post" style="display:inline-block;margin-right:8px;">
        <?php wp_nonce_field('pkpremium_settings_action'); ?>
        <input type="hidden" name="pkpremium_action" value="verify_last_webhook">
        <input type="hidden" name="pkpremium_tab" value="paypal-webhooks">
        <?php submit_button('Verifier le dernier webhook', 'secondary', 'submit', false); ?>
    </form>
    <form method="post" style="display:inline-block;">
        <?php wp_nonce_field('pkpremium_settings_action'); ?>
        <input type="hidden" name="pkpremium_action" value="process_last_webhook">
        <input type="hidden" name="pkpremium_tab" value="paypal-webhooks">
        <?php submit_button('Traiter le dernier webhook', 'secondary', 'submit', false); ?>
    </form>

    <?php if (!empty($last_verification)) : ?>
        <h3>Derniere verification</h3>
        <textarea readonly rows="10" style="width:100%;max-width:1100px;font-family:monospace;"><?php echo esc_textarea(wp_json_encode($last_verification, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); ?></textarea>
    <?php endif; ?>
    <?php
}

function pkpremium_render_users_tab() {
    $lookup_email = isset($_GET['lookup_email']) ? sanitize_email(wp_unslash($_GET['lookup_email'])) : '';
    $lookup_user = $lookup_email !== '' ? get_user_by('email', $lookup_email) : null;
    $premium_users = get_users(array(
        'role' => PKPREMIUM_FUTURE_SITE_PREMIUM_ROLE,
        'orderby' => 'registered',
        'order' => 'DESC',
        'number' => 20,
    ));
    ?>
    <h2>Gestion manuelle premium</h2>
    <p>Cette v1 permet deja de tester le plugin sans paiement live. Tu peux attribuer ou retirer l’acces premium sur un utilisateur WordPress existant.</p>

    <form method="get" style="margin:16px 0;">
        <input type="hidden" name="page" value="pkpremium">
        <input type="hidden" name="tab" value="users">
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row"><label for="lookup_email">Chercher un utilisateur par email</label></th>
                    <td>
                        <input type="email" name="lookup_email" id="lookup_email" class="regular-text" value="<?php echo esc_attr($lookup_email); ?>">
                        <?php submit_button('Chercher', 'secondary', 'submit', false); ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>

    <?php if ($lookup_email !== '') : ?>
        <h3>Resultat</h3>
        <?php if ($lookup_user instanceof WP_User) : ?>
            <table class="widefat striped" style="max-width:900px;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Identifiant</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Acces futur</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo esc_html((string) $lookup_user->ID); ?></td>
                        <td><?php echo esc_html($lookup_user->user_login); ?></td>
                        <td><?php echo esc_html($lookup_user->user_email); ?></td>
                        <td><?php echo esc_html(implode(', ', $lookup_user->roles)); ?></td>
                        <td><?php echo pkpremium_user_has_future_access($lookup_user->ID) ? 'Oui' : 'Non'; ?></td>
                        <td>
                            <form method="post" style="display:inline-block;margin-right:8px;">
                                <?php wp_nonce_field('pkpremium_settings_action'); ?>
                                <input type="hidden" name="pkpremium_tab" value="users">
                                <input type="hidden" name="target_user_id" value="<?php echo esc_attr((string) $lookup_user->ID); ?>">
                                <input type="hidden" name="pkpremium_action" value="grant_premium_access">
                                <?php submit_button('Donner premium', 'primary small', 'submit', false); ?>
                            </form>
                            <form method="post" style="display:inline-block;">
                                <?php wp_nonce_field('pkpremium_settings_action'); ?>
                                <input type="hidden" name="pkpremium_tab" value="users">
                                <input type="hidden" name="target_user_id" value="<?php echo esc_attr((string) $lookup_user->ID); ?>">
                                <input type="hidden" name="pkpremium_action" value="revoke_premium_access">
                                <?php submit_button('Retirer premium', 'secondary small', 'submit', false); ?>
                            </form>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php else : ?>
            <p>Aucun utilisateur WordPress trouve pour cet email.</p>
        <?php endif; ?>
    <?php endif; ?>

    <h3 style="margin-top:32px;">Derniers utilisateurs premium</h3>
    <?php if (!empty($premium_users)) : ?>
        <table class="widefat striped" style="max-width:900px;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Identifiant</th>
                    <th>Email</th>
                    <th>Inscription</th>
                    <th>Acces futur</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($premium_users as $user) : ?>
                    <tr>
                        <td><?php echo esc_html((string) $user->ID); ?></td>
                        <td><?php echo esc_html($user->user_login); ?></td>
                        <td><?php echo esc_html($user->user_email); ?></td>
                        <td><?php echo esc_html($user->user_registered); ?></td>
                        <td><?php echo pkpremium_user_has_future_access($user->ID) ? 'Oui' : 'Non'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>Aucun utilisateur premium pour le moment.</p>
    <?php endif; ?>
    <?php
}

function pkpremium_render_diagnostics_tab($settings) {
    $last_webhook = get_option('pkpremium_last_paypal_webhook');
    $plugin_notice = function_exists('fs_future_site_preview_requested');
    ?>
    <h2>Diagnostic</h2>
    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">Apercu futur</th>
                <td>
                    <?php if (function_exists('pkpremium_future_site_preview_url')) : ?>
                        <a class="button button-secondary" href="<?php echo esc_url(pkpremium_future_site_preview_url()); ?>">Ouvrir l’apercu futur</a>
                    <?php else : ?>
                        <span>Le module Futur site de PKpremium n’est pas charge.</span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th scope="row">URL webhook REST</th>
                <td><code><?php echo esc_html(pkpremium_get_rest_webhook_url()); ?></code></td>
            </tr>
            <tr>
                <th scope="row">Mode PayPal</th>
                <td><?php echo !empty($settings['paypal_sandbox_enabled']) ? 'Sandbox / Test' : 'Live / Production'; ?></td>
            </tr>
            <tr>
                <th scope="row">Ancien snippet Futur site detecte</th>
                <td><?php echo $plugin_notice ? 'Oui' : 'Non'; ?></td>
            </tr>
        </tbody>
    </table>

    <?php if (!pkpremium_is_future_preview_managed_by_plugin()) : ?>
        <div class="notice notice-warning inline">
            <p>L’ancien snippet "Futur site" semble encore actif. Desactive-le avant de tester PKpremium, sinon le module futur du plugin reste en veille.</p>
        </div>
    <?php endif; ?>

    <h3>Dernier webhook PayPal recu</h3>
    <?php if (is_array($last_webhook) && !empty($last_webhook)) : ?>
        <p>Recu le <?php echo esc_html(isset($last_webhook['received_at']) ? $last_webhook['received_at'] : 'inconnu'); ?></p>
        <textarea readonly rows="16" style="width:100%;max-width:1100px;font-family:monospace;"><?php echo esc_textarea(wp_json_encode($last_webhook, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)); ?></textarea>
    <?php else : ?>
        <p>Aucun webhook recu pour le moment.</p>
    <?php endif; ?>
    <?php
}

function pkpremium_render_admin_page() {
    if (!current_user_can('manage_options')) {
        wp_die('Desole, vous n’avez pas l’autorisation d’acceder a cette page.');
    }

    $settings = pkpremium_get_settings();
    $current_tab = pkpremium_get_current_admin_tab();
    ?>
    <div class="wrap">
        <h1>WP PK premium</h1>
        <p>Base premium Mondary. Le futur-site est repris ici, et cette page servira a brancher PayPal puis la gestion automatique des abonnes premium. Version actuelle : <strong><?php echo esc_html(PKPREMIUM_VERSION); ?></strong>.</p>

        <?php pkpremium_render_admin_tabs($current_tab); ?>

        <?php if ($current_tab === 'overview') : ?>
            <?php pkpremium_render_overview_tab(); ?>
        <?php elseif ($current_tab === 'future-site') : ?>
            <?php pkpremium_render_future_site_tab(); ?>
        <?php elseif ($current_tab === 'general') : ?>
            <?php pkpremium_render_general_tab($settings); ?>
        <?php elseif ($current_tab === 'member-journey') : ?>
            <?php pkpremium_render_member_journey_tab(); ?>
        <?php elseif ($current_tab === 'subscription') : ?>
            <?php pkpremium_render_subscription_tab($settings); ?>
        <?php elseif ($current_tab === 'paypal-api') : ?>
            <?php pkpremium_render_paypal_api_tab($settings); ?>
        <?php elseif ($current_tab === 'paypal-webhooks') : ?>
            <?php pkpremium_render_paypal_webhooks_tab($settings); ?>
        <?php elseif ($current_tab === 'users') : ?>
            <?php pkpremium_render_users_tab(); ?>
        <?php elseif ($current_tab === 'diagnostics') : ?>
            <?php pkpremium_render_diagnostics_tab($settings); ?>
        <?php endif; ?>
    </div>
    <?php
}

function pkpremium_register_rest_routes() {
    register_rest_route('pkpremium/v1', '/paypal/webhook', array(
        'methods' => WP_REST_Server::CREATABLE,
        'callback' => 'pkpremium_handle_paypal_webhook',
        'permission_callback' => '__return_true',
    ));
}

add_action('rest_api_init', 'pkpremium_register_rest_routes');

function pkpremium_handle_paypal_webhook(WP_REST_Request $request) {
    $payload = $request->get_json_params();
    $headers = $request->get_headers();

    if (!is_array($payload)) {
        $payload = array(
            'raw_body' => $request->get_body(),
        );
    }

    $verification = pkpremium_verify_paypal_webhook($headers, $payload);
    $verified = !is_wp_error($verification) && !empty($verification['verified']);
    $processed = pkpremium_apply_webhook_event_to_user($payload, $verified);

    update_option(
        'pkpremium_last_paypal_webhook',
        array(
            'received_at' => current_time('mysql'),
            'headers' => $headers,
            'payload' => $payload,
            'verification' => is_wp_error($verification) ? array(
                'verified' => false,
                'error' => $verification->get_error_message(),
            ) : $verification,
            'processed' => $processed,
        ),
        false
    );

    return new WP_REST_Response(array(
        'success' => true,
        'message' => 'Webhook recu.',
        'verified' => $verified,
        'processed' => $processed,
    ), 200);
}
