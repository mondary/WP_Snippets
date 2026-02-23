<?php

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

        .post-actions a {
            text-decoration: none;
            color: #666;
            margin-left: 8px;
            font-size: 14px;
        }

        .post-actions a:hover {
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

        .post-time {
            font-size: 11px;
            color: #666;
        }

        .post-actions {
            display: flex;
            gap: 8px;
        }

        .post-actions a {
            text-decoration: none;
            color: #666;
            padding: 2px;
            border-radius: 3px;
            transition: all 0.2s ease;
        }

        .post-actions a:hover {
            color: #2271b1;
        }

        .dashicons-visibility.dashicons {
            position: relative !important;
            top: 0 !important;
            left: 0 !important;
            transform: none !important;
            transition: none !important;
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
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        @media (min-width: 1800px) {
            .calendar-months-container {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 18px;
                align-items: start;
            }

            .calendar-month-section {
                margin: 0;
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
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
                padding-top: 4px;
            }

            .post-time {
                font-size: 10px;
            }

            .post-actions {
                gap: 4px;
                flex-wrap: wrap;
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

        function applySearchFilter() {
            const searchTerm = (searchInput.value || '').toLowerCase();
            const postItems = document.querySelectorAll('.post-item');

            postItems.forEach(item => {
                const title = item.textContent.toLowerCase();
                item.style.display = title.includes(searchTerm) ? '' : 'none';
            });
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

            return Promise.all([
                fetch(`<?php echo esc_url(rest_url('wp/v2/posts')); ?>?per_page=100&status=publish,future&after=${after}&before=${before}&orderby=date&order=asc`, {
                    headers: {
                        'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                    }
                }).then(response => response.json()),
                fetch(`<?php echo esc_url(rest_url('wp/v2/posts')); ?>?per_page=100&status=draft&after=${after}&before=${before}&orderby=date&order=asc`, {
                    headers: {
                        'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                    }
                }).then(response => response.json())
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

            return fetch(`<?php echo esc_url(rest_url('wp/v2/posts')); ?>?per_page=100&status=publish&after=${yearStart}&before=${yearEnd}&orderby=date&order=desc`, {
                headers: {
                    'X-WP-Nonce': '<?php echo wp_create_nonce('wp_rest'); ?>'
                }
            })
            .then(response => {
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

        function renderMonthSection(firstDay, lastDay, posts) {
            const section = document.createElement('section');
            section.className = 'calendar-month-section';

            const title = document.createElement('h2');
            title.className = 'calendar-month-title';
            title.textContent = `${monthLabels[firstDay.getMonth()]} ${firstDay.getFullYear()}`;

            const grid = document.createElement('div');
            grid.className = 'calendar-grid';

            section.appendChild(title);
            section.appendChild(grid);
            calendarMonthsContainer.appendChild(section);

            generateCalendarGrid(grid, firstDay, lastDay, posts);
        }

        function refreshCurrentView() {
            const monthsToRender = (visibleMonths.length ? visibleMonths : [normalizeMonth(currentDate)])
                .map(normalizeMonth)
                .sort((a, b) => a - b);

            visibleMonths = monthsToRender;
            calendarMonthsContainer.innerHTML = '';

            Promise.all([
                Promise.all(monthsToRender.map(fetchMonthPosts)),
                fetchYearStats(currentDate)
            ])
            .then(([monthResults, yearStats]) => {
                monthResults.forEach(({ firstDay, lastDay, posts }) => {
                    renderMonthSection(firstDay, lastDay, posts);
                });

                const selectedMonthResult = monthResults.find(({ firstDay }) => getMonthKey(firstDay) === getMonthKey(currentDate));
                const selectedMonthCount = selectedMonthResult ? selectedMonthResult.posts.length : 0;
                updateMonthlyStats(yearStats.yearlyTotal, selectedMonthCount, yearStats.avgPostsPerMonth);
                applySearchFilter();
                focusCalendarCellIfNeeded();
            })
            .catch(error => {
                calendarMonthsContainer.innerHTML = '';
                console.error('Erreur lors de la récupération des articles:', error);
            });
        }

        function updateCalendar(date) {
            currentViewMode = 'single';
            pendingCenterToday = false;
            visibleMonths = [normalizeMonth(date)];
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

                    postDiv.innerHTML = `
                        <div class="post-title">${postTitle}</div>
                        <div class="post-footer">
                            <span class="post-time">${postTime}</span>
                            <div class="post-actions">
                                <a href="${post.link}" target="_blank" title="Voir l'article">
                                    <span class="dashicons dashicons-visibility"></span>
                                </a>
                                <a href="<?php echo admin_url('post.php'); ?>?post=${post.id}&action=edit" title="Modifier l'article">
                                    <span class="dashicons dashicons-edit"></span>
                                </a>
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

            const targetElement = event.currentTarget && event.currentTarget.getAttribute('data-date')
                ? event.currentTarget
                : (event.target && event.target.closest ? event.target.closest('[data-date]') : null);

            const newDate = targetElement ? targetElement.getAttribute('data-date') : null;
            const monthOffset = targetElement ? parseInt(targetElement.getAttribute('data-month-offset') || '0', 10) : 0;
            
            if (newDate) {
                updatePostDate(postId, newDate, monthOffset);
            } else {
                console.error('Impossible de trouver une date valide pour le drop');
            }
        }

        function drag(event) {
            const draggable = event.currentTarget && event.currentTarget.getAttribute('data-post-id')
                ? event.currentTarget
                : (event.target && event.target.closest ? event.target.closest('.post-item[data-post-id]') : null);

            if (!draggable) {
                return;
            }
            event.dataTransfer.setData("text", draggable.getAttribute('data-post-id'));
        }

        function updatePostDate(postId, newDate, monthOffset = 0) {
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
            const dateWithTime = new Date(year, month - 1, day, 14, 0, 0);
            const targetMonthDate = new Date(year, month - 1, 1);

            if (isNaN(dateWithTime.getTime())) {
                console.error('Date invalide après conversion:', newDate);
                return;
            }

            const payloadDate = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}T14:00:00`;

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
            .then(data => {
                console.log('Date mise à jour avec succès:', data);
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

        // Initialisation du calendrier
        if (initialView === 'month') {
            updateCalendar(currentDate);
        } else {
            showFullYearView(currentDate.getFullYear());
        }
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
?>
