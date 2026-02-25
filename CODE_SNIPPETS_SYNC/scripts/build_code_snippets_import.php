<?php

declare(strict_types=1);

/**
 * Génère un fichier JSON d'import compatible avec le plugin WordPress "Code Snippets".
 *
 * Usage:
 *   php scripts/build_code_snippets_import.php
 *   php scripts/build_code_snippets_import.php --manifest=sync/snippets.manifest.json
 *   php scripts/build_code_snippets_import.php --out=build/codesnippets-import.json
 */

const DEFAULT_SNIPPETS_DIR = 'WP_Snippets';
const DEFAULT_OUTPUT_FILE = 'build/code-snippets-import.json';

main($argv);

function main(array $argv): void
{
    $options = parseOptions($argv);
    $repoRoot = getcwd() ?: '.';

    $snippetsDir = resolvePath($repoRoot, $options['snippets-dir'] ?? DEFAULT_SNIPPETS_DIR);
    $outputFile = resolvePath($repoRoot, $options['out'] ?? DEFAULT_OUTPUT_FILE);
    $manifestFile = isset($options['manifest']) ? resolvePath($repoRoot, $options['manifest']) : null;

    if (!is_dir($snippetsDir)) {
        fail("Dossier snippets introuvable: {$snippetsDir}");
    }

    $manifest = [];
    if ($manifestFile !== null) {
        $manifest = loadManifest($manifestFile);
    }

    $files = glob($snippetsDir . DIRECTORY_SEPARATOR . '*.php') ?: [];
    natcasesort($files);

    $snippets = [];
    $skipped = [];
    $scopeCounts = [];

    foreach ($files as $filePath) {
        $baseName = basename($filePath);
        $fileMeta = manifestForFile($manifest, $baseName);

        if (($fileMeta['exclude'] ?? false) === true) {
            $skipped[] = $baseName;
            continue;
        }

        $code = file_get_contents($filePath);
        if ($code === false) {
            fail("Impossible de lire le fichier: {$filePath}");
        }

        $code = stripUtf8Bom($code);

        $autoMeta = extractMetadataFromSnippetContent($code);

        $scope = (string)($fileMeta['scope'] ?? $autoMeta['scope'] ?? inferScopeFromFilename($baseName));

        $autoName = pathinfo($baseName, PATHINFO_FILENAME);
        $name = isset($fileMeta['name'])
            ? (string)$fileMeta['name']
            : formatImportSnippetName($autoName, $autoMeta);

        $snippet = [
            'name'  => $name,
            'code'  => normalizeLineEndings($code),
            'scope' => $scope,
        ];

        if (!empty($fileMeta['desc'])) {
            $snippet['desc'] = (string)$fileMeta['desc'];
        }

        if (!empty($fileMeta['description'])) {
            $snippet['description'] = (string)$fileMeta['description'];
        }

        if (empty($snippet['desc']) && empty($snippet['description'])) {
            $autoDesc = buildAutoDescription($autoMeta);
            if ($autoDesc !== null) {
                $snippet['desc'] = $autoDesc;
                $snippet['description'] = $autoDesc;
            }
        }

        if (array_key_exists('priority', $fileMeta) && $fileMeta['priority'] !== null && $fileMeta['priority'] !== '') {
            $snippet['priority'] = (int)$fileMeta['priority'];
        }

        if (!empty($fileMeta['tags'])) {
            $tags = $fileMeta['tags'];
            if (is_array($tags)) {
                $snippet['tags'] = array_values(array_map('strval', $tags));
            } elseif (is_string($tags)) {
                $snippet['tags'] = array_values(
                    array_filter(
                        array_map('trim', explode(',', $tags)),
                        static fn(string $tag): bool => $tag !== ''
                    )
                );
            }
        }

        if (isset($fileMeta['modified'])) {
            $snippet['modified'] = (string)$fileMeta['modified'];
        }

        if (isset($fileMeta['cloud_id'])) {
            $snippet['cloud_id'] = (string)$fileMeta['cloud_id'];
        }

        if (isset($fileMeta['shared_network'])) {
            $snippet['shared_network'] = (bool)$fileMeta['shared_network'];
        }

        // Le champ "active" n'est généralement pas importé par le flux JSON classique,
        // mais on accepte la config pour préparer une future sync via API.
        if (isset($fileMeta['active'])) {
            $snippet['active'] = (bool)$fileMeta['active'];
        }

        $snippets[] = $snippet;
        $scopeCounts[$scope] = ($scopeCounts[$scope] ?? 0) + 1;
    }

    if ($snippets === []) {
        fail('Aucun snippet exportable trouve.');
    }

    $payload = [
        'generator'    => 'Local Code Snippets Sync Builder (Git -> WordPress)',
        'date_created' => gmdate('Y-m-d H:i'),
        'snippets'     => $snippets,
    ];

    $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        fail('Echec JSON: ' . json_last_error_msg());
    }

    $outDir = dirname($outputFile);
    if (!is_dir($outDir) && !mkdir($outDir, 0777, true) && !is_dir($outDir)) {
        fail("Impossible de creer le dossier de sortie: {$outDir}");
    }

    if (file_put_contents($outputFile, $json . PHP_EOL) === false) {
        fail("Impossible d'ecrire le fichier de sortie: {$outputFile}");
    }

    echo "Export JSON genere: {$outputFile}\n";
    echo "Snippets inclus: " . count($snippets) . "\n";

    ksort($scopeCounts);
    foreach ($scopeCounts as $scope => $count) {
        echo "  - {$scope}: {$count}\n";
    }

    if ($skipped !== []) {
        echo "Snippets ignores via manifest: " . count($skipped) . "\n";
    }
}

function parseOptions(array $argv): array
{
    $options = [];

    foreach (array_slice($argv, 1) as $arg) {
        if (!str_starts_with($arg, '--')) {
            continue;
        }

        $arg = substr($arg, 2);
        [$key, $value] = array_pad(explode('=', $arg, 2), 2, null);

        if ($value === null) {
            $options[$key] = true;
            continue;
        }

        $options[$key] = $value;
    }

    if (!empty($options['help'])) {
        echo "Usage:\n";
        echo "  php scripts/build_code_snippets_import.php [--snippets-dir=WP_Snippets] [--out=build/code-snippets-import.json] [--manifest=sync/snippets.manifest.json]\n";
        exit(0);
    }

    return $options;
}

function resolvePath(string $root, string $path): string
{
    if ($path === '') {
        return $root;
    }

    if ($path[0] === '/' || preg_match('/^[A-Za-z]:[\\\\\\/]/', $path) === 1) {
        return $path;
    }

    return $root . DIRECTORY_SEPARATOR . $path;
}

function loadManifest(string $manifestFile): array
{
    if (!is_file($manifestFile)) {
        fail("Manifest introuvable: {$manifestFile}");
    }

    $raw = file_get_contents($manifestFile);
    if ($raw === false) {
        fail("Impossible de lire le manifest: {$manifestFile}");
    }

    $data = json_decode($raw, true);
    if (!is_array($data)) {
        fail("Manifest JSON invalide: {$manifestFile}");
    }

    return $data;
}

function manifestForFile(array $manifest, string $baseName): array
{
    $withoutExt = pathinfo($baseName, PATHINFO_FILENAME);

    $defaults = [];
    if (isset($manifest['_defaults']) && is_array($manifest['_defaults'])) {
        $defaults = $manifest['_defaults'];
    }

    $fromFile = (isset($manifest[$baseName]) && is_array($manifest[$baseName])) ? $manifest[$baseName] : [];
    $fromName = (isset($manifest[$withoutExt]) && is_array($manifest[$withoutExt])) ? $manifest[$withoutExt] : [];

    return array_merge($defaults, $fromFile, $fromName);
}

function inferScopeFromFilename(string $baseName): string
{
    $rules = [
        'WP_ADMIN - '    => 'admin',
        'WP_POST - '     => 'front-end',
        'WP_THEME - '    => 'global',
        'WP_PLUGIN - '   => 'global',
        'WP_OPTI - '     => 'front-end',
        'WP_Shortcode'   => 'global',
        'WP_SHORTCODE'   => 'global',
    ];

    foreach ($rules as $prefix => $scope) {
        if (str_starts_with($baseName, $prefix)) {
            return $scope;
        }
    }

    return 'global';
}

function stripUtf8Bom(string $content): string
{
    if (str_starts_with($content, "\xEF\xBB\xBF")) {
        return substr($content, 3);
    }

    return $content;
}

function extractMetadataFromSnippetContent(string $content): array
{
    $meta = [];

    $fields = [
        'Scope' => 'scope',
        'Display name' => 'display_name',
        'Resume fonctionnalites' => 'summary',
        'Features detectees' => 'features',
        'Hooks WP' => 'hooks',
        'Fonctions clefs' => 'functions',
        'Cluster principal' => 'cluster',
        'Statut' => 'status',
        'Online active' => 'online_active',
    ];

    foreach ($fields as $label => $key) {
        if (preg_match('/^\s*\*\s+' . preg_quote($label, '/') . ':\s*(.+)\s*$/mi', $content, $m) === 1) {
            $meta[$key] = trim((string)$m[1]);
        }
    }

    return $meta;
}

function formatImportSnippetName(string $name, array $meta = []): string
{
    // Keep current human naming, but uppercase the leading family prefix for readability:
    // "Admin Menubar - Search - v4" => "ADMIN MENUBAR - Search - v4"
    $parts = explode(' - ', $name, 2);
    $emoji = inferFeatureEmoji($meta);

    if (count($parts) < 2) {
        return prependEmojiIfMissing($name, $emoji);
    }

    $parts[0] = mb_strtoupper($parts[0]);
    $formatted = implode(' - ', $parts);
    return prependEmojiIfMissing($formatted, $emoji);
}

function buildAutoDescription(array $meta): ?string
{
    $lines = [];
    $emoji = inferFeatureEmoji($meta);

    if (!empty($meta['summary'])) {
        $summary = (string)$meta['summary'];
        $lines[] = $emoji !== null ? ($emoji . ' ' . $summary) : $summary;
    }

    if (!empty($meta['features']) && mb_strtolower((string)$meta['features']) !== 'aucun') {
        $lines[] = 'Features: ' . shortenCsv((string)$meta['features'], 6);
    }

    if (!empty($meta['hooks']) && mb_strtolower((string)$meta['hooks']) !== 'aucun') {
        $lines[] = 'Hooks: ' . shortenCsv((string)$meta['hooks'], 5);
    }

    if (!empty($meta['functions']) && mb_strtolower((string)$meta['functions']) !== 'aucun') {
        $lines[] = 'Fonctions: ' . shortenCsv((string)$meta['functions'], 4);
    }

    if (!empty($meta['scope'])) {
        $extra = 'Scope: ' . (string)$meta['scope'];
        if (!empty($meta['status'])) {
            $extra .= ' | Statut: ' . (string)$meta['status'];
        } elseif (!empty($meta['online_active'])) {
            $extra .= ' | Online active: ' . (string)$meta['online_active'];
        }
        $lines[] = $extra;
    }

    $lines = array_values(array_filter(array_map('trim', $lines), static fn(string $line): bool => $line !== ''));
    if ($lines === []) {
        return null;
    }

    return implode("\n", $lines);
}

function inferFeatureEmoji(array $meta): ?string
{
    $cluster = mb_strtolower((string)($meta['cluster'] ?? ''));
    $features = mb_strtolower((string)($meta['features'] ?? ''));
    $display = mb_strtolower((string)($meta['display_name'] ?? ''));
    $scope = mb_strtolower((string)($meta['scope'] ?? ''));
    $haystack = $cluster . ' | ' . $features . ' | ' . $display;

    $clusterMap = [
        'scheduler_posts' => '📅',
        'tracking_analytics' => '📊',
        'search_ui' => '🔎',
        'media_images' => '🖼️',
        'shortcode_preview' => '🔗',
        'post_footer_ui' => '🦶',
        'gutenberg_editor' => '✍️',
        'admin_menubar' => '🧭',
        'admin_columns_list' => '📋',
        'admin_ui_settings' => '⚙️',
        'links_external' => '🔗',
        'performance_optimization' => '⚡',
        'misc_utilities' => '🧰',
        'taxonomy_tags' => '🏷️',
        'rss_feed' => '📰',
    ];

    if ($cluster !== '' && isset($clusterMap[$cluster])) {
        return $clusterMap[$cluster];
    }

    $rules = [
        'calendar' => '📅',
        'analytics' => '📊',
        'counter' => '📊',
        'search' => '🔎',
        'featured image' => '🖼️',
        'image' => '🖼️',
        'shortcode' => '🧩',
        'external links' => '🔗',
        'footer' => '🦶',
        'gutenberg' => '✍️',
        'menubar' => '🧭',
        'columns' => '📋',
        'taxonomy' => '🏷️',
        'tags' => '🏷️',
        'rss' => '📰',
        'opti' => '⚡',
        'utilities' => '🧰',
    ];

    foreach ($rules as $needle => $emoji) {
        if ($needle !== '' && str_contains($haystack, $needle)) {
            return $emoji;
        }
    }

    return match ($scope) {
        'admin' => '🛠️',
        'front-end' => '🌐',
        'global' => '🧩',
        default => null,
    };
}

function prependEmojiIfMissing(string $text, ?string $emoji): string
{
    if ($emoji === null) {
        return $text;
    }

    // If it already starts with a symbol/emoji, leave it.
    if (preg_match('/^\p{So}|\p{Emoji}/u', $text) === 1) {
        return $text;
    }

    return $emoji . ' ' . $text;
}

function shortenCsv(string $value, int $limit): string
{
    $parts = array_values(array_filter(array_map('trim', explode(',', $value)), static fn(string $s): bool => $s !== ''));
    if ($parts === []) {
        return trim($value);
    }

    $shown = array_slice($parts, 0, $limit);
    $text = implode(', ', $shown);
    if (count($parts) > $limit) {
        $text .= ', …';
    }

    return $text;
}

function normalizeLineEndings(string $content): string
{
    return str_replace(["\r\n", "\r"], "\n", $content);
}

function fail(string $message): void
{
    fwrite(STDERR, "[ERREUR] {$message}\n");
    exit(1);
}
