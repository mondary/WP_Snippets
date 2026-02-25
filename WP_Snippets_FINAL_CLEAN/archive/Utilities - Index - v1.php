
/* FINAL-CANONICAL-META
 * Role final: canonical
 * Source root: A TRIER
 * Source path: A TRIER/WP_POST newsletter template/index.php
 * Display name: index
 * Scope: front-end
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: index (1 variantes)
 * Version: v1
 * Recommended latest in family: A TRIER/WP_POST newsletter template/index.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 144 / 4290
 * Hash code normalise (sha256): 9c3d7008681bba1739852b24def66e7c7974314e427dfac18bc8be4bbbf6ad98
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

/* CLM-FEATURE-CLASSIFICATION:START
 * Fichier: LOCAL__front-end__index__v1__src-a-trier.php
 * Path: WP_Snippets_FINAL_CLEAN/canonical/LOCAL__front-end__index__v1__src-a-trier.php
 * Bucket FINAL: canonical
 * Statut: LOCAL
 * Cluster principal: misc_utilities
 * Clusters secondaires: aucun
 * Domaine: post-front
 * Confiance: low
 * Scores (top): misc_utilities=1
 * Raisons principales: fallback
 * Classification generee le (UTC): 2026-02-24T16:44:28+00:00
 * CLM-FEATURE-CLASSIFICATION:END */

* Role final: canonical
 * Source root: A TRIER
 * Source path: A TRIER/WP_POST newsletter template/index.php
 * Display name: index
 * Scope: front-end
 * Online snippet: non
 * Exact duplicate group: non
 * Version family: index (1 variantes)
 * Version: v1
 * Recommended latest in family: A TRIER/WP_POST newsletter template/index.php
 * Is family latest: oui
 * Canonical reasons: unique-code
 * Features: snippet-php
 * Dependances probables: WordPress core hooks
 * Hooks WP: aucun
 * Fonctions clefs: aucun
 * Lignes / octets (brut): 144 / 4290
 * Hash code normalise (sha256): 9c3d7008681bba1739852b24def66e7c7974314e427dfac18bc8be4bbbf6ad98
 * Genere le (UTC): 2026-02-24T16:05:10+00:00
 */

// index.php

header("Content-Type: text/html; charset=UTF-8");

// Récupérer le flux RSS
$rss = simplexml_load_file('https://mondary.design/feed/');
if ($rss === false) {
    echo "Erreur lors du chargement du flux RSS.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil de Clement MONDARY</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }
        .avatar img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-left: auto; /* Aligne l'image à droite */
        }
        .header-info {
            flex: 1; /* Permet au texte de prendre l'espace restant */
        }
        .header-info h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold; /* Titre en gras */
            color: black; /* Titre en noir */
        }
        .header-info p {
            color: #666;
            margin: 5px 0 0;
        }
        .header-stats {
            display: flex;
            gap: 20px;
            margin-left: auto;
        }
        .stat-number {
            font-size: 18px;
            font-weight: bold;
        }
        .stat-label {
            font-size: 14px;
            color: #666;
        }
        .post {
            background: #fff;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .post h3 {
            margin: 0 0 10px;
            font-size: 18px;
        }
        .post h3 a {
            text-decoration: none; /* Pas de soulignement */
            color: black; /* Couleur du texte */
            font-weight: bold; /* Titre en gras */
        }
        .post h3 a:hover {
            color: darkblue; /* Couleur au survol */
        }
        .post-meta {
            font-size: 14px;
            color: #666;
        }
        .post-content {
            font-size: 16px;
            margin: 10px 0;
        }
        .post img {
            width: 100%;
            height: auto;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header">
            <div class="header-info">
                <h1>Clement MONDARY</h1>
                <p>Chercheur de pépites sur le net</p>
                <div class="header-stats">
                    <div>
                        <div class="stat-number">2.2k</div>
                        <div class="stat-label">Posts</div>
                    </div>
                    <div>
                        <div class="stat-number">449</div>
                        <div class="stat-label">Subscribers</div>
                    </div>
                </div>
            </div>
            <div class="avatar">
                <img src="https://i0.wp.com/mondary.design/wp-content/uploads/2020/10/cropped-cropped-clem_icon-copie-1-1.png?w=356&ssl=1" alt="Profile" />
            </div>
        </div>

        <!-- Affichage des articles du flux RSS -->
        <?php foreach ($rss->channel->item as $item): ?>
            <div class="post">
                <h3><a href="<?php echo $item->link; ?>" target="_blank"><?php echo $item->title; ?></a></h3>
                <div class="post-meta">Publié le : <?php echo date('d M Y', strtotime($item->pubDate)); ?></div>
                <p class="post-content"><?php echo $item->description; ?></p>
                <?php if (!empty($item->image)): ?>
                    <img src="<?php echo $item->image; ?>" alt="Post image" />
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>