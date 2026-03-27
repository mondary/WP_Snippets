
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: A TRIER
 * Source path: A TRIER/WP_PAGE_contactfull/page-fullwidth-contact.php
 * Display name: page-fullwidth-contact
 * Scope: global
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: page-fullwidth-contact (1 variantes)
 * Version: v1
 * Recommended latest in family: A TRIER/WP_PAGE_contactfull/page-fullwidth-contact.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: head-injection, footer-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 51 / 1962
 * Hash code normalise (sha256): 2b61e13ce7d2ab32e670bfee59d34e07c00c93dd7d9c3b2c06825dbb9c1cd440
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: LOCAL__global__page-fullwidth-contact__v1__src-a-trier.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/LOCAL__global__page-fullwidth-contact__v1__src-a-trier.php
 * Bucket FINAL: canonical
 * Statut: LOCAL
 * Cluster principal: misc_utilities
 * Clusters secondaires: aucun
 * Domaine: global
 * Confiance: low
 * Scores (top): misc_utilities=1
 * Raisons principales: fallback
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

l
 * Source root: A TRIER
 * Source path: A TRIER/WP_PAGE_contactfull/page-fullwidth-contact.php
 * Display name: page-fullwidth-contact
 * Scope: global
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: page-fullwidth-contact (1 variantes)
 * Version: v1
 * Recommended latest in family: A TRIER/WP_PAGE_contactfull/page-fullwidth-contact.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: head-injection, footer-injection
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 51 / 1962
 * Hash code normalise (sha256): 2b61e13ce7d2ab32e670bfee59d34e07c00c93dd7d9c3b2c06825dbb9c1cd440
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/**
 * Template Name: Full Width Contact Page
 * Description: A custom full-width page template for the contact page
 */

get_header('empty'); // This will load a clean header without theme elements
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); ?></title>
    <?php wp_head(); ?>
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/styles.css">
</head>
<body <?php body_class(); ?>>
    <div class="container">
        <header>
            <div class="logo">
                <div class="circle"></div>
            </div>
        </header>
        <main>
            <div class="hero-section">
                <div class="hero-image">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/city-view.jpg" alt="Peaceful city view through an arch">
                </div>
                <div class="content">
                    <h1>Sublimez votre présence digitale</h1>
                    <p>Moi, Clément Mondary, je transforme vos idées en expériences digitales captivantes. Expert en design numérique, je crée des solutions web qui font rayonner votre marque.</p>
                    <button class="cta-button">Concrétisons votre vision</button>
                </div>
            </div>
            <div class="tagline">
                <h2>L'innovation digitale à votre service</h2>
                <div class="services">
                    <span>web</span>
                    <span class="plus">+</span>
                    <span>produit</span>
                    <span class="plus">+</span>
                    <span>marque</span>
                </div>
                <div class="company">mondary.design</div>
            </div>
        </main>
    </div>
    <?php wp_footer(); ?>
</body>
</html>