# Snippets PHP pour WordPress

Ce document décrit plusieurs snippets PHP conçus pour améliorer les fonctionnalités de WordPress.  Chaque snippet est indépendant et peut être utilisé séparément.  Ils sont organisés pour une meilleure lisibilité et compréhension.

## Table des matières

* [Ajout d'un bouton "Dupliquer" aux articles](#ajout-dun-bouton-dupliquer-aux-articles)
* [Redimensionnement des colonnes dans l'administration](#redimensionnement-des-colonnes-dans-ladministration)
* [Calendrier de planification des articles](#calendrier-de-planification-des-articles)
    * [Fonctionnalités](#fonctionnalites-du-calendrier)
    * [Installation et Configuration](#installation-et-configuration-du-calendrier)
* [Ajout automatique de tags existants](#ajout-automatique-de-tags-existants)
    * [Options de Configuration](#options-de-configuration-pour-la-fonction-aet_tagging)
* [Heure de publication par défaut à 14h00](#heure-de-publication-par-defaut-a-14h00)
* [Conversion des hashtags en liens](#conversion-des-hashtags-en-liens)


## Ajout d'un bouton "Dupliquer" aux articles

Ce snippet ajoute un bouton "Dupliquer" aux actions de chaque article dans la liste des articles de l'administration WordPress.  Il permet de créer une copie d'un article existant rapidement et facilement.

**Fichier :** `WP_ADMIN - Duplicate post.php`

**Fonctionnement :**  Le snippet utilise les filtres `post_row_actions` et `page_row_actions` pour ajouter un lien "Dupliquer" à chaque article.  Ce lien déclenche une action personnalisée qui crée une copie de l'article, incluant ses métadonnées et taxonomies.  Une redirection vers la page d'édition du nouvel article est ensuite effectuée, permettant une modification immédiate.  Des vérifications de sécurité (nonce) sont incluses pour prévenir les actions non autorisées.


## Redimensionnement des colonnes dans l'administration

Ce snippet améliore l'expérience utilisateur en ajoutant des grips de redimensionnement aux en-têtes des colonnes dans les tableaux de l'administration WordPress.  Cela permet aux utilisateurs de personnaliser la largeur des colonnes pour une meilleure lisibilité et organisation des données.

**Fichier :** `WP_ADMIN - Resize columns.php`

**Fonctionnement :**  Le snippet utilise une combinaison de CSS et de JavaScript. Le CSS ajoute des grips visuels aux en-têtes de colonne, tandis que le JavaScript gère l'interaction utilisateur (clic et glisser) pour redimensionner les colonnes dynamiquement.  Une largeur minimale est définie pour empêcher les colonnes de devenir trop étroites.


## Calendrier de planification des articles

Ce snippet crée un calendrier interactif et visuellement attrayant dans l'administration WordPress pour visualiser et gérer les articles planifiés.  Il offre une vue d'ensemble claire et concise de votre contenu planifié.

**Fichier :** `WP_ADMIN - Schedule Calendar.php`

### Fonctionnalités du calendrier

* **Affichage mensuel :**  Affiche les articles planifiés pour un mois donné.
* **Navigation :**  Permet de naviguer facilement entre les mois et les années.
* **Filtre par catégorie :**  Filtre les articles affichés par catégorie.
* **Recherche :**  Recherche rapide des articles par titre.
* **Actions sur les articles :**  Liens pour visualiser et modifier chaque article directement depuis le calendrier.
* **Statistiques mensuelles :**  Affiche des statistiques sur le nombre d'articles planifiés pour le mois courant et l'année.
* **Gestion des différents statuts :**  Affiche les articles avec les statuts "publié", "brouillon", "en attente", et "planifié".
* **Intégration avec la barre d'administration :**  Ajoute un raccourci vers le calendrier dans la barre d'administration.


### Installation et Configuration du calendrier

1. **Enregistrement du script :**  Le snippet utilise `add_action('admin_menu', ...)` pour ajouter une nouvelle page de menu dans l'administration WordPress.
2. **Ajout des scripts et styles :**  Il utilise `add_action('admin_enqueue_scripts', ...)` pour charger les scripts jQuery UI nécessaires pour la fonctionnalité du calendrier.
3. **Génération du calendrier :**  La fonction `generate_scheduled_posts_calendar_alpha` génère le code HTML du calendrier.
4. **Requêtes à l'API REST :**  Le calendrier récupère les données des articles via l'API REST de WordPress.


## Ajout automatique de tags existants

Ce snippet automatise le processus d'ajout de tags à vos articles en fonction de leur titre et/ou contenu.  Il analyse le texte de l'article et ajoute les tags correspondants parmi les tags déjà existants dans votre base de données.

**Fichier :** `WP_POST - Already existing tags.php`

**Fonctionnement :**  Le snippet utilise des expressions régulières pour rechercher les tags dans le titre et/ou le contenu de l'article.  Il offre une grande flexibilité grâce à ses options de configuration.

### Options de configuration pour la fonction `aet_tagging`

* **`aet_turn_on` :** Active ou désactive la fonctionnalité.
* **`aet_examine_post_title` :**  Indique si le titre doit être analysé.
* **`aet_examine_post_content` :** Indique si le contenu doit être analysé.
* **`aet_filter_by_category` :**  Indique si le processus doit être filtré par catégories spécifiques.
* **`aet_included_categories` :**  Tableau des identifiants des catégories à inclure dans le filtrage.
* **`aet_block_manually_added_tags` :**  Indique si les tags ajoutés manuellement doivent être supprimés avant l'ajout automatique.


## Heure de publication par défaut à 14h00

Ce snippet permet de définir une heure de publication par défaut pour les nouveaux articles.  Il vise à optimiser la planification des publications en évitant les conflits et en proposant un créneau horaire cohérent.

**Fichier :** `WP_POST - Default hours 14.00.php`

**Fonctionnement :**  Le snippet utilise le filtre `wp_insert_post_data` pour modifier la date et l'heure de publication des nouveaux articles.  Il recherche le prochain jour ouvrable disponible (du lundi au vendredi) à 14h00 et définit cette date/heure comme heure de publication par défaut.  Il vérifie également la disponibilité du créneau horaire pour éviter les conflits avec les articles déjà planifiés.


## Conversion des hashtags en liens

Ce snippet améliore l'expérience utilisateur en transformant automatiquement les hashtags présents dans le contenu des articles en liens cliquables.  Cela facilite l'accès aux pages de tags correspondantes.

**Fichier :** `WP_POST - Smart hashtags.php`

**Fonctionnement :**  Le snippet utilise une expression régulière pour identifier les hashtags (mots commençant par #).  Chaque hashtag est ensuite converti en un lien hypertexte pointant vers la page de tag correspondante sur votre site.  Le snippet gère également les balises HTML existantes pour éviter toute interférence.


## Installation

Pour utiliser ces snippets, copiez-collez le code de chaque fichier dans votre fichier `functions.php` de votre thème WordPress ou dans un plugin personnalisé.  Assurez-vous de respecter l'ordre des fonctions et des actions.  Certains snippets nécessitent des options personnalisées qui peuvent être configurées via la page des options de votre thème ou plugin (voir les sections de configuration pour plus de détails).


## Remarques

Ces snippets sont fournis "tels quels" sans garantie.  Il est fortement recommandé de sauvegarder votre site avant d'appliquer ces modifications.  Testez toujours ces snippets dans un environnement de développement avant de les mettre en production.  N'hésitez pas à adapter le code à vos besoins spécifiques et à le personnaliser davantage.