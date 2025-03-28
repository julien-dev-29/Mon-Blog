# Mon Blog

![Build Status](https://img.shields.io/badge/build-passing-brightgreen)
![License](https://img.shields.io/badge/license-MIT-blue)
![PHP Version](https://img.shields.io/badge/php-%3E%3D7.4-8892BF)

Mon Blog est un projet PHP conçu pour fournir une structure légère et modulaire pour le développement d'applications web. Il inclut des fonctionnalités pour la gestion des routes, l'interaction avec une base de données, et la création de vues dynamiques.

## Structure du projet

Voici un aperçu de la structure du projet :
- **/app** : Contient le code applicatif, y compris les contrôleurs, modèles et vues.
- **/config** : Fichiers de configuration pour l'application.
- **/public** : Point d'entrée public de l'application (ex. `index.php`).
- **/vendor** : Dépendances gérées par Composer.

## Instructions d'installation

1. Clonez le dépôt :
   ```bash
   git clone <url-du-repo>
   ```

2. Installez les dépendances avec Composer :
   ```bash
   composer install
   ```

3. Configurez les paramètres dans le fichier `/config/config.php`.

4. Lancez un serveur local :
   ```bash
   php -S localhost:8000 -t public
   ```

5. Accédez à l'application via `http://localhost:8000`.

## Fonctionnalités principales

- Gestion des routes.
- Système de templates pour les vues.
- Intégration facile avec une base de données via PDO.
- Architecture MVC simplifiée.

## Contribution

Les contributions sont les bienvenues ! Veuillez soumettre une pull request ou ouvrir une issue pour signaler des bugs ou proposer des améliorations.
