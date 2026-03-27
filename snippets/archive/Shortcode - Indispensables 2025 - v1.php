
/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: WP_Snippets
 * Source path: WP_Snippets/WP_ShortCode_indispensables.php
 * Display name: WP_ShortCode_indispensables
 * Scope: global
 * Online snippet: non
 * Exact duplicate group: oui (9c103c65e647…, 2 membres)
 * Canonical exact group ID: 100
 * Version family: DUP WP_ShortCode_indispensables <2025 (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets/WP_ShortCode_indispensables.php
 * Is family latest: oui
 * Archive reasons: exact-duplicate
 * Features: shortcode, head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head
 * Fonctions clefs: add_masonry_styles, generate_indispensables_shortcode
 * Shortcodes: indispensables
 * Lignes / octets (brut): 111 / 2984
 * Hash code normalise (sha256): 9c103c65e64743435f2dd9d84f5cd130148bd719f1a99a6201ab8330cf1aef5f
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: shortcode-indispensables__v001.php
 * Path: WP_Snippets_FINAL_CLEAN/archive/shortcode-indispensables__v001.php
 * Bucket FINAL: archive
 * Statut: LOCAL
 * Cluster principal: shortcode_preview
 * Clusters secondaires: aucun
 * Domaine: shortcode
 * Confiance: low
 * Scores (top): shortcode_preview=5, shortcode_other=2
 * Raisons principales: shortcode
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

/* FINAL-CANONICAL-META
 * Role final: archive
 * Source root: WP_Snippets
 * Source path: WP_Snippets/WP_ShortCode_indispensables.php
 * Display name: WP_ShortCode_indispensables
 * Scope: global
 * Online snippet: non
 * Exact duplicate group: oui (9c103c65e647…, 2 membres)
 * Canonical exact group ID: 100
 * Version family: DUP WP_ShortCode_indispensables <2025 (1 variantes)
 * Version: v1
 * Recommended latest in family: WP_Snippets/WP_ShortCode_indispensables.php
 * Is family latest: oui
 * Archive reasons: exact-duplicate
 * Features: shortcode, head-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: wp_head
 * Fonctions clefs: add_masonry_styles, generate_indispensables_shortcode
 * Shortcodes: indispensables
 * Lignes / octets (brut): 111 / 2984
 * Hash code normalise (sha256): 9c103c65e64743435f2dd9d84f5cd130148bd719f1a99a6201ab8330cf1aef5f
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/**
 * Page template pour afficher les articles avec la catégorie 'indispensables' pour les années de 2017 à 2025
 */

// Ajouter les styles CSS dans l'en-tête
function add_masonry_styles() {
    echo '<style>
    .masonry-container {
        columns: 5 250px;
        column-gap: 1.5rem;
        padding: 1.5rem;
        margin: 0 auto;
        max-width: 1800px;
    }
    .masonry-item {
        display: block;
        text-decoration: none;
        break-inside: avoid;
        margin-bottom: 1.5rem;
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .masonry-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    .masonry-content {
        position: relative;
        width: 100%;
    }
    .masonry-image {
        width: 100%;
        display: block;
        position: relative;
    }
    .masonry-image img {
        width: 100%;
        height: auto;
        display: block;
        object-fit: cover;
    }
    .masonry-details {
        padding: 1.2rem;
        background: linear-gradient(180deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,1) 100%);
    }
    .masonry-item h2 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1a1a1a;
        margin: 0 0 0.8rem;
        line-height: 1.3;
    }

    @media (max-width: 768px) {
        .masonry-container {
            columns: 2 200px;
            padding: 1rem;
        }
    }
    @media (max-width: 480px) {
        .masonry-container {
            columns: 1;
        }
    }
    </style>';
}
add_action('wp_head', 'add_masonry_styles');

function generate_indispensables_shortcode($year) {
    $args = array(
        'category_name' => 'indispensables',
        'tag' => $year,
        'posts_per_page' => -1 // Afficher tous les articles
    );

    $query = new WP_Query($args);
    $output = '<div class="masonry-container">';

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $output .= '<a href="' . get_permalink() . '" class="masonry-item">
                <div class="masonry-content">
                    <div class="masonry-image">' . get_the_post_thumbnail(null, 'large') . '</div>
                    <div class="masonry-details">
                        <h2>' . get_the_title() . '</h2>
                    </div>
                </div>
            </a>';
        }
    } else {
        $output = '<p>Aucun article trouvé pour ' . $year . '.</p>';
    }

    wp_reset_postdata();
    return $output;
}

// Générer les shortcodes pour chaque année de 2017 à 2025
$years = range(2017, 2025);
foreach ($years as $year) {
    add_shortcode('indispensables' . $year, function() use ($year) {
        return generate_indispensables_shortcode($year);
    });
}
?>