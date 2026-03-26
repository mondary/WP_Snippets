---
name: wppkpremium-workflow
description: Règles de travail pour le plugin WordPress WPpkpremium dans ce dépôt, en repartant d’une base stable et en appliquant les changements par petites étapes sûres, avec versioning 1.xx et ZIP de release versionné.
---

# WPpkpremium Workflow

Utiliser ce workflow dès qu’un travail concerne le plugin `WPpkpremium`.

## Arborescence

Le plugin vit dans cette structure :

```text
extension/
  src/
    WPpkpremium/
      WPpkpremium.php
      includes/
  release/
```

Règles :

- Le code source modifiable est dans `extension/src/WPpkpremium`.
- Les archives installables WordPress sont dans `extension/release`.
- Ne pas développer une deuxième copie du plugin ailleurs dans le dépôt.

## Versioning

À chaque modification du plugin :

- incrémenter la version dans le header du plugin
- utiliser un format `1.xx`

Exemples :

- `1.01`
- `1.02`
- `1.03`

La constante `PKPREMIUM_VERSION` doit toujours matcher la version du header.

## ZIP de release

Chaque build installable doit être exporté avec un nom versionné :

```text
WPpkpremium-v1.15.zip
```

Règles :

- générer le zip dans `extension/release/`
- inclure le dossier plugin `WPpkpremium` à la racine de l’archive
- ne pas écraser silencieusement une release précédente si la version change

## Portée actuelle du plugin

`WPpkpremium` est la base du futur système d’abonnement.

Il reprend déjà :

- la logique `future_site`
- le rôle WordPress `premium`
- l’accès admin + premium à l’aperçu futur
- la future base de réglages PayPal et webhooks

## Transition depuis les snippets

Pendant la migration :

- garder le plugin comme nouvelle source de vérité
- éviter le double chargement avec l’ancien snippet `Futur site`
- si l’ancien snippet est encore actif, le désactiver avant les tests complets du plugin

Le pipeline distant actuel pousse seulement les snippets via `Code Snippets`.
Il ne déploie pas automatiquement les plugins WordPress.

## Règles de travail

- Avant toute release, valider la syntaxe PHP avec `php -l`.
- Après chaque modification plugin, régénérer le zip versionné.
- Si l’arborescence change, mettre à jour ce fichier.
- Préférer une migration progressive : v1 testable, puis ajout PayPal, puis webhooks, puis mapping paiement -> utilisateur -> rôle `premium`.

## Commandes utiles

Validation :

```bash
php -l extension/src/WPpkpremium/WPpkpremium.php
php -l extension/src/WPpkpremium/includes/admin.php
php -l extension/src/WPpkpremium/includes/future-preview.php
php -l extension/src/WPpkpremium/includes/sync.php
```

Release :

```bash
cd extension/src
zip -qr ../release/WPpkpremium-v1.15.zip WPpkpremium
```

## Intention produit

Objectif à terme :

- comptes WordPress natifs
- paiement d’abonnement mensuel
- attribution automatique de l’accès premium
- accès premium au futur site pour les abonnés et les administrateurs
