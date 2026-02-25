<?php

declare(strict_types=1);

/**
 * Compare un export JSON du plugin "Code Snippets" avec les fichiers locaux WP_Snippets/*.php
 * sans rien modifier.
 *
 * Usage:
 *   php scripts/compare_code_snippets_export.php --export=/chemin/export-code-snippets.json
 *   php scripts/compare_code_snippets_export.php --export=build/site-export.json --snippets-dir=WP_Snippets --report=build/compare-report.json
 */

const DEFAULT_LOCAL_SNIPPETS_DIR = 'WP_Snippets';

main($argv);

function main(array $argv): void
{
    $options = parseOptions($argv);
    $root = getcwd() ?: '.';

    if (empty($options['export'])) {
        fail("Option requise: --export=/chemin/vers/export.json");
    }

    $exportFile = resolvePath($root, (string)$options['export']);
    $snippetsDir = resolvePath($root, (string)($options['snippets-dir'] ?? DEFAULT_LOCAL_SNIPPETS_DIR));
    $reportFile = isset($options['report']) ? resolvePath($root, (string)$options['report']) : null;

    if (!is_file($exportFile)) {
        fail("Fichier export introuvable: {$exportFile}");
    }
    if (!is_dir($snippetsDir)) {
        fail("Dossier snippets introuvable: {$snippetsDir}");
    }

    $siteSnippets = loadSiteExport($exportFile);
    $localSnippets = loadLocalSnippets($snippetsDir);

    $allNames = array_unique(array_merge(array_keys($localSnippets), array_keys($siteSnippets)));
    natcasesort($allNames);

    $results = [
        'same' => [],
        'different' => [],
        'local_only' => [],
        'site_only' => [],
    ];

    foreach ($allNames as $name) {
        $local = $localSnippets[$name] ?? null;
        $site = $siteSnippets[$name] ?? null;

        if ($local && !$site) {
            $results['local_only'][] = [
                'name' => $name,
                'file' => $local['file'],
                'scope' => $local['scope'],
            ];
            continue;
        }

        if ($site && !$local) {
            $results['site_only'][] = [
                'name' => $name,
                'scope' => $site['scope'],
            ];
            continue;
        }

        $sameCode = hash_equals($local['code_hash'], $site['code_hash']);
        $sameScope = $local['scope'] === $site['scope'];

        if ($sameCode && $sameScope) {
            $results['same'][] = [
                'name' => $name,
                'scope' => $local['scope'],
            ];
            continue;
        }

        $diff = [
            'name' => $name,
            'file' => $local['file'],
            'local_scope' => $local['scope'],
            'site_scope' => $site['scope'],
            'code_same' => $sameCode,
            'scope_same' => $sameScope,
        ];

        if (!$sameCode) {
            $diff['local_hash'] = $local['code_hash'];
            $diff['site_hash'] = $site['code_hash'];
            $diff['local_preview'] = previewOneLine($local['raw_code']);
            $diff['site_preview'] = previewOneLine($site['raw_code']);
        }

        $results['different'][] = $diff;
    }

    printSummary($results, $exportFile);
    printExamples($results);

    if ($reportFile) {
        writeReport($reportFile, $exportFile, $snippetsDir, $results);
        echo "\nRapport JSON ecrit: {$reportFile}\n";
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
        $options[$key] = $value ?? true;
    }

    if (!empty($options['help'])) {
        echo "Usage:\n";
        echo "  php scripts/compare_code_snippets_export.php --export=build/site-export.json [--snippets-dir=WP_Snippets] [--report=build/compare-report.json]\n";
        exit(0);
    }

    return $options;
}

function loadSiteExport(string $exportFile): array
{
    $raw = file_get_contents($exportFile);
    if ($raw === false) {
        fail("Impossible de lire l'export: {$exportFile}");
    }

    $data = json_decode($raw, true);
    if (!is_array($data) || !isset($data['snippets']) || !is_array($data['snippets'])) {
        fail("Export JSON invalide (champ 'snippets' manquant): {$exportFile}");
    }

    $snippets = [];

    foreach ($data['snippets'] as $idx => $row) {
        if (!is_array($row)) {
            continue;
        }

        $name = trim((string)($row['name'] ?? ''));
        if ($name === '') {
            $name = "Snippet #{$idx}";
        }

        $code = normalizeSnippetCode((string)($row['code'] ?? ''));
        $scope = (string)($row['scope'] ?? 'global');

        $snippets[$name] = [
            'name' => $name,
            'scope' => $scope,
            'raw_code' => (string)($row['code'] ?? ''),
            'code_hash' => hash('sha256', $code),
        ];
    }

    return $snippets;
}

function loadLocalSnippets(string $snippetsDir): array
{
    $files = glob($snippetsDir . DIRECTORY_SEPARATOR . '*.php') ?: [];
    natcasesort($files);

    $snippets = [];

    foreach ($files as $filePath) {
        $base = basename($filePath);
        $name = pathinfo($base, PATHINFO_FILENAME);
        $raw = file_get_contents($filePath);
        if ($raw === false) {
            fail("Impossible de lire le fichier local: {$filePath}");
        }

        $snippets[$name] = [
            'name' => $name,
            'file' => $base,
            'scope' => inferScopeFromFilename($base),
            'raw_code' => $raw,
            'code_hash' => hash('sha256', normalizeSnippetCode($raw)),
        ];
    }

    return $snippets;
}

function normalizeSnippetCode(string $code): string
{
    $code = stripUtf8Bom($code);
    $code = str_replace(["\r\n", "\r"], "\n", $code);

    // Code Snippets stocke les snippets PHP sans balise d'ouverture/fermeture externes.
    $code = preg_replace('/^\s*<\?(php)?/i', '', $code) ?? $code;
    $code = preg_replace('/\?>\s*$/', '', $code) ?? $code;

    return trim($code) . "\n";
}

function previewOneLine(string $code, int $limit = 120): string
{
    $line = trim(preg_replace('/\s+/', ' ', stripUtf8Bom($code)) ?? '');
    if (strlen($line) <= $limit) {
        return $line;
    }
    return substr($line, 0, $limit - 1) . '…';
}

function printSummary(array $results, string $exportFile): void
{
    echo "Comparaison terminee (lecture seule)\n";
    echo "Export WordPress: {$exportFile}\n";
    echo "\n";
    echo "Identiques : " . count($results['same']) . "\n";
    echo "Differents : " . count($results['different']) . "\n";
    echo "Local uniquement : " . count($results['local_only']) . "\n";
    echo "Site uniquement : " . count($results['site_only']) . "\n";
}

function printExamples(array $results): void
{
    $max = 10;

    if ($results['different']) {
        echo "\nExemples DIFFERENTS (max {$max})\n";
        foreach (array_slice($results['different'], 0, $max) as $row) {
            $flags = [];
            if (!$row['code_same']) {
                $flags[] = 'code';
            }
            if (!$row['scope_same']) {
                $flags[] = 'scope';
            }
            echo " - {$row['name']} [" . implode(', ', $flags) . "]\n";
        }
    }

    if ($results['local_only']) {
        echo "\nExemples LOCAL ONLY (max {$max})\n";
        foreach (array_slice($results['local_only'], 0, $max) as $row) {
            echo " - {$row['name']}\n";
        }
    }

    if ($results['site_only']) {
        echo "\nExemples SITE ONLY (max {$max})\n";
        foreach (array_slice($results['site_only'], 0, $max) as $row) {
            echo " - {$row['name']}\n";
        }
    }
}

function writeReport(string $reportFile, string $exportFile, string $snippetsDir, array $results): void
{
    $payload = [
        'generated_at' => gmdate('c'),
        'export_file' => $exportFile,
        'local_snippets_dir' => $snippetsDir,
        'summary' => [
            'same' => count($results['same']),
            'different' => count($results['different']),
            'local_only' => count($results['local_only']),
            'site_only' => count($results['site_only']),
        ],
        'results' => $results,
    ];

    $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        fail('Echec JSON du rapport: ' . json_last_error_msg());
    }

    $dir = dirname($reportFile);
    if (!is_dir($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
        fail("Impossible de creer le dossier: {$dir}");
    }

    if (file_put_contents($reportFile, $json . PHP_EOL) === false) {
        fail("Impossible d'ecrire le rapport: {$reportFile}");
    }
}

function inferScopeFromFilename(string $baseName): string
{
    $rules = [
        'WP_ADMIN - '  => 'admin',
        'WP_POST - '   => 'front-end',
        'WP_OPTI - '   => 'front-end',
        'WP_THEME - '  => 'global',
        'WP_PLUGIN - ' => 'global',
        'WP_Shortcode' => 'global',
        'WP_SHORTCODE' => 'global',
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

function fail(string $message): void
{
    fwrite(STDERR, "[ERREUR] {$message}\n");
    exit(1);
}

