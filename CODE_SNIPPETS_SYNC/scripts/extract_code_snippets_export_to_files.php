<?php

declare(strict_types=1);

/**
 * Extrait un export JSON du plugin "Code Snippets" vers un dossier de fichiers PHP
 * représentant l'etat actuel "online".
 *
 * - Un fichier par snippet
 * - Sous-dossiers par statut (active/inactive) et scope
 * - Entete commentaire avec metadata (nom original, id, scope, active, modified, hash)
 * - Un index JSON est genere pour conserver le mapping
 *
 * Usage:
 *   php scripts/extract_code_snippets_export_to_files.php --export="/Users/me/Desktop/site.code-snippets.json"
 *   php scripts/extract_code_snippets_export_to_files.php --export=build/site-export.json --out-dir=WP_Snippets_Online_Current
 */

const DEFAULT_OUT_DIR = 'WP_Snippets_Online_Current';

main($argv);

function main(array $argv): void
{
    $opts = parseOptions($argv);
    $root = getcwd() ?: '.';

    if (empty($opts['export'])) {
        fail('Option requise: --export=/chemin/export.code-snippets.json');
    }

    $exportFile = resolvePath($root, (string)$opts['export']);
    $outDir = resolvePath($root, (string)($opts['out-dir'] ?? DEFAULT_OUT_DIR));
    $clean = !empty($opts['clean']);

    if (!is_file($exportFile)) {
        fail("Export introuvable: {$exportFile}");
    }

    $data = loadExport($exportFile);
    $snippets = $data['snippets'];

    if ($clean && is_dir($outDir)) {
        rrmdir($outDir);
    }
    if (!is_dir($outDir) && !mkdir($outDir, 0777, true) && !is_dir($outDir)) {
        fail("Impossible de creer le dossier de sortie: {$outDir}");
    }

    $written = [];
    $counts = [
        'total' => 0,
        'active' => 0,
        'inactive' => 0,
        'scopes' => [],
    ];

    $usedPaths = [];

    foreach ($snippets as $idx => $row) {
        if (!is_array($row)) {
            continue;
        }

        $name = trim((string)($row['name'] ?? ''));
        if ($name === '') {
            $name = 'Snippet ' . ($idx + 1);
        }

        $scope = sanitizeScope((string)($row['scope'] ?? 'global'));
        $active = array_key_exists('active', $row) ? (bool)$row['active'] : false;
        $statusDir = $active ? 'active' : 'inactive';

        $originalCode = (string)($row['code'] ?? '');
        $hash = hash('sha256', normalizeSnippetCode($originalCode));

        $subDir = $outDir . DIRECTORY_SEPARATOR . $statusDir . DIRECTORY_SEPARATOR . $scope;
        if (!is_dir($subDir) && !mkdir($subDir, 0777, true) && !is_dir($subDir)) {
            fail("Impossible de creer le sous-dossier: {$subDir}");
        }

        $idPart = isset($row['id']) ? 'id-' . preg_replace('/[^0-9]/', '', (string)$row['id']) : 'id-na';
        $slug = slugify($name);
        if ($slug === '') {
            $slug = 'snippet';
        }

        $baseFile = sprintf('%03d__%s__%s.php', $idx + 1, $idPart, $slug);
        $targetPath = uniquePath($subDir . DIRECTORY_SEPARATOR . $baseFile, $usedPaths);

        $content = buildOutputContent($originalCode, [
            'source_export_file' => $exportFile,
            'name' => $name,
            'id' => $row['id'] ?? null,
            'scope' => $scope,
            'active' => $active,
            'modified' => $row['modified'] ?? null,
            'priority' => $row['priority'] ?? null,
            'revision' => $row['revision'] ?? null,
            'desc' => $row['desc'] ?? null,
            'hash_sha256' => $hash,
        ]);

        if (file_put_contents($targetPath, $content) === false) {
            fail("Impossible d'ecrire: {$targetPath}");
        }

        $counts['total']++;
        if ($active) {
            $counts['active']++;
        } else {
            $counts['inactive']++;
        }
        $counts['scopes'][$scope] = ($counts['scopes'][$scope] ?? 0) + 1;

        $written[] = [
            'name' => $name,
            'id' => $row['id'] ?? null,
            'scope' => $scope,
            'active' => $active,
            'modified' => $row['modified'] ?? null,
            'priority' => $row['priority'] ?? null,
            'revision' => $row['revision'] ?? null,
            'hash_sha256' => $hash,
            'output_file' => relativePath($root, $targetPath),
            'output_abs' => $targetPath,
            'code_bytes' => strlen($originalCode),
        ];
    }

    ksort($counts['scopes']);

    $indexPayload = [
        'generated_at' => gmdate('c'),
        'source_export' => $exportFile,
        'output_dir' => $outDir,
        'summary' => $counts,
        'snippets' => $written,
    ];

    $indexFile = $outDir . DIRECTORY_SEPARATOR . '_index.code-snippets-online.json';
    $json = json_encode($indexPayload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        fail('Erreur JSON index: ' . json_last_error_msg());
    }
    file_put_contents($indexFile, $json . PHP_EOL);

    echo "Extraction terminee\n";
    echo "Export source: {$exportFile}\n";
    echo "Dossier cree: {$outDir}\n";
    echo "Index: {$indexFile}\n";
    echo "Snippets: {$counts['total']} (actifs: {$counts['active']}, inactifs: {$counts['inactive']})\n";
    foreach ($counts['scopes'] as $scopeName => $n) {
        echo "  - {$scopeName}: {$n}\n";
    }
}

function parseOptions(array $argv): array
{
    $opts = [];
    foreach (array_slice($argv, 1) as $arg) {
        if (!str_starts_with($arg, '--')) {
            continue;
        }
        $arg = substr($arg, 2);
        [$k, $v] = array_pad(explode('=', $arg, 2), 2, null);
        $opts[$k] = $v ?? true;
    }

    if (!empty($opts['help'])) {
        echo "Usage:\n";
        echo "  php scripts/extract_code_snippets_export_to_files.php --export=/path/export.code-snippets.json [--out-dir=WP_Snippets_Online_Current] [--clean]\n";
        exit(0);
    }

    return $opts;
}

function loadExport(string $file): array
{
    $raw = file_get_contents($file);
    if ($raw === false) {
        fail("Impossible de lire l'export: {$file}");
    }

    $data = json_decode($raw, true);
    if (!is_array($data) || !isset($data['snippets']) || !is_array($data['snippets'])) {
        fail("Export JSON invalide (champ snippets absent): {$file}");
    }

    return $data;
}

function buildOutputContent(string $originalCode, array $meta): string
{
    $header = buildMetadataComment($meta);
    $clean = stripUtf8Bom(str_replace(["\r\n", "\r"], "\n", $originalCode));

    if (preg_match('/^\s*<\?(php)?[^\n]*\n?/i', $clean, $m) === 1) {
        $openTag = $m[0];
        $rest = substr($clean, strlen($openTag));
        return $openTag . $header . ltrim($rest, "\n");
    }

    return $header . ltrim($clean, "\n");
}

function buildMetadataComment(array $m): string
{
    $lines = [];
    $lines[] = "/* ONLINE-CODE-SNIPPET-EXPORT";
    $lines[] = " * Nom original: " . (string)$m['name'];
    $lines[] = " * ID: " . valueOr($m['id'], 'n/a');
    $lines[] = " * Scope: " . (string)$m['scope'];
    $lines[] = " * Actif: " . (($m['active'] ?? false) ? 'oui' : 'non');
    $lines[] = " * Modifie (site): " . valueOr($m['modified'], 'n/a');
    $lines[] = " * Priorite: " . valueOr($m['priority'], 'n/a');
    $lines[] = " * Revision: " . valueOr($m['revision'], 'n/a');
    if (!empty($m['desc'])) {
        $desc = trim((string)$m['desc']);
        $desc = preg_replace('/\s+/', ' ', $desc) ?? $desc;
        $lines[] = " * Description: " . $desc;
    }
    $lines[] = " * Hash code (sha256): " . (string)$m['hash_sha256'];
    $lines[] = " * Export source: " . (string)$m['source_export_file'];
    $lines[] = " * Extrait le (UTC): " . gmdate('c');
    $lines[] = " */";

    return implode("\n", $lines) . "\n\n";
}

function normalizeSnippetCode(string $code): string
{
    $code = str_replace(["\r\n", "\r"], "\n", stripUtf8Bom($code));
    $code = preg_replace('/^\s*<\?(php)?/i', '', $code) ?? $code;
    $code = preg_replace('/\?>\s*$/', '', $code) ?? $code;
    return trim($code) . "\n";
}

function sanitizeScope(string $scope): string
{
    $scope = trim($scope);
    if ($scope === '') {
        return 'global';
    }
    return preg_replace('/[^a-z0-9._-]+/i', '-', strtolower($scope)) ?: 'global';
}

function slugify(string $s): string
{
    $s = trim($s);
    if ($s === '') {
        return 'snippet';
    }

    $s = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s) ?: $s;
    $s = strtolower($s);
    $s = preg_replace('/[^a-z0-9]+/', '-', $s) ?? $s;
    $s = trim($s, '-');

    return $s === '' ? 'snippet' : $s;
}

function uniquePath(string $path, array &$usedPaths): string
{
    $candidate = $path;
    $i = 2;
    while (isset($usedPaths[$candidate]) || file_exists($candidate)) {
        $dir = dirname($path);
        $base = pathinfo($path, PATHINFO_FILENAME);
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $candidate = $dir . DIRECTORY_SEPARATOR . $base . '__' . $i . ($ext ? '.' . $ext : '');
        $i++;
    }
    $usedPaths[$candidate] = true;
    return $candidate;
}

function rrmdir(string $dir): void
{
    if (!is_dir($dir)) {
        return;
    }
    $items = scandir($dir);
    if ($items === false) {
        return;
    }
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path)) {
            rrmdir($path);
        } else {
            @unlink($path);
        }
    }
    @rmdir($dir);
}

function relativePath(string $root, string $path): string
{
    $root = rtrim($root, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    if (str_starts_with($path, $root)) {
        return substr($path, strlen($root));
    }
    return $path;
}

function stripUtf8Bom(string $content): string
{
    return str_starts_with($content, "\xEF\xBB\xBF") ? substr($content, 3) : $content;
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

function valueOr($v, string $fallback): string
{
    if ($v === null || $v === '') {
        return $fallback;
    }
    if (is_bool($v)) {
        return $v ? 'true' : 'false';
    }
    return (string)$v;
}

function fail(string $message): void
{
    fwrite(STDERR, "[ERREUR] {$message}\n");
    exit(1);
}

