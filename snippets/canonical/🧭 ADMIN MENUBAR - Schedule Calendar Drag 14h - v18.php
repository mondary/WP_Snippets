<?php
/*
 * Display name: ADMIN - Schedule Calendar [DRAG+14h] + Reallocation (MIN v18)
 * Scope: global
 */

// Assurez-vous que le script ne peut être exécuté que dans WordPress
if (!defined('ABSPATH')) exit;

// Ajout des scripts nécessaires
function add_calendar_scripts() {
    wp_enqueue_script('jquery-ui-draggable');
    wp_enqueue_script('jquery-ui-droppable');
}
add_action('admin_enqueue_scripts', 'add_calendar_scripts');

// Ajout du style CSS pour le calendrier et la liste d'articles
function scheduled_posts_calendar_styles_alpha() {
    $screen = get_current_screen();
    ?>
    <style>
        .calendar-container {
            max-width: 100%;
            margin: 20px 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
        }
        .calendar-header {
            position: sticky;
            top: 32px;
            z-index: 100;
            display: grid;
            grid-template-columns: minmax(200px, 1fr) auto minmax(150px, auto) auto;
            gap: 12px;
            align-items: center;
            background: #fff;
            padding: 12px;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .calendar-nav {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .calendar-nav select {
            padding: 4px 24px 4px 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background: #fff url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="%23999"><path d="M6 9L1 4h10z"/></svg>') no-repeat right 8px center;
        }
        .calendar-nav button {
            padding: 4px 8px;
            border: 1px solid #ddd;
            background: #fff;
            color: #666;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .calendar-nav button .dashicons {
            font-size: 16px;
            width: 16px;
            height: 16px;
        }
        .calendar-nav button:hover {
            background: #f0f0f1;
            border-color: #999;
        }
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
            background: #f0f0f1;
            padding: 15px;
            border-radius: 8px;
        }
        .calendar-day-header {
            text-align: center;
            font-weight: bold;
            padding: 10px;
            background: #2271b1;
            color: white;
            border-radius: 4px;
        }
        .calendar-day {
            min-height: 120px;
            background: white;
            padding: 10px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .calendar-day.empty {
            background: #f8f9fa;
            opacity: 0.7;
            color: #999;
        }
        .calendar-day.empty .date {
            color: #999;
        }
        .calendar-day .date {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .post-item {
            font-size: 12px;
            margin: 5px 0;
            padding: 8px;
            border-radius: 6px;
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .post-item.publish,
        .status-publish {
            background: #d4edda !important; /* Vert clair pour les articles publiés */
        }
        .post-item.draft,
        .status-draft {
            background: #ffe5d9 !important; /* Orange clair pour les brouillons */
        }
        .post-item.pending,
        .status-pending {
            background: #ffeeba !important; /* Jaune pour les articles en attente */
        }
        .post-item.future,
        .status-future {
            background: #cce5ff !important; /* Bleu clair pour les articles planifiés */
        }
        .post-featured-image {
            width: 100%;
            height: 40px;
            overflow: hidden;
            border-radius: 4px;
            margin-bottom: 4px;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .post-featured-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        /* Highlight articles sans featured image */
        .post-item[data-featured-image="0"] {
            border-left: 3px solid #dc3545;
        }
        .post-item[data-featured-image="0"] .post-title::before {
            content: '🖼️';
            margin-right: 4px;
            opacity: 0.5;
        }
        .today {
            border: 2px solid #2271b1;
        }
        .monthly-stats {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .monthly-stats ul {
            list-style: none;
            padding: 0;
        }
        .monthly-stats li {
            font-size: 14px;
            margin: 5px 0;
        }
        .monthly-stats li span {
            font-weight: bold;
        }

        /* Styles spécifiques pour la liste d'articles */
        .wp-list-table tr.status-publish {
            background: #d4edda !important;
        }
        .wp-list-table tr.status-draft {
            background: #ffe5d9 !important;
        }
        .wp-list-table tr.status-pending {
            background: #ffeeba !important;
        }
        .wp-list-table tr.status-future {
            background: #cce5ff !important;
        }

        /* Hover states pour la liste d'articles */
        .wp-list-table tr.status-publish:hover {
            background: #c3e6cb !important;
        }
        .wp-list-table tr.status-draft:hover {
            background: #ffd5c2 !important;
        }
        .wp-list-table tr.status-pending:hover {
            background: #ffe7a0 !important;
        }
        .wp-list-table tr.status-future:hover {
            background: #b8daff !important;
        }

        /* S'assurer que le texte reste lisible */
        .wp-list-table tr td {
            color: #000 !important;
        }

        /* Styles pour la barre de recherche */
        .calendar-search {
            position: relative;
            margin: 0;
            padding: 0;
        }
        .calendar-search input {
            width: 100%;
            padding: 6px 8px 6px 30px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
        }
        .calendar-search:before {
            content: '\f179';
            font-family: dashicons;
            position: absolute;
            left: 8px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }
        .calendar-nav {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .calendar-nav select {
            padding: 4px 24px 4px 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background: #fff url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="%23999"><path d="M6 9L1 4h10z"/></svg>') no-repeat right 8px center;
        }
        .calendar-nav button {
            padding: 4px 8px;
            border: 1px solid #ddd;
            background: #fff;
            color: #666;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
        }
        .calendar-nav button:hover {
            background: #f0f0f1;
            border-color: #999;
        }
        #categoryFilter {
            padding: 4px 24px 4px 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
            max-width: 200px;
        }

        /* Styles pour le drag & drop */
        .post-item.dragging {
            opacity: 0.5;
            cursor: move;
        }
        .calendar-day.droppable-hover {
            background: #f0f7ff;
        }

        .post-time {
            font-size: 10px;
            color: #666;
            position: absolute;
            top: 2px;
            right: 5px;
        }

        .post-grip {
            cursor: move;
            padding-right: 8px;
            color: #999;
        }

        .post-title {
            flex-grow: 1;
            margin-right: 5px;
            padding-right: 45px;
        }

        .post-actions {
            display: none;
            position: absolute;
            right: 5px;
            bottom: 2px;
        }

        .post-item:hover .post-actions {
            display: block;
        }

        .post-actions a,
        .post-actions button {
            text-decoration: none;
            color: #666;
            margin-left: 8px;
            font-size: 14px;
        }

        .post-actions a:hover,
        .post-actions button:hover {
            color: #2271b1;
        }

        /* Ajout des styles pour le dashicons */
        .post-item .dashicons {
            font-size: 14px;
            width: 14px;
            height: 14px;
            line-height: 14px;
        }

        .post-title {
            font-weight: 500;
            line-height: 1.4;
            padding: 0 4px;
            margin-bottom: 8px;
        }

        .post-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 6px;
            border-top: 1px solid rgba(0,0,0,0.06);
        }

        .post-footer .post-time {
            font-size: 11px;
            color: #666;
            position: static;
            top: auto;
            right: auto;
            margin: 0;
            flex-shrink: 0;
        }

        .post-footer .post-actions,
        .post-item:hover .post-actions {
            display: flex;
            gap: 8px;
        }

        /* V12: Bulk actions buttons */
        .calendar-bulk-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .calendar-bulk-actions .button {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            font-size: 13px;
            white-space: nowrap;
        }
        .calendar-bulk-actions .button:hover {
            background: #f0f0f1;
        }
        .calendar-bulk-actions .dashicons {
            font-size: 16px;
            width: 16px;
            height: 16px;
        }
        .calendar-bulk-actions .button.button-secondary {
            background: #fff;
            border: 1px solid #2271b1;
            color: #2271b1;
        }

        /* V12: Duplicate detection styles */
        .post-item.is-duplicate {
            border-right: 3px solid #ff6b6b;
        }
        .post-item.is-duplicate .post-title::after {
            content: '🔄';
            margin-left: 6px;
            font-size: 11px;
        }
        .post-item.is-duplicate[data-featured-image="0"] {
            border-left: 3px solid #dc3545;
            border-right: 3px solid #ff6b6b;
        }
        .duplicate-group {
            border: 1px dashed #ff6b6b;
            padding: 4px;
            margin: 4px 0;
            border-radius: 4px;
            background: rgba(255, 107, 107, 0.05);
        }
            position: static;
            right: auto;
            bottom: auto;
            margin-left: auto;
            flex-wrap: nowrap;
            align-items: center;
        }

        .post-actions a,
        .post-actions button {
            text-decoration: none;
            color: #666;
            padding: 2px;
            margin-left: 0;
            border-radius: 3px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .post-actions button {
            border: 0;
            background: transparent;
            cursor: pointer;
            margin: 0;
        }

        .post-actions a:hover,
        .post-actions button:hover {
            color: #2271b1;
        }

        .post-actions button:focus-visible {
            outline: 2px solid #2271b1;
            outline-offset: 1px;
        }

        .dashicons-visibility.dashicons {
            position: relative !important;
            top: 0 !important;
            left: 0 !important;
            transform: none !important;
            transition: none !important;
        }

        .quick-edit-dialog {
            border: 0;
            border-radius: 8px;
            width: min(480px, calc(100vw - 32px));
            padding: 0;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25);
        }

        .quick-edit-dialog::backdrop {
            background: rgba(0, 0, 0, 0.45);
        }

        .quick-edit-dialog__inner {
            padding: 18px;
        }

        .quick-edit-dialog h2 {
            margin: 0 0 14px;
            font-size: 18px;
            line-height: 1.2;
        }

        .quick-edit-field {
            margin: 0 0 12px;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .quick-edit-field label {
            font-weight: 600;
        }

        .quick-edit-field input {
            width: 100%;
        }

        .quick-edit-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .quick-edit-status {
            min-height: 18px;
            font-size: 12px;
            margin: 2px 0 10px;
            color: #1d2327;
        }

        .quick-edit-status.is-error {
            color: #b32d2e;
        }

        .quick-edit-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        /* Style pour les statistiques */
        .calendar-stats {
            display: flex;
            gap: 16px;
            font-size: 11px;
            color: #666;
            margin-left: auto;
            padding-left: 16px;
            border-left: 1px solid #ddd;
        }

        .calendar-stats span {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .calendar-stats .count {
            font-weight: 600;
            color: #2271b1;
        }

        .calendar-view-actions {
            display: flex;
            gap: 8px;
            margin-left: 4px;
        }

        .calendar-view-actions button {
            white-space: nowrap;
        }

        .calendar-months-container {
            display: flex;
            flex-direction: column;
            gap: 18px;
            margin-top: 14px;
            position: relative;
            transition: opacity .16s ease;
        }

        .calendar-months-container.is-loading {
            opacity: 0.6;
        }

        .calendar-months-container.is-loading::before {
            content: '';
            position: absolute;
            inset: 0;
            z-index: 29;
            background: rgba(24, 28, 33, 0.34);
            border-radius: 10px;
            backdrop-filter: blur(1.5px);
            -webkit-backdrop-filter: blur(1.5px);
        }

        .calendar-months-container.is-loading::after {
            content: 'Mise a jour du calendrier...';
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            z-index: 30;
            background: #111;
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 999px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.35);
            padding: 12px 22px;
            font-size: 15px;
            line-height: 1.2;
            font-weight: 700;
            letter-spacing: 0;
            text-align: center;
            pointer-events: none;
            min-width: 280px;
            animation: clmCalendarPulse 0.95s ease-in-out infinite alternate;
        }

        @keyframes clmCalendarPulse {
            from { transform: translate(-50%, -50%) scale(1); }
            to { transform: translate(-50%, -50%) scale(1.025); }
        }

        .calendar-load-error {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            background: #fff3cd;
            color: #664d03;
            border: 1px solid #ffe69c;
            border-radius: 6px;
            padding: 10px 12px;
            font-size: 13px;
        }

        .calendar-load-error .button {
            white-space: nowrap;
            flex-shrink: 0;
        }

        .calendar-month-section {
            background: #fff;
            border: 1px solid #e2e4e7;
            border-radius: 8px;
            padding: 12px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }

        .calendar-month-title {
            font-size: 14px;
            font-weight: 600;
            color: #1d2327;
            margin: 0 0 10px 0;
        }

        .calendar-month-section {
        }

        @media (min-width: 3200px) {
            .calendar-months-container {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 18px;
                align-items: start;
            }

            .calendar-month-section {
                margin: 0;
                min-width: 0;
            }

            .calendar-grid {
                gap: 8px;
                padding: 12px;
            }
        }

        @media (max-width: 1100px) {
            .calendar-header {
                grid-template-columns: 1fr;
                align-items: stretch;
            }

            .calendar-nav {
                flex-wrap: wrap;
            }

            .calendar-nav select,
            .calendar-nav button,
            .calendar-view-actions button,
            #categoryFilter {
                min-height: 36px;
            }

            #categoryFilter {
                max-width: none;
                width: 100%;
            }

            .calendar-view-actions {
                margin-left: 0;
                width: 100%;
                flex-wrap: wrap;
            }

            .calendar-stats {
                margin-left: 0;
                padding-left: 0;
                border-left: 0;
                border-top: 1px solid #ddd;
                padding-top: 10px;
                flex-wrap: wrap;
            }
        }

        @media (max-width: 782px) {
            .calendar-container {
                margin: 12px 0;
            }

            .calendar-header {
                top: 46px;
                padding: 10px;
                gap: 10px;
            }

            .calendar-search input {
                font-size: 16px;
                min-height: 40px;
            }

            .calendar-nav {
                gap: 6px;
            }

            .calendar-nav button {
                padding: 6px 10px;
            }

            .calendar-nav select {
                min-width: 120px;
                flex: 1 1 140px;
            }

            .calendar-view-actions button {
                flex: 1 1 140px;
                justify-content: center;
            }

            .calendar-months-container {
                gap: 12px;
                margin-top: 10px;
            }

            .calendar-month-section {
                padding: 8px;
                border-radius: 6px;
            }

            .calendar-month-title {
                font-size: 13px;
                margin-bottom: 8px;
            }

            .calendar-grid {
                min-width: 760px;
                gap: 6px;
                padding: 8px;
            }

            .calendar-day-header {
                padding: 8px 6px;
                font-size: 12px;
            }

            .calendar-day {
                min-height: 110px;
                padding: 6px;
            }

            .calendar-day .date {
                font-size: 12px;
                margin-bottom: 4px;
            }

            .post-item {
                margin: 4px 0;
                padding: 6px;
                gap: 6px;
                font-size: 11px;
            }

            .post-title {
                font-size: 11px;
                line-height: 1.3;
                margin-bottom: 4px;
                padding: 0;
                margin-right: 0;
            }

            .post-footer {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                gap: 6px;
                padding-top: 4px;
            }

            .post-time {
                font-size: 10px;
                position: static;
            }

            .post-actions {
                gap: 4px;
                flex-wrap: nowrap;
                margin-left: auto;
            }

            .post-actions a {
                padding: 4px;
            }

            .calendar-stats {
                gap: 10px;
                font-size: 10px;
            }
        }
    </style>
    <?php
}

// Ajouter les styles à l'administration
add_action('admin_head', 'scheduled_posts_calendar_styles_alpha');

// Fonction pour récupérer la plage d'années des articles
function get_posts_years_range() {
    global $wpdb;
    $result = $wpdb->get_row("
        SELECT 
            MIN(YEAR(post_date)) as min_year,
            MAX(YEAR(post_date)) as max_year
        FROM $wpdb->posts
        WHERE post_type = 'post'
        AND post_status IN ('publish', 'future', 'draft')
    ");
    
    $min_year = $result->min_year ? intval($result->min_year) : date('Y');
    $max_year = $result->max_year ? intval($result->max_year) : date('Y');
    
    // Ajouter une année supplémentaire pour les articles futurs
    $max_year = max($max_year, date('Y') + 1);
    
    return array($min_year, $max_year);
}

// Fonction pour générer le HTML du calendrier
function generate_scheduled_posts_calendar_alpha() {
    ?>
    <div class="wrap">
        <h1>Calendrier</h1>
        <div class="calendar-container" data-jetpack-boost="ignore">
            <div class="calendar-header">
                <div class="calendar-search">
                    <input type="text" id="searchPosts" placeholder="Rechercher des articles...">
                </div>
                <div class="calendar-nav">
                    <button id="prevMonth"><span class="dashicons dashicons-arrow-left-alt2"></span></button>
                    <select id="monthSelect">
                        <?php
                        $months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 
                                  'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                        foreach ($months as $index => $month) {
                            echo '<option value="' . $index . '">' . $month . '</option>';
                        }
                        ?>
                    </select>
                    <select id="yearSelect">
                        <?php
                        list($min_year, $max_year) = get_posts_years_range();
                        for ($year = $min_year; $year <= $max_year; $year++) {
                            $selected = $year == date('Y') ? ' selected' : '';
                            echo '<option value="' . $year . '"' . $selected . '>' . $year . '</option>';
                        }
                        ?>
                    </select>
                    <button id="nextMonth"><span class="dashicons dashicons-arrow-right-alt2"></span></button>
                    <div class="calendar-view-actions">
                        <button id="appendNextMonth" type="button" title="Ajouter le mois suivant à la vue">+1 mois</button>
                        <button id="showFullYear" type="button" title="Afficher l'année complète">Année complète</button>
                    </div>
                    <div class="calendar-bulk-actions">
                        <button id="reallocateDrafts" type="button" title="Réallouer les brouillons passés vers des créneaux futurs" class="button button-secondary">
                            <span class="dashicons dashicons-randomize"></span> Réallouer brouillons
                        </button>
                        <div id="reallocateStatus" aria-live="polite" style="margin-top:6px;font-size:12px;color:#50575e;"></div>
                    </div>
                </div>
                <select id="categoryFilter">
                    <option value="">Toutes les catégories</option>
                    <?php
                    $categories = get_categories();
                    foreach ($categories as $category) {
                        echo '<option value="' . esc_attr($category->term_id) . '">' . esc_html($category->name) . '</option>';
                    }
                    ?>
                </select>
                <div class="calendar-stats">
                    <span title="Total des articles de l'année"><i class="dashicons dashicons-calendar-alt"></i> <span id="totalYearPosts" class="count">0</span></span>
                    <span title="Articles du mois en cours"><i class="dashicons dashicons-calendar"></i> <span id="totalMonthPosts" class="count">0</span></span>
                    <span title="Moyenne mensuelle"><i class="dashicons dashicons-chart-area"></i> <span id="avgPostsPerMonth" class="count">0</span></span>
                </div>
            </div>
            <div class="calendar-months-container" id="calendarMonthsContainer" data-jetpack-boost="ignore">
                <!-- Le calendrier sera généré ici par JavaScript -->
            </div>
        </div>
    </div>
    <dialog id="quickEditDialog" class="quick-edit-dialog" aria-labelledby="quickEditTitle">
        <form id="quickEditForm" class="quick-edit-dialog__inner">
            <h2 id="quickEditTitle">Édition rapide</h2>
            <input type="hidden" id="quickEditPostId">
            <div class="quick-edit-field">
                <label for="quickEditPostTitle">Nom du post</label>
                <input type="text" id="quickEditPostTitle" required>
            </div>
            <div class="quick-edit-row">
                <div class="quick-edit-field">
                    <label for="quickEditPostDate">Date</label>
                    <input type="date" id="quickEditPostDate" required>
                </div>
                <div class="quick-edit-field">
                    <label for="quickEditPostTime">Heure</label>
                    <input type="time" id="quickEditPostTime" step="60" required>
                </div>
            </div>
            <p id="quickEditStatus" class="quick-edit-status" role="status" aria-live="polite"></p>
            <div class="quick-edit-actions">
                <button type="button" class="button" id="quickEditCancel">Annuler</button>
                <button type="submit" class="button button-primary" id="quickEditSave">Enregistrer</button>
            </div>
        </form>
    </dialog>

    <script data-jetpack-boost="ignore">
    document.addEventListener('DOMContentLoaded', function() {
        let currentDate = new Date();
        let currentViewMode = 'single';
        let visibleMonths = [new Date(currentDate.getFullYear(), currentDate.getMonth(), 1)];

        // Initialisation des sélecteurs
        const monthSelect = document.getElementById('monthSelect');
        const yearSelect = document.getElementById('yearSelect');
        const categoryFilter = document.getElementById('categoryFilter');
        const searchInput = document.getElementById('searchPosts');
        const calendarMonthsContainer = document.getElementById('calendarMonthsContainer');
        const monthLabels = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        const urlParams = new URLSearchParams(window.location.search);
        const initialView = urlParams.get('view');
        const initialYearParam = parseInt(urlParams.get('year'), 10);
        let pendingCenterToday = true;
        let pendingFocusDate = null;
        let refreshRequestToken = 0;

        // V16: Fonction utilitaire pour formater les dates au format WordPress REST API
        const formatWPDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            const hours = String(date.getHours()).padStart(2, '0');
            const minutes = String(date.getMinutes()).padStart(2, '0');
            const seconds = String(date.getSeconds()).padStart(2, '0');
            return `${year}-${month}-${day}T${hours}:${minutes}:${seconds}`;
        };
        const AUTO_SCHEDULE_MIN_HOUR = 10;
        const AUTO_SCHEDULE_MAX_HOUR = 15;
        const AUTO_SCHEDULE_ANCHOR_HOUR = 14;
        const AUTO_SCHEDULE_STATUSES = 'publish,future,draft,pending';
        let lastQuickEditTrigger = null;
        const quickEditDialog = document.getElementById('quickEditDialog');
        const quickEditForm = document.getElementById('quickEditForm');
        const quickEditPostId = document.getElementById('quickEditPostId');
        const quickEditPostTitle = document.getElementById('quickEditPostTitle');
        const quickEditPostDate = document.getElementById('quickEditPostDate');
        const quickEditPostTime = document.getElementById('quickEditPostTime');
        const quickEditStatus = document.getElementById('quickEditStatus');
        const quickEditCancel = document.getElementById('quickEditCancel');
        const quickEditSave = document.getElementById('quickEditSave');

        if (!Number.isNaN(initialYearParam)) {
            currentDate = new Date(initialYearParam, currentDate.getMonth(), 1);
        }
        
        // Mise à jour initiale des sélecteurs
        monthSelect.value = currentDate.getMonth();
        yearSelect.value = currentDate.getFullYear();

        function normalizeMonth(date) {
            return new Date(date.getFullYear(), date.getMonth(), 1);
        }

        function getMonthKey(date) {
            return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
        }

        function formatDateInputValue(dateObj) {
            return `${dateObj.getFullYear()}-${String(dateObj.getMonth() + 1).padStart(2, '0')}-${String(dateObj.getDate()).padStart(2, '0')}`;
        }

        function formatTimeInputValue(dateObj) {
            return `${String(dateObj.getHours()).padStart(2, '0')}:${String(dateObj.getMinutes()).padStart(2, '0')}`;
        }

        function setQuickEditStatus(message, isError = false) {
            quickEditStatus.textContent = message || '';
            quickEditStatus.classList.toggle('is-error', Boolean(message) && isError);
        }

        function closeQuickEditDialog() {
            if (quickEditDialog.open) {
                quickEditDialog.close();
            }
        }

        function openQuickEditDialog(postItem, triggerButton) {
            if (!quickEditDialog || typeof quickEditDialog.showModal !== 'function') {
                return;
            }

            const postId = postItem.getAttribute('data-post-id') || '';
            const rawTitle = postItem.getAttribute('data-post-title') || '';
            const rawDate = postItem.getAttribute('data-post-date') || '';
            const parsedDate = rawDate ? new Date(rawDate) : new Date();
            const safeDate = Number.isNaN(parsedDate.getTime()) ? new Date() : parsedDate;

            lastQuickEditTrigger = triggerButton || null;
            quickEditPostId.value = postId;
            quickEditPostTitle.value = rawTitle;
            quickEditPostDate.value = formatDateInputValue(safeDate);
            quickEditPostTime.value = formatTimeInputValue(safeDate);
            setQuickEditStatus('');

            if (!quickEditDialog.open) {
                quickEditDialog.showModal();
            }

            window.requestAnimationFrame(() => {
                quickEditPostTitle.focus();
                quickEditPostTitle.select();
            });
        }

        function formatPostHourLabel(hour) {
            return `${String(hour).padStart(2, '0')}:00:00`;
        }

        function fetchPostsForDay(dayDate, excludedPostId = null) {
            const after = `${dayDate}T00:00:00`;
            const before = `${dayDate}T23:59:59`;
            const endpoint = `<?php echo esc_url(rest_url('wp/v2/posts')); ?>?per_page=100&status=${AUTO_SCHEDULE_STATUSES}&after=${encodeURIComponent(after)}&before=${encodeURIComponent(before)}&orderby=date&order=asc&_embed`;

            return fetch(endpoint, {
                headers: {
                    'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Impossible de recuperer les posts du jour pour reequilibrage');
                }
                return response.json();
            })
            .then(posts => {
                if (!excludedPostId) {
                    return posts;
                }
                const excludedIdString = String(excludedPostId);
                return posts.filter(post => String(post.id) !== excludedIdString);
            });
        }

        function buildDaySchedulePlan(posts, dayDate) {
            const sortedPosts = [...posts].sort((a, b) => {
                const dateA = new Date(a.date).getTime();
                const dateB = new Date(b.date).getTime();

                if (dateA !== dateB) {
                    return dateA - dateB;
                }

                return (a.id || 0) - (b.id || 0);
            });

            if (!sortedPosts.length) {
                return [];
            }

            const startHour = Math.max(
                AUTO_SCHEDULE_MIN_HOUR,
                Math.min(AUTO_SCHEDULE_MAX_HOUR, AUTO_SCHEDULE_ANCHOR_HOUR - (sortedPosts.length - 1))
            );

            return sortedPosts.map((post, index) => {
                const hour = startHour + index;
                const payloadDate = `${dayDate}T${formatPostHourLabel(hour)}`;
                const currentPostDate = typeof post.date === 'string' ? post.date.slice(0, 19) : '';
                return {
                    id: post.id,
                    payloadDate: payloadDate,
                    needsUpdate: currentPostDate !== payloadDate
                };
            }).filter(item => item.needsUpdate);
        }

        function applyDaySchedulePlan(plan) {
            if (!plan.length) {
                return Promise.resolve();
            }

            return Promise.all(plan.map(item =>
                fetch(`<?php echo esc_url(rest_url('wp/v2/posts/')); ?>${item.id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                    },
                    body: JSON.stringify({
                        date: item.payloadDate
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Mise a jour horaire impossible pour le post ${item.id}`);
                    }
                    return response.json();
                })
            ));
        }

        function rebalanceDaySchedule(dayDate, excludedPostId = null) {
            if (!dayDate || typeof dayDate !== 'string') {
                return Promise.resolve();
            }

            return fetchPostsForDay(dayDate, excludedPostId)
                .then(posts => {
                    const plan = buildDaySchedulePlan(posts, dayDate);
                    return applyDaySchedulePlan(plan);
                });
        }

        function applySearchFilter() {
            const searchTerm = (searchInput.value || '').toLowerCase();
            const postItems = document.querySelectorAll('.post-item');

            postItems.forEach(item => {
                const title = item.textContent.toLowerCase();
                item.style.display = title.includes(searchTerm) ? '' : 'none';
            });
        }

        function fetchJsonWithRetry(url, retries = 1, delayMs = 250) {
            return fetch(url, {
                headers: {
                    'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status} on ${url}`);
                }
                return response.json();
            })
            .catch(error => {
                if (retries <= 0) {
                    throw error;
                }
                return new Promise(resolve => setTimeout(resolve, delayMs))
                    .then(() => fetchJsonWithRetry(url, retries - 1, delayMs));
            });
        }

        function fetchPostsPageWithRetry(url, retries = 1, delayMs = 250) {
            return fetch(url, {
                headers: {
                    'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status} on ${url}`);
                }

                const totalPages = parseInt(response.headers.get('X-WP-TotalPages') || '1', 10) || 1;
                return response.json().then(data => ({
                    items: Array.isArray(data) ? data : [],
                    totalPages: totalPages
                }));
            })
            .catch(error => {
                if (retries <= 0) {
                    throw error;
                }
                return new Promise(resolve => setTimeout(resolve, delayMs))
                    .then(() => fetchPostsPageWithRetry(url, retries - 1, delayMs));
            });
        }

        function fetchAllPagedPosts(baseUrl) {
            return fetchPostsPageWithRetry(`${baseUrl}&page=1`, 1)
                .then(firstPage => {
                    if (firstPage.totalPages <= 1) {
                        return firstPage.items;
                    }

                    const remainingRequests = [];
                    for (let page = 2; page <= firstPage.totalPages; page++) {
                        remainingRequests.push(
                            fetchPostsPageWithRetry(`${baseUrl}&page=${page}`, 1).then(pageResult => pageResult.items)
                        );
                    }

                    return Promise.all(remainingRequests).then(remainingPages => firstPage.items.concat(...remainingPages));
                });
        }

        function renderCalendarLoadError() {
            const errorBox = document.createElement('div');
            errorBox.className = 'calendar-load-error';

            const text = document.createElement('span');
            text.textContent = 'Le calendrier n’a pas pu se charger. Vérifie la connexion puis réessaie.';

            const retryButton = document.createElement('button');
            retryButton.type = 'button';
            retryButton.className = 'button button-secondary';
            retryButton.textContent = 'Réessayer';
            retryButton.addEventListener('click', function() {
                refreshCurrentView();
            }, { once: true });

            errorBox.appendChild(text);
            errorBox.appendChild(retryButton);

            if (typeof calendarMonthsContainer.replaceChildren === 'function') {
                calendarMonthsContainer.replaceChildren(errorBox);
            } else {
                calendarMonthsContainer.innerHTML = '';
                calendarMonthsContainer.appendChild(errorBox);
            }
        }

        function scrollCalendarCellIntoView(targetCell) {
            window.requestAnimationFrame(() => {
                const monthSection = targetCell.closest('.calendar-month-section');

                if (monthSection && monthSection.scrollWidth > monthSection.clientWidth) {
                    const targetLeft = targetCell.offsetLeft - ((monthSection.clientWidth - targetCell.offsetWidth) / 2);
                    monthSection.scrollLeft = Math.max(0, targetLeft);
                }

                const rect = targetCell.getBoundingClientRect();
                const targetTop = window.scrollY + rect.top - ((window.innerHeight - rect.height) / 2);
                window.scrollTo({
                    top: Math.max(0, targetTop),
                    behavior: 'auto'
                });
            });
        }

        function focusCalendarCellIfNeeded() {
            if (pendingFocusDate) {
                const targetDate = pendingFocusDate;
                pendingFocusDate = null;

                const exactCell = calendarMonthsContainer.querySelector(`.calendar-day[data-date="${targetDate}"]:not(.empty)`)
                    || calendarMonthsContainer.querySelector(`.calendar-day[data-date="${targetDate}"]`);

                if (exactCell) {
                    pendingCenterToday = false;
                    scrollCalendarCellIntoView(exactCell);
                    return;
                }
            }

            if (!pendingCenterToday) {
                return;
            }

            const todayCell = calendarMonthsContainer.querySelector('.calendar-day.today');
            pendingCenterToday = false;

            if (!todayCell) {
                return;
            }

            scrollCalendarCellIntoView(todayCell);
        }

        function fetchMonthPosts(date) {
            const firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
            const lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);

            const after = firstDay.toISOString();
            const before = new Date(lastDay.getFullYear(), lastDay.getMonth(), lastDay.getDate(), 23, 59, 59).toISOString();
            const monthlyPublishedUrl = `<?php echo esc_url(rest_url('wp/v2/posts')); ?>?per_page=100&status=publish,future&after=${after}&before=${before}&orderby=date&order=asc&_embed`;
            const monthlyDraftsUrl = `<?php echo esc_url(rest_url('wp/v2/posts')); ?>?per_page=100&status=draft&after=${after}&before=${before}&orderby=date&order=asc&_embed`;

            return Promise.all([
                fetchAllPagedPosts(monthlyPublishedUrl),
                fetchAllPagedPosts(monthlyDraftsUrl)
            ])
            .then(([monthlyPublished, monthlyDrafts]) => {
                const monthlyPosts = [...monthlyPublished, ...monthlyDrafts];
                const selectedCategory = categoryFilter.value;
                const filteredPosts = selectedCategory
                    ? monthlyPosts.filter(post => post.categories.includes(parseInt(selectedCategory, 10)))
                    : monthlyPosts;

                return { firstDay, lastDay, posts: filteredPosts };
            });
        }

        function fetchYearStats(date) {
            const yearStart = new Date(date.getFullYear(), 0, 1).toISOString();
            const yearEnd = new Date(date.getFullYear(), 11, 31, 23, 59, 59).toISOString();

            return fetch(`<?php echo esc_url(rest_url('wp/v2/posts')); ?>?per_page=100&status=publish&after=${yearStart}&before=${yearEnd}&orderby=date&order=desc&_embed`, {
                headers: {
                    'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return { posts: [], total: 0 };
                }
                const total = response.headers.get('X-WP-Total');
                return response.json().then(posts => ({ posts, total }));
            })
            .then(yearlyPosts => {
                const currentMonth = currentDate.getMonth() + 1;
                const yearlyTotal = parseInt(yearlyPosts.total, 10) || yearlyPosts.posts.length;
                const avgPostsPerMonth = currentMonth > 0 ? (yearlyTotal / currentMonth).toFixed(2) : 0;
                return { yearlyTotal, avgPostsPerMonth };
            });
        }

        function renderMonthSection(parentNode, firstDay, lastDay, posts) {
            const section = document.createElement('section');
            section.className = 'calendar-month-section';

            const title = document.createElement('h2');
            title.className = 'calendar-month-title';
            title.textContent = `${monthLabels[firstDay.getMonth()]} ${firstDay.getFullYear()}`;

            const grid = document.createElement('div');
            grid.className = 'calendar-grid';

            section.appendChild(title);
            section.appendChild(grid);
            parentNode.appendChild(section);

            generateCalendarGrid(grid, firstDay, lastDay, posts);
        }

        function refreshCurrentView() {
            const requestToken = ++refreshRequestToken;
            const monthsToRender = (visibleMonths.length ? visibleMonths : [normalizeMonth(currentDate)])
                .map(normalizeMonth)
                .sort((a, b) => a - b);

            visibleMonths = monthsToRender;
            const previousHeight = calendarMonthsContainer.offsetHeight;
            if (previousHeight > 0) {
                calendarMonthsContainer.style.minHeight = `${previousHeight}px`;
            }
            calendarMonthsContainer.classList.add('is-loading');

            Promise.all([
                Promise.all(monthsToRender.map(fetchMonthPosts)),
                fetchYearStats(currentDate).catch(() => null)
            ])
            .then(([monthResults, yearStats]) => {
                if (requestToken !== refreshRequestToken) {
                    return;
                }

                const nextContent = document.createDocumentFragment();
                monthResults.forEach(({ firstDay, lastDay, posts }) => {
                    renderMonthSection(nextContent, firstDay, lastDay, posts);
                });
                if (typeof calendarMonthsContainer.replaceChildren === 'function') {
                    calendarMonthsContainer.replaceChildren(nextContent);
                } else {
                    calendarMonthsContainer.innerHTML = '';
                    calendarMonthsContainer.appendChild(nextContent);
                }

                const selectedMonthResult = monthResults.find(({ firstDay }) => getMonthKey(firstDay) === getMonthKey(currentDate));
                const selectedMonthCount = selectedMonthResult ? selectedMonthResult.posts.length : 0;
                if (yearStats) {
                    updateMonthlyStats(yearStats.yearlyTotal, selectedMonthCount, yearStats.avgPostsPerMonth);
                } else {
                    updateMonthlyStats(document.getElementById('totalYearPosts').textContent || 0, selectedMonthCount, document.getElementById('avgPostsPerMonth').textContent || 0);
                }
                applySearchFilter();
                focusCalendarCellIfNeeded();
            })
            .catch(error => {
                if (requestToken !== refreshRequestToken) {
                    return;
                }
                console.error('Erreur lors de la récupération des articles:', error);
                const hasCalendarContent = Boolean(calendarMonthsContainer.querySelector('.calendar-month-section'));
                if (!hasCalendarContent) {
                    renderCalendarLoadError();
                }
            })
            .finally(() => {
                if (requestToken !== refreshRequestToken) {
                    return;
                }
                calendarMonthsContainer.classList.remove('is-loading');
                calendarMonthsContainer.style.minHeight = '';
            });
        }

        function updateCalendar(date) {
            // Preserve existing visible months if in dual mode
            if (visibleMonths.length <= 1) {
                visibleMonths = [normalizeMonth(date)];
                currentViewMode = 'single';
            } else {
                // Just update the date, keep the dual view
                currentDate = date;
            }
            pendingCenterToday = false;
            refreshCurrentView();
        }

        function appendNextMonthToView() {
            const lastVisible = visibleMonths.length ? visibleMonths[visibleMonths.length - 1] : normalizeMonth(currentDate);
            const nextMonth = new Date(lastVisible.getFullYear(), lastVisible.getMonth() + 1, 1);

            if (!visibleMonths.some(monthDate => getMonthKey(monthDate) === getMonthKey(nextMonth))) {
                visibleMonths = [...visibleMonths, nextMonth];
            }

            currentViewMode = visibleMonths.length > 1 ? 'stacked' : 'single';
            pendingCenterToday = false;
            refreshCurrentView();
        }

        function showFullYearView(year) {
            currentViewMode = 'year';
            currentDate = new Date(year, currentDate.getMonth(), 1);
            monthSelect.value = currentDate.getMonth();
            yearSelect.value = currentDate.getFullYear();
            visibleMonths = Array.from({ length: 12 }, (_, monthIndex) => new Date(year, monthIndex, 1));
            pendingCenterToday = (year === new Date().getFullYear());
            refreshCurrentView();
        }

        function ensureTargetMonthVisible(targetMonthDate) {
            if (currentViewMode !== 'stacked') {
                return;
            }

            if (!visibleMonths.some(monthDate => getMonthKey(monthDate) === getMonthKey(targetMonthDate))) {
                visibleMonths = [...visibleMonths, targetMonthDate].sort((a, b) => a - b);
            }
        }

        function generateCalendarGrid(grid, firstDay, lastDay, posts) {
            grid.innerHTML = '';

            function formatDateLocal(dateObj) {
                const y = dateObj.getFullYear();
                const m = String(dateObj.getMonth() + 1).padStart(2, '0');
                const d = String(dateObj.getDate()).padStart(2, '0');
                return `${y}-${m}-${d}`;
            }

            // Ajout des en-têtes des jours
            const dayNames = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
            dayNames.forEach(day => {
                const dayHeader = document.createElement('div');
                dayHeader.className = 'calendar-day-header';
                dayHeader.textContent = day;
                grid.appendChild(dayHeader);
            });

            // Ajout des cases vides pour le début du mois avec les jours du mois précédent
            const emptyDaysStart = (firstDay.getDay() || 7) - 1;
            const prevMonthLastDay = new Date(firstDay.getFullYear(), firstDay.getMonth(), 0).getDate();
            
            for (let i = 0; i < emptyDaysStart; i++) {
                const emptyDay = document.createElement('div');
                emptyDay.className = 'calendar-day empty';
                const prevDate = new Date(firstDay.getFullYear(), firstDay.getMonth() - 1, prevMonthLastDay - emptyDaysStart + i + 1);
                emptyDay.setAttribute('data-date', formatDateLocal(prevDate));
                emptyDay.setAttribute('data-month-offset', '-1');
                emptyDay.addEventListener('drop', drop);
                emptyDay.addEventListener('dragover', allowDrop);
                const dateDiv = document.createElement('div');
                dateDiv.className = 'date';
                dateDiv.textContent = prevMonthLastDay - emptyDaysStart + i + 1;
                emptyDay.appendChild(dateDiv);
                grid.appendChild(emptyDay);
            }

            // Ajout des jours du mois
            for (let day = 1; day <= lastDay.getDate(); day++) {
                const dayCell = document.createElement('div');
                dayCell.className = 'calendar-day';
                
                const currentDayDate = new Date(firstDay.getFullYear(), firstDay.getMonth(), day);
                dayCell.setAttribute('data-date', formatDateLocal(currentDayDate));
                dayCell.setAttribute('data-month-offset', '0');
                dayCell.addEventListener('drop', drop);
                dayCell.addEventListener('dragover', allowDrop);

                if (currentDayDate.toDateString() === new Date().toDateString()) {
                    dayCell.classList.add('today');
                }

                const dateDiv = document.createElement('div');
                dateDiv.className = 'date';
                dateDiv.textContent = day;
                dayCell.appendChild(dateDiv);

                // Ajout des articles pour ce jour
                const dayPosts = posts.filter(post => {
                    const postDate = new Date(post.date);
                    return postDate.getDate() === day &&
                           postDate.getMonth() === firstDay.getMonth() &&
                           postDate.getFullYear() === firstDay.getFullYear();
                });

                dayPosts.forEach(post => {
                    const postDiv = document.createElement('div');
                    postDiv.className = 'post-item ' + post.status;
                    postDiv.setAttribute('data-post-id', post.id);
                    postDiv.setAttribute('data-post-date', post.date);
                    postDiv.setAttribute('draggable', true);
                    postDiv.addEventListener('dragstart', drag);

                    const postTime = new Date(post.date).toLocaleTimeString('fr-FR', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    const postTitle = post.title.rendered.replace(/&amp;/g, '&')
                        .replace(/&lt;/g, '<')
                        .replace(/&gt;/g, '>')
                        .replace(/&quot;/g, '"')
                        .replace(/&#039;/g, "'");
                    postDiv.setAttribute('data-post-title', postTitle);

                    // Récupérer l'URL featured avec fallback robuste (évite crash si tailles absentes)
                    const media = post && post._embedded && post._embedded['wp:featuredmedia'] && post._embedded['wp:featuredmedia'][0]
                        ? post._embedded['wp:featuredmedia'][0]
                        : null;
                    const featuredImageUrl = media
                        ? ((media.media_details && media.media_details.sizes && media.media_details.sizes.thumbnail && media.media_details.sizes.thumbnail.source_url)
                            || (media.media_details && media.media_details.sizes && media.media_details.sizes.medium && media.media_details.sizes.medium.source_url)
                            || media.source_url
                            || null)
                        : null;
                    const hasFeaturedImage = featuredImageUrl && featuredImageUrl.length > 0;
                    postDiv.setAttribute('data-featured-image', hasFeaturedImage ? '1' : '0');

                    postDiv.innerHTML = `
                        ${hasFeaturedImage ? `<div class="post-featured-image"><img src="${featuredImageUrl}" alt="" loading="lazy" /></div>` : ''}
                        <div class="post-title">${postTitle}</div>
                        <div class="post-footer">
                            <span class="post-time">${postTime}</span>
                            <div class="post-actions">
                                <a href="${post.link}" target="_blank" rel="noopener noreferrer" title="Prévisualiser l'article" aria-label="Prévisualiser l'article dans un nouvel onglet">
                                    <span class="dashicons dashicons-visibility" aria-hidden="true"></span>
                                </a>
                                <a href="<?php echo admin_url('post.php'); ?>?post=${post.id}&action=edit" title="Modifier l'article" aria-label="Modifier l'article">
                                    <span class="dashicons dashicons-edit" aria-hidden="true"></span>
                                </a>
                                <button type="button" class="post-quick-edit" title="Édition rapide" aria-label="Édition rapide: modifier le nom, la date et l'heure">
                                    <span class="dashicons dashicons-admin-tools" aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                    `;

                    dayCell.appendChild(postDiv);
                });

                grid.appendChild(dayCell);
            }

            // Ajout des cases vides pour la fin du mois avec les jours du mois suivant
            const totalDays = emptyDaysStart + lastDay.getDate();
            const emptyDaysEnd = Math.ceil(totalDays / 7) * 7 - totalDays;
            
            for (let i = 1; i <= emptyDaysEnd; i++) {
                const emptyDay = document.createElement('div');
                emptyDay.className = 'calendar-day empty';
                const nextDate = new Date(firstDay.getFullYear(), firstDay.getMonth() + 1, i);
                emptyDay.setAttribute('data-date', formatDateLocal(nextDate));
                emptyDay.setAttribute('data-month-offset', '1');
                emptyDay.addEventListener('drop', drop);
                emptyDay.addEventListener('dragover', allowDrop);
                const dateDiv = document.createElement('div');
                dateDiv.className = 'date';
                dateDiv.textContent = i;
                emptyDay.appendChild(dateDiv);
                grid.appendChild(emptyDay);
            }
        }

        function updateMonthlyStats(yearlyTotal, monthlyCount, avgPerMonth) {
            document.getElementById('totalYearPosts').textContent = yearlyTotal;
            document.getElementById('totalMonthPosts').textContent = monthlyCount;
            document.getElementById('avgPostsPerMonth').textContent = avgPerMonth;
        }

        // Gestionnaires d'événements pour la navigation
        document.getElementById('prevMonth').addEventListener('click', () => {
            currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth() - 1, 1);
            updateSelectorsAndCalendar();
        });

        document.getElementById('nextMonth').addEventListener('click', () => {
            currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 1);
            updateSelectorsAndCalendar();
        });

        document.getElementById('appendNextMonth').addEventListener('click', () => {
            appendNextMonthToView();
        });

        document.getElementById('showFullYear').addEventListener('click', () => {
            const selectedYear = parseInt(yearSelect.value, 10) || currentDate.getFullYear();
            currentDate = new Date(selectedYear, currentDate.getMonth(), 1);
            monthSelect.value = currentDate.getMonth();
            yearSelect.value = currentDate.getFullYear();
            showFullYearView(selectedYear);
        });

        // Gestionnaires d'événements pour les sélecteurs
        monthSelect.addEventListener('change', function() {
            currentDate = new Date(currentDate.getFullYear(), parseInt(this.value, 10), 1);
            updateCalendar(currentDate);
        });
        
        yearSelect.addEventListener('change', function() {
            currentDate = new Date(parseInt(this.value, 10), currentDate.getMonth(), 1);
            updateCalendar(currentDate);
        });

        // Fonction pour mettre à jour les sélecteurs et le calendrier
        function updateSelectorsAndCalendar() {
            monthSelect.value = currentDate.getMonth();
            yearSelect.value = currentDate.getFullYear();
            updateCalendar(currentDate);
        }

        // Filtre par catégorie
        categoryFilter.addEventListener('change', () => {
            refreshCurrentView();
        });

        // Recherche
        searchInput.addEventListener('input', function() {
            applySearchFilter();
        });

        function allowDrop(event) {
            event.preventDefault();
        }

        function drop(event) {
            event.preventDefault();
            const postId = event.dataTransfer.getData("text");
            const sourceDate = event.dataTransfer.getData("source-date");

            const targetElement = event.currentTarget && event.currentTarget.getAttribute('data-date')
                ? event.currentTarget
                : (event.target && event.target.closest ? event.target.closest('[data-date]') : null);

            const newDate = targetElement ? targetElement.getAttribute('data-date') : null;
            const monthOffset = targetElement ? parseInt(targetElement.getAttribute('data-month-offset') || '0', 10) : 0;
            
            if (newDate) {
                updatePostDate(postId, newDate, monthOffset, sourceDate);
            } else {
                console.error('Impossible de trouver une date valide pour le drop');
            }
        }

        calendarMonthsContainer.addEventListener('click', function(event) {
            const quickEditButton = event.target && event.target.closest ? event.target.closest('.post-quick-edit') : null;
            if (!quickEditButton) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();

            const postItem = quickEditButton.closest('.post-item[data-post-id]');
            if (!postItem) {
                return;
            }

            openQuickEditDialog(postItem, quickEditButton);
        });

        function drag(event) {
            const draggable = event.currentTarget && event.currentTarget.getAttribute('data-post-id')
                ? event.currentTarget
                : (event.target && event.target.closest ? event.target.closest('.post-item[data-post-id]') : null);

            if (!draggable) {
                return;
            }
            event.dataTransfer.setData("text", draggable.getAttribute('data-post-id'));
            event.dataTransfer.setData("source-date", draggable.getAttribute('data-post-date') || '');
        }

        function updatePostDate(postId, newDate, monthOffset = 0, sourceDate = '') {
            if (!newDate || typeof newDate !== 'string') {
                console.error('Format de date invalide:', newDate);
                return;
            }

            const parts = newDate.split('-');
            if (parts.length !== 3) {
                console.error('Format de date attendu YYYY-MM-DD:', newDate);
                return;
            }

            const year = parseInt(parts[0], 10);
            const month = parseInt(parts[1], 10);
            const day = parseInt(parts[2], 10);
            const dateWithTime = new Date(year, month - 1, day, AUTO_SCHEDULE_ANCHOR_HOUR, 0, 0);
            const targetMonthDate = new Date(year, month - 1, 1);

            if (isNaN(dateWithTime.getTime())) {
                console.error('Date invalide après conversion:', newDate);
                return;
            }

            const payloadDate = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}T${formatPostHourLabel(AUTO_SCHEDULE_ANCHOR_HOUR)}`;
            const sourceDay = (sourceDate || '').slice(0, 10);

            fetch(`<?php echo esc_url(rest_url('wp/v2/posts/')); ?>${postId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                },
                body: JSON.stringify({ date: payloadDate })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur lors de la mise à jour de la date');
                }
                return response.json();
            })
            .then(() => {
                const rebalances = [rebalanceDaySchedule(newDate)];
                if (sourceDay && sourceDay !== newDate) {
                    rebalances.push(rebalanceDaySchedule(sourceDay, postId));
                }

                return Promise.all(rebalances);
            })
            .then(() => {
                currentDate = targetMonthDate;
                monthSelect.value = currentDate.getMonth();
                yearSelect.value = currentDate.getFullYear();
                pendingFocusDate = newDate;
                pendingCenterToday = false;

                if (currentViewMode === 'single' && monthOffset !== 0) {
                    updateSelectorsAndCalendar();
                    return;
                }

                ensureTargetMonthVisible(targetMonthDate);
                refreshCurrentView();
            })
            .catch(error => {
                console.error('Erreur:', error);
            });
        }

        if (quickEditDialog) {
            quickEditCancel.addEventListener('click', function() {
                closeQuickEditDialog();
            });

            quickEditDialog.addEventListener('cancel', function(event) {
                event.preventDefault();
                closeQuickEditDialog();
            });

            quickEditDialog.addEventListener('close', function() {
                setQuickEditStatus('');
                quickEditForm.reset();
                if (lastQuickEditTrigger && typeof lastQuickEditTrigger.focus === 'function') {
                    lastQuickEditTrigger.focus();
                }
            });

            quickEditForm.addEventListener('submit', function(event) {
                event.preventDefault();

                const postId = (quickEditPostId.value || '').trim();
                const titleValue = (quickEditPostTitle.value || '').trim();
                const dateValue = (quickEditPostDate.value || '').trim();
                const timeValue = (quickEditPostTime.value || '').trim();

                if (!postId || !titleValue || !dateValue || !timeValue) {
                    setQuickEditStatus('Merci de remplir nom, date et heure.', true);
                    return;
                }

                const payloadDate = `${dateValue}T${timeValue}:00`;
                const parts = dateValue.split('-');
                const year = parseInt(parts[0], 10);
                const month = parseInt(parts[1], 10);
                const day = parseInt(parts[2], 10);
                const targetMonthDate = new Date(year, month - 1, 1);

                quickEditSave.disabled = true;
                quickEditForm.setAttribute('aria-busy', 'true');
                setQuickEditStatus('Enregistrement en cours...');

                fetch(`<?php echo esc_url(rest_url('wp/v2/posts/')); ?>${postId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                    },
                    body: JSON.stringify({
                        title: titleValue,
                        date: payloadDate
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur lors de la sauvegarde rapide');
                    }
                    return response.json();
                })
                .then(() => {
                    closeQuickEditDialog();
                    currentDate = targetMonthDate;
                    monthSelect.value = currentDate.getMonth();
                    yearSelect.value = currentDate.getFullYear();
                    pendingFocusDate = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                    pendingCenterToday = false;

                    if (currentViewMode === 'single') {
                        updateSelectorsAndCalendar();
                    } else {
                        ensureTargetMonthVisible(targetMonthDate);
                        refreshCurrentView();
                    }
                })
                .catch(error => {
                    console.error('Erreur quick edit:', error);
                    setQuickEditStatus('Impossible d’enregistrer. Vérifie les droits et le format.', true);
                })
                .finally(() => {
                    quickEditSave.disabled = false;
                    quickEditForm.removeAttribute('aria-busy');
                });
            });
        }

        // Initialisation: mois par défaut, année seulement si demandé explicitement
        if (initialView === 'year') {
            showFullYearView(currentDate.getFullYear());
        } else {
            updateCalendar(currentDate);
        }

        // ======== V17: Réallocation serveur minifiée (règle 10h / 14h) ========
        const reallocateDraftsButton = document.getElementById('reallocateDrafts');
        const reallocateStatus = document.getElementById('reallocateStatus');

        function setReallocateStatus(message, isError = false) {
            if (!reallocateStatus) return;
            reallocateStatus.textContent = message || '';
            reallocateStatus.style.color = isError ? '#b32d2e' : '#50575e';
        }

        if (reallocateDraftsButton) {
            reallocateDraftsButton.addEventListener('click', function() {
                if (!confirm('Réallouer les brouillons vers des créneaux futurs (10h puis 14h) ?')) {
                    return;
                }

                reallocateDraftsButton.disabled = true;
                reallocateDraftsButton.textContent = '⏳ Réallocation...';
                setReallocateStatus('Traitement serveur en cours...');
                fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                    },
                    body: 'action=clm_reallocate_overdue_posts&nonce=<?php echo rawurlencode( wp_create_nonce('clm_reallocate_overdue_posts') ); ?>'
                })
                .then(response => response.json())
                .then(payload => {
                    if (!payload || !payload.success) {
                        const msg = payload && payload.data && payload.data.message ? payload.data.message : 'Erreur inconnue';
                        throw new Error(msg);
                    }
                    return payload.data || {};
                })
                .then(data => {
                    const moved = parseInt(data.moved || 0, 10);
                    const total = parseInt(data.total || 0, 10);
                    const failed = parseInt(data.failed || 0, 10);
                    const failedIds = Array.isArray(data.failed_ids) ? data.failed_ids.map(id => `#${id}`).join(', ') : '';

                    if (total === 0) {
                        const candidates = parseInt(data.candidates || 0, 10);
                        const today = data.today ? `\nRéférence: ${data.today}` : '';
                        setReallocateStatus(`Aucun contenu en retard trouvé. Candidats analysés: ${candidates}.`);
                        alert(`Aucun brouillon à réallouer.\nCandidats analysés: ${candidates}${today}`);
                        return;
                    }

                    setReallocateStatus(`Terminé. Déplacés: ${moved}/${total}. Échecs: ${failed}.`, failed > 0);
                    const details = failed > 0 && failedIds ? `\nIDs en échec: ${failedIds}` : '';
                    alert(`✅ Réallocation terminée !\nDéplacés: ${moved}/${total}\nÉchecs: ${failed}${details}`);
                    refreshCurrentView();
                })
                .catch(error => {
                    console.error('Erreur réallocation:', error);
                    setReallocateStatus(`Erreur: ${error.message}`, true);
                    alert('❌ Erreur lors de la réallocation: ' + error.message);
                })
                .finally(() => {
                    reallocateDraftsButton.disabled = false;
                    reallocateDraftsButton.innerHTML = '<span class="dashicons dashicons-randomize"></span> Réallouer brouillons';
                });
            });
        }

        // ======== V12: Détection des doublons (par premier mot du titre) ========
        function detectDuplicates() {
            // Analyser tous les articles draft/pending/future
            const postItems = document.querySelectorAll('.post-item');
            const postsByFirstWord = {};

            postItems.forEach(item => {
                const status = item.className.match(/(draft|pending|future)/);
                if (!status) return; // Ignorer les articles publiés

                const titleElement = item.querySelector('.post-title');
                if (!titleElement) return;

                const title = titleElement.textContent.trim();
                const words = title.split(/\s+/);
                if (words.length === 0) return;

                const firstWord = words[0].toLowerCase().replace(/[^a-z0-9]/g, '');

                if (!postsByFirstWord[firstWord]) {
                    postsByFirstWord[firstWord] = [];
                }

                postsByFirstWord[firstWord].push({
                    element: item,
                    title: title,
                    id: item.getAttribute('data-post-id')
                });
            });

            // Marquer les doublons
            Object.values(postsByFirstWord).forEach(posts => {
                if (posts.length > 1) {
                    // Plus d'un article avec le même premier mot = doublons
                    posts.forEach(post => {
                        post.element.classList.add('is-duplicate');
                    });

                    // Optionnel: regrouper visuellement sur la même journée
                    groupDuplicatesOnSameDay(posts);
                }
            });
        }

        // Regrouper les doublons sur la même journée
        function groupDuplicatesOnSameDay(posts) {
            if (posts.length < 2) return;

            // Trouver le jour du premier doublon
            const firstPost = posts[0].element;
            const dayCell = firstPost.closest('.calendar-day');

            if (dayCell && !dayCell.querySelector('.duplicate-group')) {
                // Créer un groupe de doublons
                const group = document.createElement('div');
                group.className = 'duplicate-group';

                posts.forEach(post => {
                    const postElement = post.element;
                    const parent = postElement.parentElement;

                    if (parent !== dayCell && !parent.classList.contains('duplicate-group')) {
                        // Déplacer l'élément dans le groupe
                        group.appendChild(postElement);
                    }
                });

                if (group.children.length > 0) {
                    dayCell.appendChild(group);
                }
            }
        }

        // Appliquer la détection des doublons après chaque rafraîchissement
        const originalRenderMonthSection = window.renderMonthSection;
        if (originalRenderMonthSection) {
            window.renderMonthSection = function() {
                const result = originalRenderMonthSection.apply(this, arguments);
                setTimeout(() => detectDuplicates(), 100);
                return result;
            };
        }

        // Appliquer la détection au chargement initial
        setTimeout(() => detectDuplicates(), 500);
    });
    </script>
    <?php
}

// Ajout de la page du calendrier au menu admin
add_action('admin_menu', function() {
    add_submenu_page('edit.php', 'Calendrier', 'Calendrier', 'edit_posts', 'scheduled-posts-calendar', 'generate_scheduled_posts_calendar_alpha', 1);
});

// Lien "Calendrier annuel" dans la ligne de vues de "Tous les articles"
add_filter('views_edit-post', function($views) {
    if (!current_user_can('edit_posts')) {
        return $views;
    }

    $target_year = (int) date('Y');
    if (!empty($_GET['m']) && preg_match('/^(\d{4})/', (string) wp_unslash($_GET['m']), $matches)) {
        $target_year = (int) $matches[1];
    }

    $calendar_year_url = add_query_arg([
        'page' => 'scheduled-posts-calendar',
        'view' => 'year',
        'year' => $target_year,
    ], admin_url('admin.php'));

    $views['calendar_year'] = '<a href="' . esc_url($calendar_year_url) . '">Calendrier annuel</a>';

    return $views;
});

// Ajout de l'entrée dans la barre d'administration
add_action('admin_bar_menu', function($admin_bar) {
    $admin_bar->add_node([
        'id'    => 'calendar',
        'title' => '<span class="ab-icon dashicons dashicons-calendar-alt"></span>',
        'href'  => admin_url('admin.php?page=scheduled-posts-calendar'),
        'meta'  => [
            'title' => 'Voir le calendrier des articles',
        ],
    ]);
}, 100);

// Réallocation serveur: brouillons -> 10h puis 14h sur jours futurs.
function clm_reallocate_overdue_posts_ajax() {
    if ( ! current_user_can( 'edit_posts' ) ) wp_send_json_error( array( 'message' => 'Permissions insuffisantes.' ), 403 );
    check_ajax_referer( 'clm_reallocate_overdue_posts', 'nonce' );

    $tz = wp_timezone();
    $today = new DateTimeImmutable( 'today', $tz );
    $tomorrow = $today->modify( '+1 day' );
    $statuses = array( 'draft' );

    $candidates = get_posts( array(
        'post_type' => 'post', 'post_status' => $statuses, 'posts_per_page' => -1,
        'orderby' => 'date', 'order' => 'ASC', 'fields' => 'ids',
    ) );

    $overdue = array();
    foreach ( $candidates as $id ) {
        $p = get_post( $id );
        if ( ! $p ) continue;
        $ref = ( $p->post_date && '0000-00-00 00:00:00' !== $p->post_date ) ? $p->post_date : $p->post_modified;
        if ( ! $ref || false === strtotime( $ref ) ) continue;
        // Règle métier: tous les brouillons sont réallouables.
        $overdue[] = (int) $id;
    }

    if ( ! $overdue ) wp_send_json_success( array( 'moved' => 0, 'failed' => 0, 'failed_ids' => array(), 'total' => 0, 'candidates' => count( $candidates ), 'today' => $today->format( 'Y-m-d H:i:s' ), 'next_slots' => array() ) );

    $moved = 0; $failed = 0; $failed_ids = array(); $next_slots = array();
    $day = $tomorrow; $hours = array( 10, 14 ); $i = 0;

    foreach ( $overdue as $post_id ) {
        if ( ! current_user_can( 'edit_post', $post_id ) ) { $failed++; $failed_ids[] = (int) $post_id; continue; }
        $slot = $day->setTime( $hours[ $i ], 0, 0 )->format( 'Y-m-d H:i:s' );
        $i++;
        if ( $i >= 2 ) { $i = 0; $day = $day->modify( '+1 day' ); }
        $next_slots[] = array( 'id' => (int) $post_id, 'date' => $slot );
        $r = wp_update_post( array( 'ID' => (int) $post_id, 'post_date' => $slot, 'post_date_gmt' => get_gmt_from_date( $slot ) ), true );
        if ( is_wp_error( $r ) ) { $failed++; $failed_ids[] = (int) $post_id; continue; }
        $moved++;
    }

    wp_send_json_success( array( 'moved' => $moved, 'failed' => $failed, 'failed_ids' => $failed_ids, 'total' => count( $overdue ), 'candidates' => count( $candidates ), 'today' => $today->format( 'Y-m-d H:i:s' ), 'next_slots' => array_slice( $next_slots, 0, 10 ) ) );
}
add_action( 'wp_ajax_clm_reallocate_overdue_posts', 'clm_reallocate_overdue_posts_ajax' );
