<?php
declare(strict_types=1);

/**
 * Push local Code Snippets import JSON to WordPress Code Snippets REST API.
 *
 * Usage:
 *   php CODE_SNIPPETS_SYNC/scripts/push_code_snippets_rest.php \
 *     --site=https://example.com \
 *     --user=admin \
 *     --app-password="xxxx xxxx xxxx xxxx xxxx xxxx" \
 *     --import-json=CODE_SNIPPETS_SYNC/imports/IMPORT-WORDPRESS.json
 *
 * Notes:
 * - Requires WordPress Application Passwords enabled.
 * - Requires Code Snippets REST API route enabled/available.
 * - Upsert strategy: match by exact snippet name.
 */

main($argv);

function main(array $argv): void
{
    $root = dirname(__DIR__, 2);
    $options = parseArgs($argv);

    if (isset($options['help'])) {
        usage();
        exit(0);
    }

    $site = rtrim((string)($options['site'] ?? getenv('WP_SITE_URL') ?: ''), '/');
    $user = (string)($options['user'] ?? getenv('WP_SYNC_USER') ?: '');
    $appPassword = (string)($options['app-password'] ?? getenv('WP_APP_PASSWORD') ?: '');
    $importJson = resolvePath($root, (string)($options['import-json'] ?? 'CODE_SNIPPETS_SYNC/imports/IMPORT-WORDPRESS.json'));
    $endpointOverride = isset($options['endpoint']) ? trim((string)$options['endpoint']) : null;
    $dryRun = array_key_exists('dry-run', $options);
    $verbose = array_key_exists('verbose', $options);

    if ($site === '' || $user === '' || $appPassword === '') {
        fail("Options requises: --site, --user, --app-password (ou variables env WP_SITE_URL, WP_SYNC_USER, WP_APP_PASSWORD)");
    }
    if (!is_file($importJson)) {
        fail("Fichier import JSON introuvable: {$importJson}");
    }

    $payload = json_decode((string)file_get_contents($importJson), true);
    if (!is_array($payload) || !isset($payload['snippets']) || !is_array($payload['snippets'])) {
        fail("JSON import invalide (champ 'snippets' manquant): {$importJson}");
    }
    /** @var array<int,array<string,mixed>> $localSnippets */
    $localSnippets = $payload['snippets'];

    $http = new HttpClient($site, $user, $appPassword, $verbose);

    $endpoint = $endpointOverride ?: discoverEndpoint($http);
    if ($endpoint === '') {
        fail("Route REST Code Snippets introuvable. Vérifie que le plugin expose l'API REST.");
    }

    echo "Site: {$site}\n";
    echo "Endpoint: {$endpoint}\n";
    echo "Mode: " . ($dryRun ? 'DRY-RUN' : 'PUSH') . "\n";

    $remoteSnippets = fetchAllRemoteSnippets($http, $endpoint);
    $remoteByName = [];
    foreach ($remoteSnippets as $row) {
        $name = (string)($row['name'] ?? '');
        if ($name !== '' && !isset($remoteByName[$name])) {
            $remoteByName[$name] = $row;
        }
    }

    echo "Remote snippets detectes: " . count($remoteSnippets) . "\n";
    echo "Local snippets a pousser: " . count($localSnippets) . "\n";

    $created = 0;
    $updated = 0;
    $skipped = 0;
    $errors = 0;

    foreach ($localSnippets as $index => $snippet) {
        $name = trim((string)($snippet['name'] ?? ''));
        $code = (string)($snippet['code'] ?? '');
        if ($name === '' || $code === '') {
            $errors++;
            echo "[ERROR] Snippet local #{$index} invalide (name/code manquant)\n";
            continue;
        }

        $restPayload = buildRestPayload($snippet);

        if (isset($remoteByName[$name])) {
            $remote = $remoteByName[$name];
            $remoteId = (int)($remote['id'] ?? 0);
            if ($remoteId <= 0) {
                $errors++;
                echo "[ERROR] '{$name}' existe a distance mais sans id exploitable\n";
                continue;
            }

            if ($dryRun) {
                echo "[UPDATE] #{$remoteId} {$name}\n";
                $updated++;
                continue;
            }

            $ok = updateRemoteSnippet($http, $endpoint, $remoteId, $restPayload);
            if ($ok) {
                echo "[UPDATE] #{$remoteId} {$name}\n";
                $updated++;
            } else {
                echo "[ERROR] update {$name}\n";
                $errors++;
            }
            continue;
        }

        if ($dryRun) {
            echo "[CREATE] {$name}\n";
            $created++;
            continue;
        }

        $createdRow = createRemoteSnippet($http, $endpoint, $restPayload);
        if (is_array($createdRow)) {
            $remoteId = (int)($createdRow['id'] ?? 0);
            echo "[CREATE] " . ($remoteId > 0 ? "#{$remoteId} " : '') . "{$name}\n";
            $created++;
            if ($remoteId > 0) {
                $remoteByName[$name] = $createdRow;
            }
        } else {
            echo "[ERROR] create {$name}\n";
            $errors++;
        }
    }

    echo "\nResume:\n";
    echo "  Creates: {$created}\n";
    echo "  Updates: {$updated}\n";
    echo "  Skipped: {$skipped}\n";
    echo "  Errors : {$errors}\n";

    if ($errors > 0) {
        exit(1);
    }
}

function buildRestPayload(array $snippet): array
{
    $desc = trim((string)($snippet['description'] ?? $snippet['desc'] ?? ''));
    $scope = (string)($snippet['scope'] ?? 'global');

    $payload = [
        'name' => (string)$snippet['name'],
        'code' => (string)$snippet['code'],
        // Compat: some versions may use desc, others description.
        'desc' => $desc,
        'description' => $desc,
        'scope' => normalizeScope($scope),
    ];

    if (isset($snippet['priority'])) {
        $payload['priority'] = (int)$snippet['priority'];
    }
    if (isset($snippet['active'])) {
        $payload['active'] = (bool)$snippet['active'];
    }

    return $payload;
}

function normalizeScope(string $scope): string
{
    $scope = strtolower(trim($scope));
    return match ($scope) {
        'admin', 'front-end', 'global' => $scope,
        'frontend', 'front' => 'front-end',
        default => 'global',
    };
}

function discoverEndpoint(HttpClient $http): string
{
    [$status, $body] = $http->request('GET', '/wp-json');
    if ($status < 200 || $status >= 300) {
        // Fallback 1: plain REST route query style (common on some hosts/permalinks).
        [$altStatus, $altBody] = $http->request('GET', '/?rest_route=/');
        if ($altStatus >= 200 && $altStatus < 300) {
            return discoverEndpointFromIndexBody($altBody, true);
        }

        // Fallback 2: probe most likely collection endpoints directly.
        foreach ([
            '/wp-json/code-snippets/v1/snippets',
            '/?rest_route=/code-snippets/v1/snippets',
        ] as $probe) {
            [$probeStatus] = $http->request('GET', $probe);
            if ($probeStatus >= 200 && $probeStatus < 300) {
                return $probe;
            }
        }

        fail("Impossible de lire /wp-json (HTTP {$status}) et endpoint Code Snippets introuvable");
    }

    return discoverEndpointFromIndexBody($body, false);
}

function discoverEndpointFromIndexBody(string $body, bool $queryStyleFallback): string
{
    $json = json_decode($body, true);
    if (!is_array($json)) {
        fail("Reponse index REST invalide (JSON attendu)");
    }

    $routes = $json['routes'] ?? null;
    if (!is_array($routes)) {
        return $queryStyleFallback
            ? '/?rest_route=/code-snippets/v1/snippets'
            : '/wp-json/code-snippets/v1/snippets';
    }

    $candidates = [];
    foreach (array_keys($routes) as $route) {
        if (!is_string($route)) {
            continue;
        }
        $r = strtolower($route);
        if (str_contains($r, 'code-snippets') && str_contains($r, '/snippets')) {
            $candidates[] = $route;
        }
    }

    usort($candidates, static function (string $a, string $b): int {
        $scoreA = endpointScore($a);
        $scoreB = endpointScore($b);
        if ($scoreA === $scoreB) {
            return strcmp($a, $b);
        }
        return $scoreB <=> $scoreA;
    });

    foreach ($candidates as $route) {
        // Prefer collection endpoint over item endpoint regex route.
        if (!str_contains($route, '(?P<')) {
            return prefixRestIndexStyle($route, $queryStyleFallback);
        }
    }

    return $candidates !== []
        ? prefixRestIndexStyle($candidates[0], $queryStyleFallback)
        : ($queryStyleFallback ? '/?rest_route=/code-snippets/v1/snippets' : '/wp-json/code-snippets/v1/snippets');
}

function endpointScore(string $route): int
{
    $score = 0;
    if (str_contains($route, '/code-snippets/')) {
        $score += 10;
    }
    if (str_contains($route, '/v1/')) {
        $score += 5;
    }
    if (str_ends_with($route, '/snippets')) {
        $score += 8;
    }
    return $score;
}

function prefixRestIndexStyle(string $route, bool $queryStyleFallback = false): string
{
    if ($queryStyleFallback) {
        $route = ltrim($route, '/');
        $route = str_starts_with($route, 'wp-json/') ? substr($route, 8) : $route;
        return '/?rest_route=/' . ltrim($route, '/');
    }
    return str_starts_with($route, '/wp-json/') ? $route : '/wp-json' . (str_starts_with($route, '/') ? '' : '/') . $route;
}

/**
 * @return array<int,array<string,mixed>>
 */
function fetchAllRemoteSnippets(HttpClient $http, string $endpoint): array
{
    $all = [];

    // Try paginated first (WP REST style).
    $page = 1;
    $usedPagination = false;
    while (true) {
        $sep = str_contains($endpoint, '?') ? '&' : '?';
        [$status, $body, $headers] = $http->request('GET', $endpoint . $sep . 'per_page=100&page=' . $page, true);
        if ($status >= 400) {
            break;
        }

        $rows = normalizeSnippetList($body);
        $usedPagination = true;
        foreach ($rows as $row) {
            $all[] = $row;
        }

        $totalPages = (int)($headers['x-wp-totalpages'] ?? $headers['x-wp-total-pages'] ?? 0);
        if ($totalPages > 0 && $page >= $totalPages) {
            break;
        }
        if (count($rows) < 100) {
            break;
        }
        $page++;
        if ($page > 50) {
            break;
        }
    }

    if ($usedPagination && $all !== []) {
        return $all;
    }

    [$status, $body] = $http->request('GET', $endpoint);
    if ($status < 200 || $status >= 300) {
        fail("Impossible de lister les snippets distants (HTTP {$status}) via {$endpoint}");
    }

    return normalizeSnippetList($body);
}

/**
 * @return array<int,array<string,mixed>>
 */
function normalizeSnippetList(string $body): array
{
    $json = json_decode($body, true);
    if (!is_array($json)) {
        fail("Reponse REST invalide (JSON attendu)");
    }

    if (array_is_list($json)) {
        return array_values(array_filter($json, 'is_array'));
    }

    foreach (['snippets', 'items', 'data'] as $key) {
        if (isset($json[$key]) && is_array($json[$key])) {
            return array_values(array_filter($json[$key], 'is_array'));
        }
    }

    // Single object fallback.
    if (isset($json['id']) && isset($json['name'])) {
        return [$json];
    }

    return [];
}

function updateRemoteSnippet(HttpClient $http, string $endpoint, int $id, array $payload): bool
{
    $itemEndpoint = rtrim($endpoint, '/') . '/' . $id;
    foreach (['PATCH', 'PUT', 'POST'] as $method) {
        [$status, $body] = $http->request($method, $itemEndpoint, false, $payload);
        if ($status >= 200 && $status < 300) {
            return true;
        }
        if ($status === 404 || $status === 405) {
            continue;
        }
        fwrite(STDERR, "[REST] {$method} {$itemEndpoint} -> HTTP {$status}\n{$body}\n");
    }
    return false;
}

/**
 * @return array<string,mixed>|null
 */
function createRemoteSnippet(HttpClient $http, string $endpoint, array $payload): ?array
{
    [$status, $body] = $http->request('POST', $endpoint, false, $payload);
    if ($status < 200 || $status >= 300) {
        fwrite(STDERR, "[REST] POST {$endpoint} -> HTTP {$status}\n{$body}\n");
        return null;
    }
    $json = json_decode($body, true);
    return is_array($json) ? $json : [];
}

function parseArgs(array $argv): array
{
    $options = [];
    foreach (array_slice($argv, 1) as $arg) {
        if ($arg === '--help' || $arg === '-h') {
            $options['help'] = true;
            continue;
        }
        if ($arg === '--dry-run' || $arg === '--verbose') {
            $options[ltrim($arg, '-')] = true;
            continue;
        }
        if (!str_starts_with($arg, '--') || !str_contains($arg, '=')) {
            continue;
        }
        [$key, $value] = explode('=', substr($arg, 2), 2);
        $options[$key] = $value;
    }
    return $options;
}

function resolvePath(string $root, string $path): string
{
    if ($path === '') {
        return $path;
    }
    if ($path[0] === '/' || preg_match('~^[A-Za-z]:[\\\\/]~', $path)) {
        return $path;
    }
    return $root . DIRECTORY_SEPARATOR . $path;
}

function usage(): void
{
    echo "Usage:\n";
    echo "  php CODE_SNIPPETS_SYNC/scripts/push_code_snippets_rest.php \\\n";
    echo "    --site=https://example.com --user=admin --app-password=\"xxxx xxxx\" \\\n";
    echo "    [--import-json=CODE_SNIPPETS_SYNC/imports/IMPORT-WORDPRESS.json] [--endpoint=/wp-json/code-snippets/v1/snippets] [--dry-run] [--verbose]\n";
    echo "\n";
    echo "Env vars accepted: WP_SITE_URL, WP_SYNC_USER, WP_APP_PASSWORD\n";
}

function fail(string $message): void
{
    fwrite(STDERR, $message . "\n");
    exit(1);
}

final class HttpClient
{
    private string $site;
    private string $auth;
    private bool $verbose;

    public function __construct(string $site, string $user, string $appPassword, bool $verbose = false)
    {
        $this->site = rtrim($site, '/');
        $this->auth = 'Basic ' . base64_encode($user . ':' . $appPassword);
        $this->verbose = $verbose;
    }

    /**
     * @param array<string,mixed>|null $json
     * @return array{0:int,1:string}|array{0:int,1:string,2:array<string,string>}
     */
    public function request(string $method, string $path, bool $withHeaders = false, ?array $json = null): array
    {
        $url = str_starts_with($path, 'http://') || str_starts_with($path, 'https://')
            ? $path
            : $this->site . (str_starts_with($path, '/') ? '' : '/') . $path;

        $ch = curl_init($url);
        $respHeaders = [];

        $headers = [
            'Authorization: ' . $this->auth,
            'Accept: application/json',
        ];

        if ($json !== null) {
            $body = json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 45,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; CLM-CodeSnippetsSync/1.0; +https://mondary.design)',
            CURLOPT_HEADERFUNCTION => static function ($curl, string $headerLine) use (&$respHeaders): int {
                $len = strlen($headerLine);
                $parts = explode(':', $headerLine, 2);
                if (count($parts) === 2) {
                    $respHeaders[strtolower(trim($parts[0]))] = trim($parts[1]);
                }
                return $len;
            },
        ]);

        $body = curl_exec($ch);
        if ($body === false) {
            $err = curl_error($ch);
            if (PHP_VERSION_ID < 80500) {
                curl_close($ch);
            }
            fail("Erreur HTTP cURL: {$err}");
        }

        $status = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        if (PHP_VERSION_ID < 80500) {
            curl_close($ch);
        }

        if ($this->verbose) {
            fwrite(STDERR, "[HTTP] {$method} {$url} -> {$status}\n");
        }

        if ($withHeaders) {
            return [$status, (string)$body, $respHeaders];
        }

        return [$status, (string)$body];
    }
}
