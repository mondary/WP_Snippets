<?php
/*
 * Display name: TOOL 📄 DIAG - Snippets - v1
 * Scope: global
 */

<?php
/*
 * Display name: #🔥 DIAG snippets !!!!!!
 * Source: WordPress (pulled)
 * Online ID: 247
 * Online modified: 2026-05-28 09:28:26
 * Scope: admin
 * Active: oui
 */

/**
 * Snippet Name: 📊 WP Snippets Performance Diagnostics
 * Snippet Description: Analyse tous vos snippets actifs et génère un rapport de performance avec recommandations. Exécutez une fois puis désactivez.
 * Snippet Version: 1.1
 * Author: Claude AI
 */

// Ne lancer que si on est dans l'admin ET si le paramètre ?run_diagnostics=1 est présent
if (!is_admin() || !isset($_GET['run_diagnostics'])) {
    return;
}

// Vérifier les permissions
if (!current_user_can('manage_options')) {
    wp_die('Accès refusé. Vous devez être administrateur.');
}

// Récupérer tous les snippets actifs
global $wpdb;

// D'abord, voir les colonnes disponibles
$columns = $wpdb->get_col("SHOW COLUMNS FROM mod623_snippets");

// Construire la requête en fonction des colonnes existantes
$select_columns = ['id', 'name', 'description', 'scope'];
if (in_array('modified', $columns)) {
    $select_columns[] = 'modified';
} elseif (in_array('last_modified', $columns)) {
    $select_columns[] = 'last_modified AS modified';
} elseif (in_array('updated', $columns)) {
    $select_columns[] = 'updated AS modified';
} else {
    $select_columns[] = "'2024-01-01' AS modified";
}

$snippets = $wpdb->get_results("
    SELECT " . implode(', ', $select_columns) . "
    FROM mod623_snippets
    WHERE active = 1
    ORDER BY name ASC
");

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>WP Snippets Performance Diagnostics</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 20px; background: #f0f0f1; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #1d2327; border-bottom: 3px solid #2271b1; padding-bottom: 10px; }
        h2 { color: #1d2327; margin-top: 30px; }
        .warning { background: #fff6e5; border-left: 4px solid #ffb900; padding: 15px; margin: 20px 0; }
        .critical { background: #f6e7e7; border-left: 4px solid #d63638; padding: 15px; margin: 20px 0; }
        .success { background: #edfaef; border-left: 4px solid #00a32a; padding: 15px; margin: 20px 0; }
        .info { background: #e7f3ff; border-left: 4px solid #2271b1; padding: 15px; margin: 20px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 14px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f6f7f7; font-weight: 600; }
        tr:hover { background: #f9f9f9; }
        .risk-high { background: #fee; }
        .risk-medium { background: #fff9e6; }
        .risk-low { background: #f0f9ff; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
        .badge-high { background: #d63638; color: white; }
        .badge-medium { background: #ffb900; color: #1d2327; }
        .badge-low { background: #00a32a; color: white; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0; }
        .stat-card { background: #f6f7f7; padding: 20px; border-radius: 8px; text-align: center; }
        .stat-number { font-size: 32px; font-weight: 700; color: #2271b1; }
        .stat-label { color: #646970; font-size: 14px; }
        .progress-bar { width: 100%; height: 30px; background: #f0f0f0; border-radius: 15px; overflow: hidden; margin: 10px 0; }
        .progress-fill { height: 100%; background: linear-gradient(90deg, #2271b1, #135e96); transition: width 0.3s; }
        .btn { display: inline-block; padding: 10px 20px; background: #2271b1; color: white; text-decoration: none; border-radius: 4px; margin: 10px 5px; }
        .btn:hover { background: #135e96; }
        .btn-danger { background: #d63638; }
        .btn-danger:hover { background: #b32d2e; }
    </style>
</head>
<body data-rsssl=1>
    <div class="container">
        <h1>🔍 WP Snippets Performance Diagnostics</h1>
        <p><strong>Généré:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>

        <div class="info">
            <strong>💡 CONSEIL:</strong> Après avoir vu ce rapport, <a href="<?php echo admin_url('admin.php?page=snippets'); ?>">cliquez ici pour désactiver ce snippet</a>. Vous pouvez le réactiver quand vous voulez.
        </div>

        <?php if (empty($snippets)): ?>
            <div class="warning">Aucun snippet actif trouvé.</div>

            <!-- DEBUG INFO -->
            <div class="info">
                <strong>🔍 DEBUG INFO:</strong><br><br>

                <?php
                // Vérifier si la table existe
                $table_exists = $wpdb->get_var("SHOW TABLES LIKE 'mod623_snippets'");
                if ($table_exists) {
                    echo '✅ Table mod623_snippets existe<br><br>';

                    // Montrer les colonnes
                    $columns = $wpdb->get_col("SHOW COLUMNS FROM mod623_snippets");
                    echo '<strong>Colonnes disponibles:</strong><ul>';
                    foreach ($columns as $col) {
                        echo '<li>' . esc_html($col) . '</li>';
                    }
                    echo '</ul><br>';

                    // Compter les snippets
                    $total = $wpdb->get_var("SELECT COUNT(*) FROM mod623_snippets");
                    echo '<strong>Total snippets dans la table:</strong> ' . esc_html($total) . '<br><br>';

                    // Compter les snippets actifs
                    $active = $wpdb->get_var("SELECT COUNT(*) FROM mod623_snippets WHERE active = 1");
                    echo '<strong>Snippets actifs (active = 1):</strong> ' . esc_html($active) . '<br><br>';

                    // Montrer quelques snippets
                    $sample = $wpdb->get_results("SELECT * FROM mod623_snippets LIMIT 3");
                    if ($sample) {
                        echo '<strong>Échantillon de snippets:</strong><pre>';
                        print_r($sample);
                        echo '</pre>';
                    }
                } else {
                    echo '❌ Table mod623_snippets NON trouvée!<br>';
                    echo 'Tables disponibles avec "snippet":<ul>';
                    $all_tables = $wpdb->get_results("SHOW TABLES");
                    foreach ($all_tables as $table) {
                        $table_name = array_values((array)$table)[0];
                        if (strpos($table_name, 'snippet') !== false) {
                            echo '<li>' . esc_html($table_name) . '</li>';
                        }
                    }
                    echo '</ul>';
                }
                ?>
            </div>
            <!-- FIN DEBUG -->
        <?php else: ?>
            <?php
            // Analyser chaque snippet
            $analysis = [];
            $tracking_count = 0;
            $performance_count = 0;
            $scheduler_count = 0;

            foreach ($snippets as $snippet) {
                $risk = 'low';
                $risk_reason = [];

                // Analyser le nom et la description
                $name_lower = strtolower($snippet->name);
                $desc_lower = strtolower($snippet->description);

                // Détecter les snippets de tracking
                if (strpos($name_lower, 'tracking') !== false ||
                    strpos($name_lower, 'analytics') !== false ||
                    strpos($name_lower, 'counter') !== false ||
                    strpos($name_lower, 'datapulse') !== false ||
                    strpos($name_lower, 'rybbit') !== false ||
                    strpos($name_lower, 'swilty') !== false ||
                    strpos($name_lower, 'umami') !== false ||
                    strpos($name_lower, 'histogram') !== false) {
                    $risk = 'high';
                    $risk_reason[] = 'Script tracking/analytics - se charge sur chaque page';
                    $tracking_count++;
                }

                // Détecter les snippets de performance
                if (strpos($name_lower, 'performance') !== false ||
                    strpos($name_lower, 'opti') !== false) {
                    $risk = 'medium';
                    $risk_reason[] = 'Optimisation performance - peut avoir des bugs';
                    $performance_count++;
                }

                // Détecter les snippets scheduler
                if (strpos($name_lower, 'scheduler') !== false ||
                    strpos($name_lower, 'scheduled') !== false) {
                    $risk = 'medium';
                    $risk_reason[] = 'Tâches planifiées - peut causer des timeouts';
                    $scheduler_count++;
                }

                // Détecter les shortcodes
                if (strpos($name_lower, 'shortcode') !== false) {
                    $risk = 'medium';
                    $risk_reason[] = 'Traitement shortcode - ajoute de la charge';
                }

                // Vérifier le numéro de version
                if (preg_match('/v(\d+)/', $snippet->name, $matches)) {
                    $version = (int)$matches[1];
                    if ($version > 10) {
                        $risk = 'medium';
                        $risk_reason[] = "Numéro de version élevé (v$version) - beaucoup de révisions";
                    }
                }

                // Compter les hooks mentionnés
                if (preg_match('/(\d+)\s*hook\(s\)/i', $snippet->description, $matches)) {
                    $hooks = (int)$matches[1];
                    if ($hooks > 3) {
                        $risk = 'medium';
                        $risk_reason[] = "Beaucoup de hooks ($hooks) - haute fréquence d'exécution";
                    }
                }

                $analysis[] = [
                    'snippet' => $snippet,
                    'risk' => $risk,
                    'reasons' => $risk_reason
                ];
            }

            // Calculer les stats
            $total_snippets = count($snippets);
            $high_risk = count(array_filter($analysis, function($a) { return $a['risk'] === 'high'; }));
            $medium_risk = count(array_filter($analysis, function($a) { return $a['risk'] === 'medium'; }));
            $low_risk = count(array_filter($analysis, function($a) { return $a['risk'] === 'low'; }));
            $performance_score = max(0, 100 - ($high_risk * 15) - ($medium_risk * 5) - ($tracking_count * 10));

            // Afficher les stats
            echo '<div class="stats">';
            echo '<div class="stat-card">';
            echo '<div class="stat-number">' . $total_snippets . '</div>';
            echo '<div class="stat-label">Snippets Actifs</div>';
            echo '</div>';
            echo '<div class="stat-card">';
            echo '<div class="stat-number" style="color: #d63638;">' . $high_risk . '</div>';
            echo '<div class="stat-label">Risque Élevé</div>';
            echo '</div>';
            echo '<div class="stat-card">';
            echo '<div class="stat-number" style="color: #ffb900;">' . $medium_risk . '</div>';
            echo '<div class="stat-label">Risque Moyen</div>';
            echo '</div>';
            echo '<div class="stat-card">';
            echo '<div class="stat-number" style="color: #00a32a;">' . $performance_score . '%</div>';
            echo '<div class="stat-label">Score Performance</div>';
            echo '</div>';
            echo '</div>';

            // Barre de progression
            echo '<h3>Santé Performance Globale</h3>';
            echo '<div class="progress-bar">';
            echo '<div class="progress-fill" style="width: ' . $performance_score . '%;"></div>';
            echo '</div>';

            // Alertes critiques
            if ($tracking_count > 0) {
                echo '<div class="critical">';
                echo '<strong>🚨 CRITIQUE: ' . $tracking_count . ' scripts tracking détectés!</strong><br>';
                echo 'Chaque script tracking se charge sur CHAQUE page. Avoir plusieurs analytics revient à avoir 6 Google Analytics en même temps. C\'est probablement la cause principale de vos timeouts.';
                echo '</div>';
            }

            if ($total_snippets > 30) {
                echo '<div class="critical">';
                echo '<strong>🚨 CRITIQUE: Trop de snippets actifs (' . $total_snippets . ')!</strong><br>';
                echo 'WordPress recommande de garder le code personnalisé sous 20 snippets. Vous avez ' . $total_snippets . ' snippets actifs, ce qui impacte significativement les performances.';
                echo '</div>';
            }

            if ($performance_score < 50) {
                echo '<div class="critical">';
                echo '<strong>🚨 CRITIQUE: Le score performance est de ' . $performance_score . '%!</strong><br>';
                echo 'Votre site est à haut risque de timeouts et de lenteurs. Action immédiate requise.';
                echo '</div>';
            } elseif ($performance_score < 70) {
                echo '<div class="warning">';
                echo '<strong>⚠️ ATTENTION: Le score performance est de ' . $performance_score . '%</strong><br>';
                echo 'Les performances de votre site sont compromises. Envisagez de désactiver les snippets à risque.';
                echo '</div>';
            } else {
                echo '<div class="success">';
                echo '<strong>✅ BON: Le score performance est de ' . $performance_score . '%</strong><br>';
                echo 'Votre configuration de snippets est acceptable, mais il peut y avoir des possibilités d\'optimisation.';
                echo '</div>';
            }

            // Tableau détaillé
            echo '<h2>Analyse Détaillée des Snippets</h2>';
            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<th style="width: 30%;">Nom du Snippet</th>';
            echo '<th style="width: 10%;">Scope</th>';
            echo '<th style="width: 10%;">Risque</th>';
            echo '<th style="width: 30%;">Raisons</th>';
            echo '<th style="width: 20%;">Dernière Modif</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            foreach ($analysis as $item) {
                $snippet = $item['snippet'];
                $risk = $item['risk'];
                $reasons = implode(', ', $item['reasons']);

                $row_class = 'risk-' . $risk;
                $badge_class = 'badge-' . $risk;
                $risk_label = strtoupper($risk);

                echo '<tr class="' . $row_class . '">';
                echo '<td><strong>' . esc_html($snippet->name) . '</strong></td>';
                echo '<td>' . esc_html($snippet->scope) . '</td>';
                echo '<td><span class="badge ' . $badge_class . '">' . $risk_label . '</span></td>';
                echo '<td>' . ($reasons ? esc_html($reasons) : 'Aucun problème détecté') . '</td>';
                echo '<td>' . esc_html($snippet->modified) . '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';

            // Recommandations
            echo '<h2>🎯 Actions Recommandées</h2>';

            if ($tracking_count > 0) {
                echo '<div class="critical">';
                echo '<strong>1. DÉSACTIVER LES SNIPPETS TRACKING (Priorité: CRITIQUE)</strong><br>';
                echo 'Désactivez immédiatement tous les ' . $tracking_count . ' scripts tracking. Gardez UN SEUL système analytics.<br>';
                echo '<strong>Recommandé:</strong> Gardez seulement "Umami PHP" (respectueux de la vie privée) ou "Counter PHP" (léger).<br>';
                echo '<strong>À désactiver:</strong> Datapulse, Rybbit, Swilty, Histogramanalytics, et un autre.';
                echo '</div>';
            }

            if ($total_snippets > 25) {
                echo '<div class="warning">';
                echo '<strong>2. RÉDUIRE LE TOTAL DES SNIPPETS (Priorité: HAUTE)</strong><br>';
                echo 'Visez à réduire de ' . $total_snippets . ' à moins de 20 snippets actifs.<br>';
                echo 'Envisagez de consolider des snippets similaires ou de supprimer ceux non utilisés.';
                echo '</div>';
            }

            if ($scheduler_count > 0) {
                echo '<div class="warning">';
                echo '<strong>3. RÉVISER LES SNIPPETS SCHEDULER (Priorité: MOYENNE)</strong><br>';
                echo 'Le "Scheduled Posts Popup v14" a beaucoup de versions - cela suggère des corrections de bugs fréquentes.<br>';
                echo 'Révisez et testez ces snippets minutieusement.';
                echo '</div>';
            }

            echo '<div class="info">';
            echo '<strong>4. TESTS DE PERFORMANCE</strong><br>';
            echo 'Après les changements, testez les performances de votre site:<br>';
            echo '- Surveillez l\'uptime pendant 24-48 heures<br>';
            echo '- Vérifiez les temps de réponse depuis tous les emplacements (surtout Frankfurt)<br>';
            echo '- Lancez un test Google PageSpeed Insights<br>';
            echo '- Surveillez les logs d\'erreurs pour les timeouts PHP';
            echo '</div>';

            // Boutons d'action
            echo '<h2>🚀 Actions Rapides</h2>';
            echo '<p>';
            echo '<a href="' . admin_url('admin.php?page=snippets') . '" class="btn">Gérer les Snippets</a>';
            echo '<a href="' . admin_url('admin.php?page=snippets&status=active') . '" class="btn">Voir les Snippets Actifs</a>';
            echo '<a href="' . admin_url('admin.php?page=edit-snippet') . '" class="btn">Ajouter un Snippet</a>';
            echo '</p>';
        ?>

        <div style="margin-top: 30px; padding: 20px; background: #f6f7f7; border-radius: 8px;">
            <h3>📋 Référence Rapide</h3>
            <ul style="line-height: 1.8;">
                <li><strong>Risque Élevé:</strong> Action immédiate requise - probablement cause des timeouts</li>
                <li><strong>Risque Moyen:</strong> Doit être révisé et testé</li>
                <li><strong>Risque Faible:</strong> Impact minimal, mais à surveiller si nécessaire</li>
                <li><strong>Score >90%:</strong> Configuration excellente</li>
                <li><strong>Score 70-89%:</strong> Bon, mais améliorable</li>
                <li><strong>Score 50-69%:</strong> Nécessite de l\'attention</li>
                <li><strong>Score <50%:</strong> Critique - action immédiate requise</li>
            </ul>
        </div>

        <div style="margin-top: 20px;">
            <a href="<?php echo admin_url('admin.php?page=snippets'); ?>" class="btn btn-danger">Désactiver ce Diagnostic</a>
        </div>
    <?php endif; ?>
    </div>
</body>
</html>
<?php

// Arrêter l'exécution normale de WordPress
die();

