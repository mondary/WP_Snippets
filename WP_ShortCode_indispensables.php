<?php
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