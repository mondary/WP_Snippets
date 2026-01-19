<?php
/*
 * Plugin Name: VS Code Extensions Tiles
 * Description: Affiche les extensions VS Code du marketplace sous forme de tuiles avec icônes et titres
 * Version: 1.4
 */

if (!defined('ABSPATH')) exit;

add_shortcode('vscode_extensions', 'vscode_extensions_shortcode');

function vscode_extensions_shortcode($atts, $content = null) {
    if (empty($content)) {
        return '<p>Veuillez ajouter des URLs d\'extensions VS Code.</p>';
    }
    
    // Process nested shortcodes
    $content = do_shortcode($content);
    
    // Extract VS Code extension URLs from content
    $extensions = get_vscode_extensions_from_content($content);
    
    if (empty($extensions)) {
        return '<p>Aucune extension VS Code valide trouvée.</p>';
    }
    
    // Generate HTML output
    $output = '<div class="vscode-extensions-tiles">';
    foreach ($extensions as $extension) {
        $output .= display_vscode_extension_tile($extension);
    }
    $output .= '</div>';
    
    // Add CSS styles
    $output .= get_vscode_tiles_styles();
    
    return $output;
}

function get_vscode_extensions_from_content($content) {
    $extensions = array();
    $processed_urls = array();
    
    // Pattern to match VS Code Marketplace URLs in markdown format: [text](url)
    preg_match_all('/\[(.*?)\]\((https:\/\/marketplace\.visualstudio\.com\/items\?itemName=[^\)]+)\)/', $content, $md_matches, PREG_SET_ORDER);
    foreach ($md_matches as $match) {
        $url = $match[2];
        if (!in_array($url, $processed_urls)) {
            $extension_data = get_vscode_extension_data($url, $match[1]);
            if ($extension_data) {
                $extensions[] = $extension_data;
                $processed_urls[] = $url;
            }
        }
    }
    
    // Pattern to match VS Code Marketplace URLs in HTML link format: <a href="url">text</a>
    preg_match_all('/<a\s+(?:[^>]*?\s+)?href=([\"\'])(https:\/\/marketplace\.visualstudio\.com\/items\?itemName=[^\"\']+)\1[^>]*>(.*?)<\/a>/i', $content, $html_matches, PREG_SET_ORDER);
    foreach ($html_matches as $match) {
        $url = $match[2];
        if (!in_array($url, $processed_urls)) {
            $extension_data = get_vscode_extension_data($url, strip_tags($match[3]));
            if ($extension_data) {
                $extensions[] = $extension_data;
                $processed_urls[] = $url;
            }
        }
    }
    
    // Pattern to match plain VS Code Marketplace URLs
    preg_match_all('/https:\/\/marketplace\.visualstudio\.com\/items\?itemName=[\w\-\.]+\/[\w\-\.]+/', $content, $plain_matches);
    foreach ($plain_matches[0] as $url) {
        if (!in_array($url, $processed_urls)) {
            $extension_data = get_vscode_extension_data($url);
            if ($extension_data) {
                $extensions[] = $extension_data;
                $processed_urls[] = $url;
            }
        }
    }
    
    return $extensions;
}

function get_vscode_extension_data($url, $title = '') {
    // Extract publisher and extension name from URL
    $pattern = '/https:\/\/marketplace\.visualstudio\.com\/items\?itemName=([\w\-\.]+)\/([\w\-\.]+)/';
    if (!preg_match($pattern, $url, $matches)) {
        return false;
    }
    
    $publisher = $matches[1];
    $extension = $matches[2];
    
    // Create cache key
    $cache_key = 'vscode_ext_' . md5($publisher . '_' . $extension);
    
    // Check if we have cached data
    $cached_data = get_transient($cache_key);
    if ($cached_data !== false) {
        return $cached_data;
    }
    
    // Try to fetch extension data
    $extension_data = fetch_extension_data($url, $publisher, $extension, $title);
    
    if (!$extension_data) {
        // Fallback data
        $display_name = !empty($title) ? $title : str_replace('-', ' ', ucfirst($extension));
        $extension_data = array(
            'url' => $url,
            'publisher' => $publisher,
            'name' => str_replace('-', ' ', ucfirst($extension)),
            'displayName' => $display_name,
            'description' => 'Extension VS Code',
            'icon' => 'https://placehold.co/48/2d7ddd/FFFFFF/png?text=' . substr($extension, 0, 2),
        );
    }
    
    // Cache for 1 hour
    set_transient($cache_key, $extension_data, HOUR_IN_SECONDS);
    
    return $extension_data;
}

function fetch_extension_data($url, $publisher, $extension, $title = '') {
    // Try to fetch the extension page
    $response = wp_remote_get($url, array(
        'timeout' => 15,
        'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
    ));
    
    if (is_wp_error($response)) {
        error_log('VSCode Extension Fetch Error: ' . $response->get_error_message());
        return false;
    }
    
    $body = wp_remote_retrieve_body($response);
    $response_code = wp_remote_retrieve_response_code($response);
    
    if ($response_code !== 200 || empty($body)) {
        error_log('VSCode Extension Fetch Error: HTTP ' . $response_code);
        return false;
    }
    
    // Extract data from the page
    $data = array(
        'url' => $url,
        'publisher' => $publisher
    );
    
    // Try to get the extension name from the title
    if (preg_match('/<title>([^<]+) - Visual Studio Marketplace<\/title>/', $body, $matches)) {
        $data['name'] = trim(str_replace(' - Visual Studio Marketplace', '', $matches[1]));
        $data['displayName'] = $data['name'];
    } else if (!empty($title)) {
        // Use provided title
        $data['name'] = $title;
        $data['displayName'] = $title;
    } else {
        // Fallback to extension name from URL
        $data['name'] = str_replace('-', ' ', ucfirst($extension));
        $data['displayName'] = $data['name'];
    }
    
    // Try to get description
    if (preg_match('/<meta[^>]+name="description"[^>]+content="([^"]+)"/', $body, $matches)) {
        $data['description'] = $matches[1];
    } else {
        $data['description'] = 'Extension VS Code';
    }
    
    // Try to get icon
    if (preg_match('/<img[^>]+class="[^"]*extension-icon[^"]*"[^>]+src="([^"]+)"/', $body, $matches)) {
        $data['icon'] = $matches[1];
        // Make sure the URL is absolute
        if (strpos($data['icon'], 'http') !== 0) {
            $data['icon'] = 'https://marketplace.visualstudio.com' . $data['icon'];
        }
    } else {
        // Fallback icon
        $data['icon'] = 'https://placehold.co/48/2d7ddd/FFFFFF/png?text=' . substr($extension, 0, 2);
    }
    
    return $data;
}

function display_vscode_extension_tile($extension) {
    $output = '<a href="' . esc_url($extension['url']) . '" class="vscode-extension-tile" target="_blank" rel="noopener">';
    
    // Extension icon
    $output .= '<div class="extension-icon">';
    $output .= '<img src="' . esc_url($extension['icon']) . '" alt="' . esc_attr($extension['name']) . '">';
    $output .= '</div>';
    
    // Extension name only
    $output .= '<div class="extension-name">' . esc_html($extension['displayName']) . '</div>';
    
    $output .= '</a>'; // .vscode-extension-tile
    
    return $output;
}

function get_vscode_tiles_styles() {
    return '
    <style>
    .vscode-extensions-tiles {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 15px;
        margin: 15px 0;
    }
    
    .vscode-extension-tile {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 16px;
        border-radius: 8px;
        background: #ffffff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        text-decoration: none;
        color: inherit;
        transition: all 0.2s ease;
        border: 1px solid #e0e0e0;
        text-align: center;
        min-height: 120px;
        justify-content: center;
    }
    
    .vscode-extension-tile:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border-color: #cccccc;
    }
    
    .extension-icon {
        margin-bottom: 10px;
    }
    
    .extension-icon img {
        width: 48px;
        height: 48px;
        border-radius: 4px;
        object-fit: contain;
    }
    
    .extension-name {
        font-size: 15px;
        font-weight: 500;
        color: #222222;
        margin: 0;
        word-break: break-word;
        line-height: 1.3;
    }
    
    @media (max-width: 768px) {
        .vscode-extensions-tiles {
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 10px;
        }
        
        .vscode-extension-tile {
            padding: 12px;
            min-height: 100px;
        }
        
        .extension-icon img {
            width: 40px;
            height: 40px;
        }
        
        .extension-name {
            font-size: 13px;
        }
    }
    </style>';
}
?>