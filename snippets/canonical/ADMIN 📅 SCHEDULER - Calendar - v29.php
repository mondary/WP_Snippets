<?php
/*
 * Display name: ADMIN 📅 SCHEDULER - Calendar - v29
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
        .calendar-topbar {
            position: sticky;
            top: 32px;
            z-index: 100;
            background: #fff;
        }
        .calendar-header {
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

        .reallocate-status {
            display: none;
            margin: 8px 0 0;
            padding: 8px 12px;
            font-size: 13px;
            line-height: 1.4;
            color: #1d2327;
            background: #fff;
            border: 1px solid #e2e4e7;
            border-radius: 6px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }
        .reallocate-status.is-visible {
            display: block;
        }
        .reallocate-status.is-error {
            background: #fff3cd;
            border-color: #ffe69c;
            color: #664d03;
        }
        .reallocate-status.is-success {
            background: #edf7ed;
            border-color: #c6e0c6;
            color: #1d6b1d;
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

        .quick-edit-dialog .quick-edit-row {
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

        .reallocate-dialog {
            border: 0;
            border-radius: 8px;
            width: min(420px, calc(100vw - 32px));
            padding: 0;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25);
        }

        .reallocate-dialog::backdrop {
            background: rgba(0, 0, 0, 0.45);
        }

        .reallocate-dialog__inner {
            padding: 18px;
        }

        .reallocate-dialog h2 {
            margin: 0 0 14px;
            font-size: 18px;
            line-height: 1.2;
        }

        .reallocate-dialog p {
            margin: 0 0 14px;
            font-size: 13px;
            color: #50575e;
        }

        .reallocate-options {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 16px;
        }

        .reallocate-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 14px;
        }

        .reallocate-field-label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #50575e;
        }

        .reallocate-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            cursor: pointer;
            transition: border-color 0.15s, background 0.15s;
        }

        .reallocate-option:hover {
            border-color: #2271b1;
            background: #f0f7ff;
        }

        .reallocate-option.selected {
            border-color: #2271b1;
            background: #e8f0fe;
        }

        .reallocate-option input[type="radio"] {
            margin: 0;
        }

        .reallocate-option-label {
            font-weight: 600;
            font-size: 13px;
        }

        .reallocate-option-hours {
            font-size: 12px;
            color: #666;
            margin-left: auto;
        }

        .reallocate-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        .reallocate-result-dialog {
            border: 0;
            border-radius: 8px;
            width: min(560px, calc(100vw - 32px));
            max-width: min(560px, calc(100vw - 32px));
            max-height: min(85vh, 760px);
            padding: 0;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25);
            color: #1d2327;
            overflow: hidden;
        }
        .reallocate-result-dialog[open] {
            display: flex;
            flex-direction: column;
        }
        .reallocate-result-dialog::backdrop {
            background: rgba(0, 0, 0, 0.45);
        }
        .reallocate-result-dialog__inner {
            padding: 18px;
            display: flex;
            flex-direction: column;
            min-height: 0;
            max-height: min(85vh, 760px);
            overflow: hidden;
        }
        .reallocate-result-dialog h2 {
            margin: 0 0 14px;
            font-size: 18px;
            line-height: 1.2;
            flex: 0 0 auto;
        }
        .reallocate-result-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            padding-right: 4px;
            font-size: 13px;
            line-height: 1.5;
        }
        .reallocate-result-section {
            border: 1px solid #e2e4e7;
            border-radius: 6px;
            padding: 10px 12px;
            margin-bottom: 10px;
            background: #fff;
        }
        .reallocate-result-section:last-child {
            margin-bottom: 0;
        }
        .reallocate-result-section__title {
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 700;
            font-size: 13px;
            margin: 0 0 6px;
            color: #1d2327;
        }
        .reallocate-result-section ul {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .reallocate-result-section li {
            padding: 2px 0;
            display: flex;
            justify-content: space-between;
            gap: 12px;
        }
        .reallocate-result-section li .label {
            color: #50575e;
        }
        .reallocate-result-section li .value {
            font-weight: 600;
            color: #1d2327;
        }
        .reallocate-result-section.is-error {
            border-color: #ffd0d0;
            background: #fff5f5;
        }
        .reallocate-result-section.is-success {
            border-color: #c6e0c6;
            background: #edf7ed;
        }
        .reallocate-result-section.is-info {
            border-color: #c6e0f0;
            background: #f0f7ff;
        }
        .reallocate-result-section .meta {
            font-size: 12px;
            color: #50575e;
            margin-top: 6px;
            word-break: break-word;
        }
        .reallocate-result-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 14px;
            flex: 0 0 auto;
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

            .calendar-topbar {
                top: 46px;
            }

            .calendar-header {
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
        <h1>Calendrier <span style="font-size:0.55em;font-weight:600;color:#2271b1;vertical-align:middle;background:#e8f0fe;padding:2px 8px;border-radius:999px;margin-left:6px;">v29</span></h1>
        <div class="calendar-container" data-jetpack-boost="ignore">
          <div class="calendar-topbar">
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
                        <button id="reallocateDrafts" type="button" title="Réallouer brouillons et planifiés dès aujourd'hui (slots partagés publish+future+draft)" class="button button-secondary">
                            <span class="dashicons dashicons-randomize"></span> Réallouer
                        </button>
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
            <div id="reallocateStatus" class="reallocate-status" role="status" aria-live="polite"></div>
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
    <dialog id="reallocateDialog" class="reallocate-dialog" aria-labelledby="reallocateTitle">
        <div class="reallocate-dialog__inner">
            <h2 id="reallocateTitle">Réallouer</h2>
            <div class="reallocate-field">
                <span class="reallocate-field-label">Mode</span>
                <div class="reallocate-options">
                    <label class="reallocate-option">
                        <input type="radio" name="reallocateMode" value="drafts">
                        <span class="reallocate-option-label">Brouillons uniquement</span>
                        <span class="reallocate-option-hours">Planifiés laissés en place</span>
                    </label>
                    <label class="reallocate-option">
                        <input type="radio" name="reallocateMode" value="compact" checked>
                        <span class="reallocate-option-label">Planifiés + brouillons</span>
                        <span class="reallocate-option-hours">Planifiés + brouillons compactés dès aujourd'hui (slots partagés)</span>
                    </label>
                </div>
            </div>
            <div class="reallocate-field">
                <span class="reallocate-field-label">Articles par jour</span>
                <div class="reallocate-options">
                    <label class="reallocate-option">
                        <input type="radio" name="articlesPerDay" value="1">
                        <span class="reallocate-option-label">1 article / jour</span>
                        <span class="reallocate-option-hours">10h</span>
                    </label>
                    <label class="reallocate-option">
                        <input type="radio" name="articlesPerDay" value="2">
                        <span class="reallocate-option-label">2 articles / jour</span>
                        <span class="reallocate-option-hours">10h, 14h</span>
                    </label>
                    <label class="reallocate-option">
                        <input type="radio" name="articlesPerDay" value="3">
                        <span class="reallocate-option-label">3 articles / jour</span>
                        <span class="reallocate-option-hours">10h, 14h, 11h</span>
                    </label>
                    <label class="reallocate-option">
                        <input type="radio" name="articlesPerDay" value="4">
                        <span class="reallocate-option-label">4 articles / jour</span>
                        <span class="reallocate-option-hours">10h, 14h, 11h, 12h</span>
                    </label>
                    <label class="reallocate-option">
                        <input type="radio" name="articlesPerDay" value="5" checked>
                        <span class="reallocate-option-label">5 articles / jour</span>
                        <span class="reallocate-option-hours">10h, 14h, 11h, 12h, 13h</span>
                    </label>
                </div>
            </div>
            <div class="reallocate-actions">
                <button type="button" class="button" id="reallocateCancel">Annuler</button>
                <button type="button" class="button button-primary" id="reallocateConfirm">Réallouer</button>
            </div>
        </div>
    </dialog>

    <dialog id="reallocateResultDialog" class="reallocate-result-dialog" aria-labelledby="reallocateResultTitle">
        <div class="reallocate-result-dialog__inner">
            <h2 id="reallocateResultTitle">Réallocation</h2>
            <div id="reallocateResultBody" class="reallocate-result-body"></div>
            <div class="reallocate-result-actions">
                <button type="button" class="button button-primary" id="reallocateResultClose">Fermer</button>
            </div>
        </div>
    </dialog>

    <script data-jetpack-boost="ignore">
    document.addEventListener('DOMContentLoaded', function() {
        let currentDate = new Date();
        let currentViewMode = 'multi';
        let nextMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 1);
        let visibleMonths = [new Date(currentDate.getFullYear(), currentDate.getMonth(), 1), nextMonth];

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
        const AUTO_SCHEDULE_ALLOWED_HOURS = [10, 14, 11, 12, 13];
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

            const allowedHours = AUTO_SCHEDULE_ALLOWED_HOURS;
            const maxSlots = allowedHours.length;

            return sortedPosts.slice(0, maxSlots).map((post, index) => {
                const hour = allowedHours[index];
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

        // ======== V19: Réallocation avec choix du nombre d'articles par jour ========
        const reallocateDraftsButton = document.getElementById('reallocateDrafts');
        const reallocateStatus = document.getElementById('reallocateStatus');
        const reallocateDialog = document.getElementById('reallocateDialog');
        const reallocateCancel = document.getElementById('reallocateCancel');
        const reallocateConfirm = document.getElementById('reallocateConfirm');
        const reallocateOptions = document.querySelectorAll('.reallocate-option');
        const reallocateResultDialog = document.getElementById('reallocateResultDialog');
        const reallocateResultTitle = document.getElementById('reallocateResultTitle');
        const reallocateResultBody = document.getElementById('reallocateResultBody');
        const reallocateResultClose = document.getElementById('reallocateResultClose');

        function setReallocateStatus(message, isError = false) {
            if (!reallocateStatus) return;
            reallocateStatus.textContent = message || '';
            reallocateStatus.classList.toggle('is-error', Boolean(message) && isError);
            reallocateStatus.classList.toggle('is-visible', Boolean(message));
        }

        function escapeHtml(str) {
            return String(str == null ? '' : str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function buildResultSection(icon, title, tone, rows, meta) {
            const section = document.createElement('section');
            section.className = 'reallocate-result-section' + (tone ? ' is-' + tone : '');

            const head = document.createElement('div');
            head.className = 'reallocate-result-section__title';
            head.innerHTML = (icon ? `<span aria-hidden="true">${icon}</span>` : '') + escapeHtml(title);
            section.appendChild(head);

            if (rows && rows.length) {
                const list = document.createElement('ul');
                rows.forEach(row => {
                    const li = document.createElement('li');
                    const labelSpan = document.createElement('span');
                    labelSpan.className = 'label';
                    labelSpan.textContent = row.label;
                    const valueSpan = document.createElement('span');
                    valueSpan.className = 'value';
                    valueSpan.textContent = row.value;
                    li.appendChild(labelSpan);
                    li.appendChild(valueSpan);
                    list.appendChild(li);
                });
                section.appendChild(list);
            }

            if (meta) {
                const metaDiv = document.createElement('div');
                metaDiv.className = 'meta';
                metaDiv.textContent = meta;
                section.appendChild(metaDiv);
            }

            return section;
        }

        function showReallocateResult(options) {
            if (!reallocateResultDialog || typeof reallocateResultDialog.showModal !== 'function') {
                const fallback = (options && options.fallbackText) || '';
                if (fallback) alert(fallback);
                return;
            }

            if (reallocateResultTitle) reallocateResultTitle.textContent = options.title || 'Réallocation';
            if (reallocateResultBody && typeof reallocateResultBody.replaceChildren === 'function') {
                reallocateResultBody.replaceChildren();
            } else if (reallocateResultBody) {
                reallocateResultBody.innerHTML = '';
            }

            (options.sections || []).forEach(section => {
                if (!section) return;
                // Accepte soit un nœud DOM déjà construit, soit un objet de config.
                if (section.nodeType === 1) {
                    reallocateResultBody.appendChild(section);
                } else {
                    reallocateResultBody.appendChild(
                        buildResultSection(section.icon, section.title, section.tone, section.rows, section.meta)
                    );
                }
            });

            reallocateResultDialog.showModal();
        }

        if (reallocateResultClose) {
            reallocateResultClose.addEventListener('click', function() {
                if (reallocateResultDialog.open) reallocateResultDialog.close();
            });
        }
        if (reallocateResultDialog) {
            reallocateResultDialog.addEventListener('cancel', function(event) {
                event.preventDefault();
                reallocateResultDialog.close();
            });
        }

        reallocateOptions.forEach(option => {
            option.addEventListener('click', function() {
                const group = this.closest('.reallocate-options');
                const siblings = group ? group.querySelectorAll('.reallocate-option') : reallocateOptions;
                siblings.forEach(o => o.classList.remove('selected'));
                this.classList.add('selected');
                const radio = this.querySelector('input[type="radio"]');
                if (radio) radio.checked = true;
            });
        });

        // Marquer visuellement les options cochées au chargement
        document.querySelectorAll('.reallocate-option input[type="radio"]:checked').forEach(radio => {
            const opt = radio.closest('.reallocate-option');
            if (opt) opt.classList.add('selected');
        });

        if (reallocateDraftsButton && reallocateDialog) {
            reallocateDraftsButton.addEventListener('click', function() {
                if (typeof reallocateDialog.showModal === 'function') {
                    reallocateDialog.showModal();
                } else {
                    alert('Votre navigateur ne supporte pas les dialogues natifs.');
                }
            });

            reallocateCancel.addEventListener('click', function() {
                reallocateDialog.close();
            });

            reallocateDialog.addEventListener('cancel', function(event) {
                event.preventDefault();
                reallocateDialog.close();
            });

            reallocateConfirm.addEventListener('click', function() {
                const selectedRadio = document.querySelector('input[name="articlesPerDay"]:checked');
                const articlesPerDay = selectedRadio ? parseInt(selectedRadio.value, 10) : 5;
                const modeRadio = document.querySelector('input[name="reallocateMode"]:checked');
                const mode = modeRadio ? modeRadio.value : 'compact';
                const isCompact = mode === 'compact';

                reallocateDialog.close();
                reallocateDraftsButton.disabled = true;
                reallocateDraftsButton.textContent = '⏳ Réallocation...';
                setReallocateStatus(
                    isCompact
                        ? `Compactage des planifiés + réallocation des brouillons (${articlesPerDay} article(s)/jour)...`
                        : `Vérification des articles planifiés + réallocation des brouillons (${articlesPerDay} article(s)/jour)...`
                );

                fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                    },
                    body: 'action=clm_reallocate_overdue_posts&nonce=<?php echo rawurlencode( wp_create_nonce('clm_reallocate_overdue_posts') ); ?>&articles_per_day=' + articlesPerDay + '&mode=' + encodeURIComponent(mode)
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
                    const remainingTodayIds = Array.isArray(data.remaining_today_ids) ? data.remaining_today_ids.map(id => `#${id}`).join(', ') : '';

                    // V21: infos articles planifiés (vérification en mode drafts, compactage en mode compact)
                    const normTotal = parseInt(data.normalized_total || 0, 10);
                    const normMoved = parseInt(data.normalized_moved || 0, 10);
                    const normUnchanged = parseInt(data.normalized_unchanged || 0, 10);
                    const normCascaded = parseInt(data.normalized_cascaded || 0, 10);
                    const normFailed = parseInt(data.normalized_failed || 0, 10);
                    const normFailedIds = Array.isArray(data.normalized_failed_ids) ? data.normalized_failed_ids.map(id => `#${id}`).join(', ') : '';

                    const planifiedVerb = isCompact ? 'compactés' : 'vérifiés';
                    const planifiedTitle = isCompact ? 'Articles planifiés (compactage)' : 'Articles planifiés (vérification)';
                    const planifiedStatusLine = normTotal > 0
                        ? ` | Planifiés ${planifiedVerb}: ${normTotal} (${isCompact ? `déplacés ${normMoved}, inchangés ${normUnchanged}` : `corrigés ${normMoved}, décalés ${normCascaded}, inchangés ${normUnchanged}`})`
                        : '';

                    function buildPlanifiedRows() {
                        const rows = [{ label: 'Total', value: String(normTotal) }];
                        if (isCompact) {
                            rows.push({ label: 'Déplacés', value: String(normMoved) });
                        } else {
                            rows.push({ label: 'Corrigés', value: String(normMoved) });
                            rows.push({ label: 'Décalés J+1', value: String(normCascaded) });
                        }
                        rows.push({ label: 'Inchangés', value: String(normUnchanged) });
                        if (normFailed > 0) rows.push({ label: 'Échecs', value: String(normFailed) + (normFailedIds ? ` (${normFailedIds})` : '') });
                        return rows;
                    }

                    const needsRefresh = normMoved > 0 || normCascaded > 0;

                    if (total === 0) {
                        const candidates = parseInt(data.candidates || 0, 10);
                        const todayRef = data.today ? data.today : '';
                        setReallocateStatus(
                            normTotal > 0
                                ? `Planifiés ${planifiedVerb}: ${normTotal}. Aucun brouillon à réallouer.`
                                : `Aucun contenu en retard trouvé. Candidats analysés: ${candidates}.`,
                            normFailed > 0
                        );
                        const sections = [];
                        sections.push(buildResultSection('🗒️', 'Brouillons', 'info', [
                            { label: 'Candidats analysés', value: String(candidates) },
                            { label: 'Déplacés', value: '0' }
                        ]));
                        if (normTotal > 0) {
                            sections.push(buildResultSection('📅', planifiedTitle, normFailed > 0 ? 'error' : 'success', buildPlanifiedRows()));
                        }
                        showReallocateResult({
                            title: 'Aucun brouillon à réallouer',
                            sections: sections,
                            meta: todayRef,
                            fallbackText: `Aucun brouillon à réallouer.\nCandidats analysés: ${candidates}${normTotal > 0 ? `\n\n📅 Articles planifiés ${planifiedVerb}: ${normTotal}` : ''}${todayRef}`
                        });
                        if (needsRefresh) refreshCurrentView();
                        return;
                    }

                    setReallocateStatus(`Terminé. Déplacés: ${moved}/${total}. Échecs: ${failed}.${planifiedStatusLine}`, failed > 0 || remainingTodayIds.length > 0 || normFailed > 0);
                    const details = failed > 0 && failedIds ? `IDs en échec: ${failedIds}` : '';
                    const remainingDetails = remainingTodayIds ? `Restent aujourd'hui/avant: ${remainingTodayIds}` : '';
                    const successSections = [];
                    successSections.push(buildResultSection(
                        '✅',
                        'Réallocation brouillons',
                        failed > 0 ? 'error' : 'success',
                        [
                            { label: 'Déplacés', value: `${moved} / ${total}` },
                            { label: 'Échecs', value: String(failed) }
                        ],
                        [details, remainingDetails].filter(Boolean).join(' · ')
                    ));
                    if (normTotal > 0) {
                        successSections.push(buildResultSection('📅', planifiedTitle, normFailed > 0 ? 'error' : 'success', buildPlanifiedRows()));
                    }
                    // V23: debug — placement détaillé des brouillons
                    const nextSlots = Array.isArray(data.next_slots) ? data.next_slots : [];
                    if (nextSlots.length > 0) {
                        const placementRows = nextSlots.map(s => ({
                            label: `#${s.id}`,
                            value: String(s.date)
                        }));
                        successSections.push(buildResultSection('📍', 'Placement des brouillons', 'info', placementRows));
                    }
                    // V25: debug — occupation à partir d'aujourd'hui
                    const debugOcc = Array.isArray(data.debug_occupancy) ? data.debug_occupancy : [];
                    if (debugOcc.length > 0) {
                        const occRows = debugOcc.map(d => ({
                            label: d.date,
                            value: `${d.count} post(s) — heures: [${Array.isArray(d.hours) ? d.hours.join(', ') : ''}]`
                        }));
                        const startDay = (data.today || '').slice(0, 10) || '?';
                        successSections.push(buildResultSection('🔍', `Occupation (à partir de ${startDay})`, 'info', occRows));
                    }
                    showReallocateResult({
                        title: 'Réallocation terminée',
                        sections: successSections,
                        fallbackText: `✅ Réallocation terminée !\nDéplacés: ${moved}/${total}\nÉchecs: ${failed}${details ? '\n' + details : ''}${remainingDetails ? '\n' + remainingDetails : ''}`
                    });
                    refreshCurrentView();
                })
                .catch(error => {
                    console.error('Erreur réallocation:', error);
                    setReallocateStatus(`Erreur: ${error.message}`, true);
                    showReallocateResult({
                        title: 'Erreur',
                        sections: [buildResultSection('❌', 'Échec de la réallocation', 'error', [], error.message)]
                    });
                })
                .finally(() => {
                    reallocateDraftsButton.disabled = false;
                    reallocateDraftsButton.innerHTML = '<span class="dashicons dashicons-randomize"></span> Réallouer';
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

// V27: Les articles avec image mise en avant sont toujours traités avant ceux sans image.
function clm_prioritize_posts_with_featured_image( array $posts ) {
    usort( $posts, function( $left, $right ) {
        $left_has_image  = has_post_thumbnail( $left->ID );
        $right_has_image = has_post_thumbnail( $right->ID );

        if ( $left_has_image !== $right_has_image ) {
            return $left_has_image ? -1 : 1;
        }

        $date_comparison = strcmp( $left->post_date, $right->post_date );
        return $date_comparison ?: ( $left->ID <=> $right->ID );
    } );

    return $posts;
}

// V23: Normalisation des articles planifiés (future) sur les créneaux.
// - Compte désormais les articles PUBLIÉS pour calculer la capacité réelle d'un jour.
// - Respecte $articles_per_day (le total publish+future ne dépasse jamais ce plafond).
// - Regroupe par jour, trie par date, repositionne sur les slots disponibles.
// - Débordement cascadé vers J+1 (et au-delà si nécessaire).
function clm_normalize_future_posts_schedule( array $slot_hours, $articles_per_day = 5 ) {
    global $wpdb;
    $tz = wp_timezone();

    $future_posts = get_posts( array(
        'post_type'      => 'post',
        'post_status'    => array( 'future' ),
        'posts_per_page' => -1,
        'numberposts'    => -1,
        'orderby'        => 'date',
        'order'          => 'ASC',
    ) );

    $future_posts = clm_prioritize_posts_with_featured_image( $future_posts );

    $result = array(
        'total'      => count( $future_posts ),
        'normalized' => 0,
        'unchanged'  => 0,
        'cascaded'   => 0,
        'failed'     => 0,
        'failed_ids' => array(),
    );

    if ( ! $future_posts ) {
        return $result;
    }

    $by_day = array();
    foreach ( $future_posts as $post ) {
        $day_key = substr( $post->post_date, 0, 10 );
        if ( ! isset( $by_day[ $day_key ] ) ) {
            $by_day[ $day_key ] = array();
        }
        $by_day[ $day_key ][] = $post;
    }
    ksort( $by_day );

    $max_per_day   = min( (int) $articles_per_day, count( $slot_hours ) );
    $min_day_key   = array_key_first( $by_day );
    $current       = new DateTimeImmutable( $min_day_key, $tz );
    $overflow      = array();
    $safety        = 2000;
    $future_ids    = array_map( 'intval', wp_list_pluck( $future_posts, 'ID' ) );
    $pub_cache     = array();

    // V23: récupère les créneaux déjà occupés par des articles publiés sur un jour donné.
    $get_published_hours = function( DateTimeImmutable $day ) use ( &$pub_cache, $slot_hours, $wpdb, $future_ids ) {
        $key = $day->format( 'Y-m-d' );
        if ( isset( $pub_cache[ $key ] ) ) {
            return $pub_cache[ $key ];
        }

        $exclude_sql = '';
        $params      = array( $key );
        if ( $future_ids ) {
            $exclude_sql = ' AND ID NOT IN (' . implode( ',', array_fill( 0, count( $future_ids ), '%d' ) ) . ')';
            $params      = array_merge( $params, $future_ids );
        }

        $row = $wpdb->get_row( $wpdb->prepare(
            "
            SELECT GROUP_CONCAT(DISTINCT HOUR(post_date)) AS hours
            FROM {$wpdb->posts}
            WHERE post_type = 'post'
              AND post_status = 'publish'
              AND DATE(post_date) = %s
              {$exclude_sql}
            ",
            $params
        ) );

        $taken = $row && null !== $row->hours ? array_map( 'intval', explode( ',', $row->hours ) ) : array();
        $taken = array_values( array_intersect( $slot_hours, $taken ) );
        $pub_cache[ $key ] = array_values( array_unique( $taken ) );
        return $pub_cache[ $key ];
    };

    while ( ( $overflow || $by_day ) && $safety-- > 0 ) {
        $day_key = $current->format( 'Y-m-d' );

        $pool = array();
        if ( isset( $by_day[ $day_key ] ) ) {
            $pool = $by_day[ $day_key ];
            unset( $by_day[ $day_key ] );
        }

        $all_today = array_merge( $pool, $overflow );

        if ( ! $all_today ) {
            $current = $current->modify( '+1 day' );
            continue;
        }

        // V23: retirer les créneaux déjà pris par des publiés, puis plafonner à max_per_day.
        $published_hours = $get_published_hours( $current );
        $available_hours = array_values( array_diff( $slot_hours, $published_hours ) );
        $day_capacity    = min( $max_per_day, count( $available_hours ) );

        $to_assign    = array_slice( $all_today, 0, $day_capacity );
        $new_overflow = array_slice( $all_today, $day_capacity );

        foreach ( $to_assign as $i => $post ) {
            if ( ! current_user_can( 'edit_post', $post->ID ) ) {
                $result['failed']++;
                $result['failed_ids'][] = (int) $post->ID;
                continue;
            }

            $hour         = (int) $available_hours[ $i ];
            $target_slot  = $current->setTime( $hour, 0, 0 )->format( 'Y-m-d H:i:s' );
            $original_day = substr( $post->post_date, 0, 10 );

            if ( $post->post_date === $target_slot ) {
                $result['unchanged']++;
                continue;
            }

            $r = wp_update_post( array(
                'ID'            => (int) $post->ID,
                'post_date'     => $target_slot,
                'post_date_gmt' => get_gmt_from_date( $target_slot ),
            ), true );

            if ( is_wp_error( $r ) ) {
                $result['failed']++;
                $result['failed_ids'][] = (int) $post->ID;
                continue;
            }

            clean_post_cache( $post->ID );
            $result['normalized']++;
            if ( $original_day !== $day_key ) {
                $result['cascaded']++;
            }
        }

        $overflow = $new_overflow;
        $current  = $current->modify( '+1 day' );
    }

    $result['failed_ids'] = array_values( array_unique( $result['failed_ids'] ) );
    return $result;
}

// V23: Compactage des articles planifiés (future) vers les premiers jours libres.
// - Reprend TOUS les future, triés par date, et les re-flotte à partir de $start_day.
// - V23: tient compte des articles PUBLIÉS: leurs créneaux sont exclus, et la capacité
//   d'un jour = $articles_per_day - count(publiés sur ce jour).
// - Pour le jour de départ (aujourd'hui): créneaux CHRONOLOGIQUES [10,11,12,13,14],
//   on saute ceux déjà passés ET ceux déjà pris par un article publié.
// - Pour les jours suivants: ordre prioritaire [10,14,11,12,13], hors créneaux publiés.
function clm_compact_future_posts( array $slot_hours, $articles_per_day, DateTimeImmutable $start_day ) {
    global $wpdb;
    $tz  = wp_timezone();
    $now = new DateTimeImmutable( 'now', $tz );

    $future_posts = get_posts( array(
        'post_type'      => 'post',
        'post_status'    => array( 'future' ),
        'posts_per_page' => -1,
        'numberposts'    => -1,
        'orderby'        => 'date',
        'order'          => 'ASC',
    ) );

    $future_posts = clm_prioritize_posts_with_featured_image( $future_posts );

    $result = array(
        'total'      => count( $future_posts ),
        'normalized' => 0,
        'unchanged'  => 0,
        'cascaded'   => 0,
        'failed'     => 0,
        'failed_ids' => array(),
    );

    if ( ! $future_posts ) {
        return $result;
    }

    $max_slots  = min( (int) $articles_per_day, count( $slot_hours ) );
    $start_key  = $start_day->format( 'Y-m-d' );
    $future_ids = array_map( 'intval', wp_list_pluck( $future_posts, 'ID' ) );
    $pub_cache  = array();

    // V23: récupère les créneaux déjà occupés par des publiés sur un jour donné.
    $get_published_hours = function( DateTimeImmutable $day ) use ( &$pub_cache, $slot_hours, $wpdb, $future_ids ) {
        $key = $day->format( 'Y-m-d' );
        if ( isset( $pub_cache[ $key ] ) ) {
            return $pub_cache[ $key ];
        }

        $exclude_sql = '';
        $params      = array( $key );
        if ( $future_ids ) {
            $exclude_sql = ' AND ID NOT IN (' . implode( ',', array_fill( 0, count( $future_ids ), '%d' ) ) . ')';
            $params      = array_merge( $params, $future_ids );
        }

        $row = $wpdb->get_row( $wpdb->prepare(
            "
            SELECT GROUP_CONCAT(DISTINCT HOUR(post_date)) AS hours
            FROM {$wpdb->posts}
            WHERE post_type = 'post'
              AND post_status = 'publish'
              AND DATE(post_date) = %s
              {$exclude_sql}
            ",
            $params
        ) );

        $taken = $row && null !== $row->hours ? array_map( 'intval', explode( ',', $row->hours ) ) : array();
        $taken = array_values( array_intersect( $slot_hours, $taken ) );
        $pub_cache[ $key ] = array_values( array_unique( $taken ) );
        return $pub_cache[ $key ];
    };

    // Renvoie la liste ordonnée des créneaux utilisables pour un jour donné.
    $day_slots_for = function( DateTimeImmutable $day ) use ( $slot_hours, $max_slots, $start_key, $now, $get_published_hours ) {
        $published_hours = $get_published_hours( $day );

        if ( $day->format( 'Y-m-d' ) === $start_key ) {
            // Aujourd'hui: chronologique, on saute les créneaux passés ET les créneaux publiés.
            $chronological = $slot_hours;
            sort( $chronological );
            $available = array();
            foreach ( $chronological as $h ) {
                $slot_dt = $day->setTime( (int) $h, 0, 0 );
                if ( $slot_dt > $now && ! in_array( (int) $h, $published_hours, true ) ) {
                    $available[] = (int) $h;
                }
            }
            return array_slice( $available, 0, $max_slots );
        }
        // Jour complet: ordre prioritaire, hors créneaux publiés.
        $priority  = array_map( 'intval', $slot_hours );
        $available = array_values( array_diff( $priority, $published_hours ) );
        return array_slice( $available, 0, $max_slots );
    };

    $current_day = $start_day;
    $day_slots   = $day_slots_for( $current_day );
    $slot_pos    = 0;
    $day_count   = array(); // day_key => int

    foreach ( $future_posts as $post ) {
        // Avance jour par jour tant que le jour courant n'a plus de créneau libre.
        while ( true ) {
            $day_key = $current_day->format( 'Y-m-d' );
            $count   = isset( $day_count[ $day_key ] ) ? $day_count[ $day_key ] : 0;
            if ( $count < $max_slots && $slot_pos < count( $day_slots ) ) {
                break;
            }
            $current_day = $current_day->modify( '+1 day' );
            $day_slots   = $day_slots_for( $current_day );
            $slot_pos    = 0;
        }

        $day_key       = $current_day->format( 'Y-m-d' );
        $hour          = (int) $day_slots[ $slot_pos ];
        $target_slot   = $current_day->setTime( $hour, 0, 0 )->format( 'Y-m-d H:i:s' );
        $original_day  = substr( $post->post_date, 0, 10 );

        if ( ! isset( $day_count[ $day_key ] ) ) $day_count[ $day_key ] = 0;
        $day_count[ $day_key ]++;
        $slot_pos++;

        if ( $post->post_date === $target_slot ) {
            $result['unchanged']++;
            continue;
        }

        if ( ! current_user_can( 'edit_post', $post->ID ) ) {
            $result['failed']++;
            $result['failed_ids'][] = (int) $post->ID;
            continue;
        }

        $r = wp_update_post( array(
            'ID'            => (int) $post->ID,
            'post_date'     => $target_slot,
            'post_date_gmt' => get_gmt_from_date( $target_slot ),
        ), true );

        if ( is_wp_error( $r ) ) {
            $result['failed']++;
            $result['failed_ids'][] = (int) $post->ID;
            continue;
        }

        clean_post_cache( $post->ID );
        $result['normalized']++;
        if ( $original_day !== $day_key ) {
            $result['cascaded']++;
        }
    }

    $result['failed_ids'] = array_values( array_unique( $result['failed_ids'] ) );
    return $result;
}
// les créneaux déjà pris et le plafond quotidien choisi.
function clm_reallocate_overdue_posts_ajax() {
    global $wpdb;

    if ( ! current_user_can( 'edit_posts' ) ) wp_send_json_error( array( 'message' => 'Permissions insuffisantes.' ), 403 );
    check_ajax_referer( 'clm_reallocate_overdue_posts', 'nonce' );

    $articles_per_day = isset( $_POST['articles_per_day'] ) ? max( 1, min( 5, intval( $_POST['articles_per_day'] ) ) ) : 5;
    $mode = isset( $_POST['mode'] ) ? sanitize_key( wp_unslash( $_POST['mode'] ) ) : 'compact';
    if ( ! in_array( $mode, array( 'drafts', 'compact' ), true ) ) $mode = 'compact';

    $tz = wp_timezone();
    $today = new DateTimeImmutable( 'today', $tz );
    $now  = new DateTimeImmutable( 'now', $tz );
    $today_end = $today->setTime( 23, 59, 59 );
    $tomorrow = $today->modify( '+1 day' );
    $slot_hours = array( 10, 14, 11, 12, 13 );

    // V22: selon le mode, on normalise (fixe les créneaux intra-jour) ou on compacte
    // (re-flotte tous les planifiés vers les premiers jours libres dès AUJOURD'HUI,
    //  en sautant les créneaux déjà passés).
    if ( $mode === 'compact' ) {
        $normalized = clm_compact_future_posts( $slot_hours, $articles_per_day, $today );
    } else {
        $normalized = clm_normalize_future_posts_schedule( $slot_hours, $articles_per_day );
    }

    $candidates = get_posts( array(
        'post_type'      => 'post',
        'post_status'    => array( 'draft' ),
        'posts_per_page' => -1,
        'numberposts'    => -1,
        'orderby'        => 'date',
        'order'          => 'ASC',
    ) );

    $candidates = clm_prioritize_posts_with_featured_image( $candidates );
    $draft_ids = array_values( array_filter( array_map( 'intval', wp_list_pluck( $candidates, 'ID' ) ) ) );

    if ( ! $draft_ids ) wp_send_json_success( array(
        'moved' => 0,
        'failed' => 0,
        'failed_ids' => array(),
        'total' => 0,
        'candidates' => 0,
        'today' => $today->format( 'Y-m-d H:i:s' ),
        'tomorrow' => $tomorrow->format( 'Y-m-d' ),
        'next_slots' => array(),
        'remaining_today_ids' => array(),
        'articles_per_day' => $articles_per_day,
        'mode' => $mode,
        'normalized_total'      => (int) $normalized['total'],
        'normalized_moved'      => (int) $normalized['normalized'],
        'normalized_unchanged'  => (int) $normalized['unchanged'],
        'normalized_cascaded'   => (int) $normalized['cascaded'],
        'normalized_failed'     => (int) $normalized['failed'],
        'normalized_failed_ids' => array_values( array_map( 'intval', $normalized['failed_ids'] ) ),
        'debug_occupancy'       => array(),
    ) );

    $moved = 0;
    $failed = 0;
    $failed_ids = array();
    $next_slots = array();
    $schedule_cache = array();
    // V25: on démarre dès AUJOURD'HUI (et non plus demain) pour remplir aussi
    // les créneaux restants du jour courant, dans la limite partagée publish+future+draft.
    $day = $today;

    $get_day_occupancy = function( DateTimeImmutable $target_day ) use ( &$schedule_cache, $slot_hours, $wpdb, $draft_ids ) {
        $key = $target_day->format( 'Y-m-d' );
        if ( isset( $schedule_cache[ $key ] ) ) {
            return $schedule_cache[ $key ];
        }

        $candidate_placeholders = implode( ',', array_fill( 0, count( $draft_ids ), '%d' ) );
        $params = array_merge(
            array( $key ),
            $draft_ids
        );

        $row = $wpdb->get_row( $wpdb->prepare(
            "
            SELECT COUNT(ID) AS total, GROUP_CONCAT(DISTINCT HOUR(post_date)) AS hours
            FROM {$wpdb->posts}
            WHERE post_type = 'post'
              AND post_status IN ('publish', 'future', 'draft')
              AND DATE(post_date) = %s
              AND ID NOT IN ($candidate_placeholders)
            ",
            $params
        ) );

        $taken_hours = $row && $row->hours !== null ? array_map( 'intval', explode( ',', $row->hours ) ) : array();
        $taken_hours = array_values( array_intersect( $slot_hours, $taken_hours ) );

        $schedule_cache[ $key ] = array(
            'count' => $row ? (int) $row->total : 0,
            'hours' => array_values( array_unique( $taken_hours ) ),
        );
        sort( $schedule_cache[ $key ]['hours'] );
        return $schedule_cache[ $key ];
    };

    $is_slot_taken = function( DateTimeImmutable $target_day, int $hour, int $post_id ) use ( $wpdb, $draft_ids ) {
        $candidate_placeholders = implode( ',', array_fill( 0, count( $draft_ids ), '%d' ) );
        $params = array_merge(
            array( $target_day->format( 'Y-m-d' ), $hour, $post_id ),
            $draft_ids
        );

        return (bool) $wpdb->get_var( $wpdb->prepare(
            "
            SELECT ID
            FROM {$wpdb->posts}
            WHERE post_type = 'post'
              AND post_status IN ('publish', 'future', 'draft')
              AND DATE(post_date) = %s
              AND HOUR(post_date) = %d
              AND ID <> %d
              AND ID NOT IN ($candidate_placeholders)
            LIMIT 1
            ",
            $params
        ) );
    };

    // V25: calcule les créneaux libres pour un jour donné.
    // - Ordre prioritaire [10,14,11,12,13] pour TOUS les jours (cohérent avec le dialog).
    // - Pour aujourd'hui: on filtre en plus les créneaux déjà passés.
    $free_slots_for = function( DateTimeImmutable $target_day, array $taken_hours ) use ( $slot_hours, $today, $now ) {
        $priority  = array_map( 'intval', $slot_hours );
        $available = array_values( array_diff( $priority, $taken_hours ) );

        if ( $target_day->format( 'Y-m-d' ) === $today->format( 'Y-m-d' ) ) {
            $available = array_values( array_filter( $available, function( $h ) use ( $target_day, $now ) {
                return $target_day->setTime( (int) $h, 0, 0 ) > $now;
            } ) );
        }
        return $available;
    };

    foreach ( $draft_ids as $post_id ) {
        if ( ! current_user_can( 'edit_post', $post_id ) ) { $failed++; $failed_ids[] = (int) $post_id; continue; }

        $occupancy = $get_day_occupancy( $day );
        $taken_hours = $occupancy['hours'];
        $day_count = $occupancy['count'];
        $free_slots = $free_slots_for( $day, $taken_hours );

        while ( $day_count >= $articles_per_day || ! $free_slots ) {
            $day = $day->modify( '+1 day' );
            $occupancy = $get_day_occupancy( $day );
            $taken_hours = $occupancy['hours'];
            $day_count = $occupancy['count'];
            $free_slots = $free_slots_for( $day, $taken_hours );
        }

        $hour = array_shift( $free_slots );
        while ( $is_slot_taken( $day, $hour, (int) $post_id ) ) {
            $taken_hours[] = $hour;
            $taken_hours = array_values( array_unique( array_map( 'intval', $taken_hours ) ) );
            sort( $taken_hours );
            $schedule_cache[ $day->format( 'Y-m-d' ) ] = array(
                'count' => $day_count,
                'hours' => $taken_hours,
            );

            $free_slots = $free_slots_for( $day, $taken_hours );
            while ( $day_count >= $articles_per_day || ! $free_slots ) {
                $day = $day->modify( '+1 day' );
                $occupancy = $get_day_occupancy( $day );
                $taken_hours = $occupancy['hours'];
                $day_count = $occupancy['count'];
                $free_slots = $free_slots_for( $day, $taken_hours );
            }
            $hour = array_shift( $free_slots );
        }

        $slot = $day->setTime( $hour, 0, 0 )->format( 'Y-m-d H:i:s' );

        $r = wp_update_post( array( 'ID' => (int) $post_id, 'post_date' => $slot, 'post_date_gmt' => get_gmt_from_date( $slot ) ), true );
        if ( is_wp_error( $r ) ) { $failed++; $failed_ids[] = (int) $post_id; continue; }
        clean_post_cache( $post_id );
        $updated_post = get_post( $post_id );
        if ( ! $updated_post || $updated_post->post_date !== $slot ) { $failed++; $failed_ids[] = (int) $post_id; continue; }
        $moved++;
        $next_slots[] = array( 'id' => (int) $post_id, 'date' => $slot );

        $taken_hours[] = $hour;
        $taken_hours = array_values( array_unique( $taken_hours ) );
        sort( $taken_hours );
        $day_count++;
        $schedule_cache[ $day->format( 'Y-m-d' ) ] = array(
            'count' => $day_count,
            'hours' => $taken_hours,
        );

        if ( $day_count >= $articles_per_day ) {
            $day = $day->modify( '+1 day' );
        }
    }

    $remaining_today_ids = get_posts( array(
        'post_type'      => 'post',
        'post_status'    => array( 'draft' ),
        'posts_per_page' => -1,
        'numberposts'    => -1,
        'date_query'     => array(
            array(
                'column'    => 'post_date',
                'before'    => $today_end->format( 'Y-m-d H:i:s' ),
                'inclusive' => true,
            ),
        ),
        'fields'         => 'ids',
    ) );

    // V25: debug — occupation des 5 prochains jours à partir d'aujourd'hui
    $debug_occupancy = array();
    $debug_day = clone $today;
    for ( $i = 0; $i < 6; $i++ ) {
        $occ = $get_day_occupancy( $debug_day );
        $debug_occupancy[] = array(
            'date'  => $debug_day->format( 'Y-m-d' ),
            'count' => $occ['count'],
            'hours' => $occ['hours'],
        );
        $debug_day = $debug_day->modify( '+1 day' );
    }

    wp_send_json_success( array(
        'moved'                 => $moved,
        'failed'                => $failed,
        'failed_ids'            => array_values( array_unique( $failed_ids ) ),
        'total'                 => count( $draft_ids ),
        'candidates'            => count( $candidates ),
        'today'                 => $today->format( 'Y-m-d H:i:s' ),
        'tomorrow'              => $tomorrow->format( 'Y-m-d' ),
        'next_slots'            => array_slice( $next_slots, 0, 10 ),
        'remaining_today_ids'   => array_values( array_map( 'intval', $remaining_today_ids ) ),
        'articles_per_day'      => $articles_per_day,
        'mode'                  => $mode,
        'normalized_total'      => (int) $normalized['total'],
        'normalized_moved'      => (int) $normalized['normalized'],
        'normalized_unchanged'  => (int) $normalized['unchanged'],
        'normalized_cascaded'   => (int) $normalized['cascaded'],
        'normalized_failed'     => (int) $normalized['failed'],
        'normalized_failed_ids' => array_values( array_map( 'intval', $normalized['failed_ids'] ) ),
        'debug_occupancy'       => $debug_occupancy,
    ) );
}
add_action( 'wp_ajax_clm_reallocate_overdue_posts', 'clm_reallocate_overdue_posts_ajax' );

// ════════════════════════════════════════════════════════════════════════════
// V29: Notification flottante permanente — articles manquants du jour
// ════════════════════════════════════════════════════════════════════════════
// Affiche en haut à droite de TOUTES les pages admin une notification flottante
// qui indique combien d'articles manquent pour atteindre l'objectif du jour
// (par défaut 5 articles/jour). Seuls les articles PUBLIÉS (publish) et
// PLANIFIÉS (future) sont comptés — les brouillons (draft) sont ignorés car
// non confirmés. La notification ne s'affiche QUE si le quota n'est pas atteint.
// Un lien vers le calendrier est toujours présent.
// ════════════════════════════════════════════════════════════════════════════

if ( ! function_exists( 'clm_daily_quota_get_status' ) ) {
    /**
     * Calcule l'état du quota d'articles du jour courant.
     *
     * ATTENTION: seuls les statuts 'publish' et 'future' sont comptés.
     * Les brouillons (draft) NE comptent PAS car non confirmés pour la journée.
     *
     * @param int $articles_per_day Objectif d'articles par jour (défaut 5).
     * @return array {
     *     @type int    $target        Cible d'articles pour aujourd'hui.
     *     @type int    $count         Nombre d'articles (publish+future) déjà prévus aujourd'hui.
     *     @type int    $missing       Nombre d'articles manquants (max 0 si atteint).
     *     @type string $status        Etat brut : 'complete' | 'partial' | 'empty'.
     *     @type array  $post_ids      Liste des IDs des articles du jour.
     *     @type string $today_date    Date du jour (Y-m-d).
     * }
     */
    function clm_daily_quota_get_status( $articles_per_day = 5 ) {
        $tz = wp_timezone();
        $today = new DateTimeImmutable( 'today', $tz );
        $today_end = $today->setTime( 23, 59, 59 );

        $target = max( 1, (int) $articles_per_day );

        $post_ids = get_posts( array(
            'post_type'      => 'post',
            'post_status'    => array( 'publish', 'future' ),
            'posts_per_page' => -1,
            'numberposts'    => -1,
            'fields'         => 'ids',
            'orderby'        => 'date',
            'order'          => 'ASC',
            'date_query'     => array(
                array(
                    'column' => 'post_date',
                    'after'  => $today->format( 'Y-m-d H:i:s' ),
                    'before' => $today_end->format( 'Y-m-d H:i:s' ),
                    'inclusive' => true,
                ),
            ),
        ) );

        $count   = count( $post_ids );
        $missing = max( 0, $target - $count );

        if ( $count >= $target ) {
            $status = 'complete';
        } elseif ( $count > 0 ) {
            $status = 'partial';
        } else {
            $status = 'empty';
        }

        return array(
            'target'     => $target,
            'count'      => $count,
            'missing'    => $missing,
            'status'     => $status,
            'post_ids'   => array_map( 'intval', $post_ids ),
            'today_date' => $today->format( 'Y-m-d' ),
        );
    }
}

/**
 * Rend le markup de la notification flottante (HTML + CSS injecté dans admin_footer).
 *
 * IMPORTANT: ne produit AUCUN markup si le quota est atteint (status === 'complete').
 * La notification n'existe que lorsqu'il manque des articles.
 */
function clm_daily_quota_floating_notice_render() {
    if ( ! current_user_can( 'edit_posts' ) ) {
        return;
    }

    // Applique le filtre pour permettre de surcharger la cible (ex: 3 articles/jour).
    $target = (int) apply_filters( 'clm_daily_quota_target', 5 );
    $data   = clm_daily_quota_get_status( $target );

    // V29: ne rien afficher si le quota est atteint — la notif ne sert
    // qu'à alerter sur un problème, pas à féliciter.
    if ( 'complete' === $data['status'] ) {
        return;
    }

    $calendar_url = add_query_arg(
        array( 'page' => 'scheduled-posts-calendar' ),
        admin_url( 'admin.php' )
    );

    $count   = $data['count'];
    $missing = $data['missing'];
    $status  = $data['status']; // 'partial' ou 'empty'

    if ( 'partial' === $status ) {
        $icon        = 'dashicons-warning';
        $badge_class = 'clm-quota-partial';
        $title       = sprintf(
            /* translators: 1: manquants, 2: prévus, 3: cible. */
            __( 'Manque %1$d article(s) — %2$d/%3$d prévus', 'clm-daily-quota' ),
            $missing,
            $count,
            $target
        );
        $message = sprintf(
            _n(
                'Il reste %d article à planifier aujourd\'hui.',
                'Il reste %d articles à planifier aujourd\'hui.',
                $missing,
                'clm-daily-quota'
            ),
            $missing
        );
    } else {
        $icon        = 'dashicons-flag';
        $badge_class = 'clm-quota-empty';
        /* translators: %d: cible d'articles par jour. */
        $title   = sprintf( __( 'Aucun article prévu (%d attendus)', 'clm-daily-quota' ), $target );
        $message = __( "Aucun article planifié pour aujourd'hui.", 'clm-daily-quota' );
    }

    // Échappements sûrs.
    $title       = esc_html( $title );
    $message     = esc_html( $message );
    $calendar_url = esc_url( $calendar_url );
    $badge_class = esc_attr( $badge_class );
    $icon        = esc_attr( $icon );
    $count_safe  = (int) $count;
    $target_safe = (int) $target;
    ?>
    <div id="clm-daily-quota-notice"
         class="clm-quota-notice <?php echo $badge_class; ?>"
         role="status"
         aria-live="polite"
         data-status="<?php echo esc_attr( $status ); ?>"
         data-count="<?php echo $count_safe; ?>"
         data-target="<?php echo $target_safe; ?>"
         data-missing="<?php echo (int) $missing; ?>">
        <div class="clm-quota-icon"><span class="dashicons <?php echo $icon; ?>"></span></div>
        <div class="clm-quota-body">
            <div class="clm-quota-title"><?php echo $title; ?></div>
            <div class="clm-quota-message"><?php echo $message; ?></div>
            <div class="clm-quota-progress" aria-hidden="true">
                <div class="clm-quota-progress-bar" style="width:<?php echo min( 100, round( ( $count_safe / max( 1, $target_safe ) ) * 100 ) ); ?>%"></div>
            </div>
            <a class="clm-quota-link" href="<?php echo $calendar_url; ?>">
                <span class="dashicons dashicons-calendar-alt"></span>
                <?php esc_html_e( 'Ouvrir le calendrier', 'clm-daily-quota' ); ?>
            </a>
        </div>
        <button type="button" class="clm-quota-toggle" aria-label="<?php esc_attr_e( 'Réduire / agrandir la notification', 'clm-daily-quota' ); ?>">
            <span class="dashicons dashicons-minus"></span>
        </button>
    </div>
    <?php
}

/**
 * CSS + JS de la notification flottante (une seule fois par page admin).
 */
function clm_daily_quota_floating_notice_assets() {
    if ( ! current_user_can( 'edit_posts' ) ) {
        return;
    }
    ?>
    <style id="clm-daily-quota-notice-css">
        #clm-daily-quota-notice {
            position: fixed;
            top: 40px;
            right: 20px;
            z-index: 99999;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            min-width: 280px;
            max-width: 340px;
            padding: 12px 14px;
            border-radius: 10px;
            background: #fff;
            color: #1d2327;
            box-shadow: 0 8px 24px rgba(0,0,0,0.18), 0 2px 6px rgba(0,0,0,0.08);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            font-size: 13px;
            line-height: 1.4;
            border-left: 4px solid #2271b1;
            transition: transform .25s ease, opacity .25s ease;
        }
        #clm-daily-quota-notice.clm-quota-complete { border-left-color: #00a32a; }
        #clm-daily-quota-notice.clm-quota-partial { border-left-color: #dba617; }
        #clm-daily-quota-notice.clm-quota-empty   { border-left-color: #d63638; }

        /* Sur mobile, on colle sous la barre d'admin */
        @media (max-width: 782px) {
            #clm-daily-quota-notice {
                top: 56px;
                right: 8px;
                left: 8px;
                max-width: none;
                min-width: 0;
            }
        }

        #clm-daily-quota-notice.clm-quota-notice-collapsed {
            transform: translateX(calc(100% + 30px));
            opacity: 0;
            pointer-events: none;
        }

        #clm-daily-quota-notice .clm-quota-icon {
            flex: 0 0 auto;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f0f0f1;
        }
        #clm-daily-quota-notice.clm-quota-complete .clm-quota-icon { background: #e3f6e5; color: #00a32a; }
        #clm-daily-quota-notice.clm-quota-partial .clm-quota-icon { background: #fdf3d7; color: #b8860b; }
        #clm-daily-quota-notice.clm-quota-empty   .clm-quota-icon { background: #fce5e5; color: #d63638; }
        #clm-daily-quota-notice .clm-quota-icon .dashicons { font-size: 18px; width: 18px; height: 18px; }

        #clm-daily-quota-notice .clm-quota-body { flex: 1 1 auto; min-width: 0; }

        #clm-daily-quota-notice .clm-quota-title {
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 2px;
        }
        #clm-daily-quota-notice .clm-quota-message {
            color: #50575e;
            font-size: 12px;
            margin-bottom: 8px;
        }

        #clm-daily-quota-notice .clm-quota-progress {
            position: relative;
            height: 6px;
            background: #f0f0f1;
            border-radius: 999px;
            overflow: hidden;
            margin-bottom: 8px;
        }
        #clm-daily-quota-notice .clm-quota-progress-bar {
            position: absolute;
            inset: 0 auto 0 0;
            background: linear-gradient(90deg, #2271b1, #3582c4);
            transition: width .4s ease;
        }
        #clm-daily-quota-notice.clm-quota-complete .clm-quota-progress-bar { background: linear-gradient(90deg, #00a32a, #46b450); }
        #clm-daily-quota-notice.clm-quota-partial  .clm-quota-progress-bar { background: linear-gradient(90deg, #dba617, #f0c33c); }
        #clm-daily-quota-notice.clm-quota-empty    .clm-quota-progress-bar { background: linear-gradient(90deg, #d63638, #f86368); }

        #clm-daily-quota-notice .clm-quota-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
            font-weight: 600;
            font-size: 12px;
            color: #2271b1;
            padding: 4px 8px;
            border-radius: 5px;
            background: #f0f6fc;
            transition: background .15s ease, color .15s ease;
        }
        #clm-daily-quota-notice .clm-quota-link:hover {
            background: #2271b1;
            color: #fff;
        }
        #clm-daily-quota-notice .clm-quota-link .dashicons {
            font-size: 14px;
            width: 14px;
            height: 14px;
        }

        #clm-daily-quota-notice .clm-quota-toggle {
            flex: 0 0 auto;
            border: none;
            background: transparent;
            cursor: pointer;
            color: #50575e;
            padding: 2px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #clm-daily-quota-notice .clm-quota-toggle:hover { background: #f0f0f1; color: #1d2327; }
        #clm-daily-quota-notice .clm-quota-toggle .dashicons { font-size: 16px; width: 16px; height: 16px; }

        /* Pastille relançable quand la notif est repliée */
        #clm-daily-quota-notice.clm-quota-notice-collapsed::after {
            content: "";
        }
        #clm-quota-collapsed-tab {
            position: fixed;
            top: 40px;
            right: 20px;
            z-index: 99998;
            display: none;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #2271b1;
            color: #fff;
            cursor: pointer;
            box-shadow: 0 6px 16px rgba(0,0,0,0.2);
            transition: transform .2s ease, background .2s ease;
        }
        #clm-quota-collapsed-tab:hover { transform: scale(1.08); }
        #clm-quota-collapsed-tab.clm-quota-partial  { background: #dba617; }
        #clm-quota-collapsed-tab.clm-quota-empty    { background: #d63638; }
        #clm-quota-collapsed-tab .dashicons { font-size: 20px; width: 20px; height: 20px; }
        #clm-quota-collapsed-tab .clm-quota-tab-count {
            position: absolute;
            top: -4px;
            right: -4px;
            min-width: 18px;
            height: 18px;
            padding: 0 4px;
            border-radius: 9px;
            background: #1d2327;
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            line-height: 18px;
            text-align: center;
        }

        @media (max-width: 782px) {
            #clm-quota-collapsed-tab { top: 56px; right: 8px; }
        }

        /* Animation pulse pour attirer l'attention si le quota n'est pas atteint */
        @keyframes clmQuotaPulse {
            0%, 100% { box-shadow: 0 8px 24px rgba(0,0,0,0.18), 0 0 0 0 rgba(219, 166, 23, 0.55); }
            50%      { box-shadow: 0 8px 24px rgba(0,0,0,0.18), 0 0 0 10px rgba(219, 166, 23, 0); }
        }
        @keyframes clmQuotaPulseRed {
            0%, 100% { box-shadow: 0 8px 24px rgba(0,0,0,0.18), 0 0 0 0 rgba(214, 54, 56, 0.55); }
            50%      { box-shadow: 0 8px 24px rgba(0,0,0,0.18), 0 0 0 10px rgba(214, 54, 56, 0); }
        }
        #clm-daily-quota-notice.clm-quota-partial { animation: clmQuotaPulse 2.4s ease-out infinite; }
        #clm-daily-quota-notice.clm-quota-empty   { animation: clmQuotaPulseRed 2.4s ease-out infinite; }
        #clm-daily-quota-notice:hover { animation-play-state: paused; }
    </style>
    <script id="clm-daily-quota-notice-js">
    (function () {
        if (window.__clmDailyQuotaInit) return;
        window.__clmDailyQuotaInit = true;

        var STORAGE_KEY = 'clm_quota_collapsed_v29';

        function init() {
            var notice = document.getElementById('clm-daily-quota-notice');
            if (!notice) return;

            // Crée la pastille repliée.
            var tab = document.createElement('div');
            tab.id = 'clm-quota-collapsed-tab';
            tab.setAttribute('role', 'button');
            tab.setAttribute('tabindex', '0');
            tab.setAttribute('aria-label', 'Afficher la notification du jour');
            var statusClass = notice.className.match(/clm-quota-(complete|partial|empty)/);
            if (statusClass) { tab.classList.add(statusClass[0]); }
            var icon = notice.querySelector('.clm-quota-icon .dashicons');
            if (icon) {
                var iconClone = document.createElement('span');
                iconClone.className = icon.className;
                tab.appendChild(iconClone);
            }
            var missing = parseInt(notice.getAttribute('data-missing'), 10) || 0;
            var count = parseInt(notice.getAttribute('data-count'), 10) || 0;
            if (missing > 0) {
                var badge = document.createElement('span');
                badge.className = 'clm-quota-tab-count';
                badge.textContent = String(missing);
                tab.appendChild(badge);
            } else if (count > 0) {
                var okBadge = document.createElement('span');
                okBadge.className = 'clm-quota-tab-count';
                okBadge.textContent = '✓';
                tab.appendChild(okBadge);
            }
            document.body.appendChild(tab);

            // Restaure l'état replié (persistant par navigateur).
            try {
                var stored = window.localStorage ? localStorage.getItem(STORAGE_KEY) : null;
                if (stored === '1') {
                    collapse(notice, tab);
                }
            } catch (e) {}

            // Bouton "réduire".
            var toggleBtn = notice.querySelector('.clm-quota-toggle');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function () {
                    if (notice.classList.contains('clm-quota-notice-collapsed')) {
                        expand(notice, tab);
                    } else {
                        collapse(notice, tab);
                    }
                });
            }

            // Clic sur la pastille pour ré-ouvrir.
            tab.addEventListener('click', function () { expand(notice, tab); });
            tab.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    expand(notice, tab);
                }
            });
        }

        function collapse(notice, tab) {
            notice.classList.add('clm-quota-notice-collapsed');
            tab.style.display = 'flex';
            try { if (window.localStorage) localStorage.setItem(STORAGE_KEY, '1'); } catch (e) {}
        }
        function expand(notice, tab) {
            notice.classList.remove('clm-quota-notice-collapsed');
            tab.style.display = 'none';
            try { if (window.localStorage) localStorage.setItem(STORAGE_KEY, '0'); } catch (e) {}
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
    </script>
    <?php
}

// Hook la notification sur TOUTES les pages admin (footer pour avoir le markup final).
add_action( 'admin_footer', 'clm_daily_quota_floating_notice_render', 20 );
add_action( 'admin_head', 'clm_daily_quota_floating_notice_assets' );

// Endpoint AJAX pour rafraîchir le compteur sans recharger la page (utile après
// création/planification d'un article via Gutenberg ou Quick Draft).
add_action( 'wp_ajax_clm_daily_quota_refresh', function () {
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( array( 'message' => 'Permissions insuffisantes.' ), 403 );
    }
    check_ajax_referer( 'clm_daily_quota_refresh', 'nonce' );

    $target = (int) apply_filters( 'clm_daily_quota_target', 5 );
    $data   = clm_daily_quota_get_status( $target );

    wp_send_json_success( array(
        'target'      => (int) $data['target'],
        'count'       => (int) $data['count'],
        'missing'     => (int) $data['missing'],
        'status'      => $data['status'],
        'today_date'  => $data['today_date'],
        'post_ids'    => $data['post_ids'],
        'calendar_url' => add_query_arg(
            array( 'page' => 'scheduled-posts-calendar' ),
            admin_url( 'admin.php' )
        ),
    ) );
} );

// Localise le nonce pour le JS rafraîchissant (hooké en bout de fichier une fois
// admin_url disponible).
add_action( 'admin_footer', function () {
    if ( ! current_user_can( 'edit_posts' ) ) return;
    ?>
    <script>
    window.CLM_DAILY_QUOTA = window.CLM_DAILY_QUOTA || {};
    window.CLM_DAILY_QUOTA.nonce = <?php echo wp_json_encode( wp_create_nonce( 'clm_daily_quota_refresh' ) ); ?>;
    window.CLM_DAILY_QUOTA.ajaxUrl = <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>;
    window.CLM_DAILY_QUOTA.refreshInterval = 60000; // 60 secondes.
    </script>
    <?php
}, 21 );

// Auto-refresh léger : toutes les 60s, on interroge l'AJAX pour mettre à jour
// la notification sans recharger la page.
add_action( 'admin_footer', function () {
    if ( ! current_user_can( 'edit_posts' ) ) return;
    ?>
    <script>
    (function () {
        if (window.__clmQuotaRefreshInit) return;
        window.__clmQuotaRefreshInit = true;

        function refresh() {
            if (!window.CLM_DAILY_QUOTA || !window.CLM_DAILY_QUOTA.nonce) return;
            var data = new URLSearchParams({
                action: 'clm_daily_quota_refresh',
                nonce: window.CLM_DAILY_QUOTA.nonce
            });
            fetch(window.CLM_DAILY_QUOTA.ajaxUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: data.toString()
            })
            .then(function (r) { return r.json(); })
            .then(function (res) {
                if (!res || !res.success) return;
                var payload = res.data;
                if (!payload) return;
                applyUpdate(payload);
            })
            .catch(function () {});
        }

        function applyUpdate(payload) {
            var notice = document.getElementById('clm-daily-quota-notice');
            var status = payload.status || 'empty';

            // V29: si le quota est atteint, on retire complètement la notif.
            if (status === 'complete') {
                if (notice) { notice.remove(); }
                var tab = document.getElementById('clm-quota-collapsed-tab');
                if (tab) { tab.remove(); }
                return;
            }

            // Si la notif n'existait pas (page chargée alors que complete, et
            // maintenant le statut a changé), on recharge pour la faire apparaître.
            if (!notice) {
                window.location.reload();
                return;
            }

            var count = parseInt(payload.count, 10) || 0;
            var target = parseInt(payload.target, 10) || 5;
            var missing = parseInt(payload.missing, 10) || 0;

            // Mise à jour des attributs data.
            notice.setAttribute('data-count', String(count));
            notice.setAttribute('data-target', String(target));
            notice.setAttribute('data-missing', String(missing));
            notice.setAttribute('data-status', status);

            // Recalcule les classes de statut.
            notice.classList.remove('clm-quota-complete', 'clm-quota-partial', 'clm-quota-empty');
            notice.classList.add('clm-quota-' + status);

            // Met à jour les textes.
            var titleEl = notice.querySelector('.clm-quota-title');
            var msgEl = notice.querySelector('.clm-quota-message');
            var barEl = notice.querySelector('.clm-quota-progress-bar');
            var iconEl = notice.querySelector('.clm-quota-icon .dashicons');

            if (titleEl) {
                if (status === 'partial') {
                    titleEl.textContent = 'Manque ' + missing + ' article(s) — ' + count + '/' + target + ' prévus';
                } else {
                    titleEl.textContent = 'Aucun article prévu (' + target + ' attendus)';
                }
            }
            if (msgEl) {
                if (status === 'partial') {
                    msgEl.textContent = missing === 1
                        ? 'Il reste 1 article à planifier aujourd\'hui.'
                        : 'Il reste ' + missing + ' articles à planifier aujourd\'hui.';
                } else {
                    msgEl.textContent = 'Aucun article planifié pour aujourd\'hui.';
                }
            }
            if (iconEl) {
                iconEl.className = status === 'partial'
                    ? 'dashicons dashicons-warning'
                    : 'dashicons dashicons-flag';
            }
            if (barEl) {
                var pct = Math.min(100, Math.round((count / Math.max(1, target)) * 100));
                barEl.style.width = pct + '%';
            }

            // Met à jour la pastille repliée.
            var tab = document.getElementById('clm-quota-collapsed-tab');
            if (tab) {
                tab.classList.remove('clm-quota-complete', 'clm-quota-partial', 'clm-quota-empty');
                tab.classList.add('clm-quota-' + status);
                var tabIcon = tab.querySelector('.dashicons');
                if (tabIcon) {
                    tabIcon.className = status === 'partial'
                        ? 'dashicons dashicons-warning'
                        : 'dashicons dashicons-flag';
                }
                var badge = tab.querySelector('.clm-quota-tab-count');
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'clm-quota-tab-count';
                    tab.appendChild(badge);
                }
                badge.textContent = missing > 0 ? String(missing) : '0';
            }
        }

        var interval = (window.CLM_DAILY_QUOTA && window.CLM_DAILY_QUOTA.refreshInterval) || 60000;
        setInterval(refresh, interval);

        // Rafraîchit aussi quand l'utilisateur revient sur l'onglet.
        document.addEventListener('visibilitychange', function () {
            if (!document.hidden) refresh();
        });
    })();
    </script>
    <?php
}, 22 );
