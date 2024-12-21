
# README

## Application Symfony - Instructions d'utilisation

Cette application Symfony est une plateforme e-commerce utilisant une base de données SQLite intégrée. Cela signifie qu'il n'est pas nécessaire de configurer une base de données externe. Tout ce dont vous avez besoin est inclus dans le projet pour une mise en route rapide et facile.

### Prérequis

- PHP >= 8.1
- Composer
- Symfony CLI (facultatif, mais recommandé)

### Installation

1. **Cloner le projet**
   ```bash
   git clone git@github.com:fouaddjellali/EcommerceAPP.git
   cd <NOM_DU_PROJET>
   ```

2. **Installer les dépendances**
   ```bash
   composer install
   ```

3. **Configurer l'environnement**
   Copiez le fichier `.env` en `.env.local` pour personnaliser les configurations locales si nécessaire.
   ```bash
   cp .env .env.local
   ```

4. **Générer la base de données et peupler les données**
   Cette application utilise SQLite comme base de données. Aucune configuration supplémentaire n'est requise. Pour générer les tables et peupler les données de test, exécutez les commandes suivantes :
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:schema:update --force
   php bin/console doctrine:fixtures:load
   ```
   Cette commande crée automatiquement :
   - Les entités principales suivantes : `Commande`, `Produit`, `Categorie`, `Commentaire`.
   - Trois utilisateurs pour tester les différents cas d'utilisation :
     - **Admin** : `role_admin@example.com`
     - **Banned** : `role_banned@example.com`
     - **User** : `role_user@example.com`

     Tous les utilisateurs ont le mot de passe : `password`.

5. **Démarrer le serveur local**
   ```bash
   symfony server:start
   ```
   Accédez à l'application via [http://localhost:8000](http://localhost:8000).

### Fonctionnalités principales

#### Gestion des utilisateurs

- **Authentification complète** :
  - Connexion avec email et mot de passe.
  - Réinitialisation du mot de passe (mot de passe oublié et reset).
  - Modification du mot de passe pour les utilisateurs connectés.
- **Rôles utilisateur** :
  - `ADMIN` : accès complet, y compris la gestion de l'administration.
  - `USER` : accès au profil utilisateur et aux fonctionnalités e-commerce.
  - `BANNED` : accès restreint avec affichage d'un message de bannissement.

#### Gestion du contenu dynamique

- Affichage adapté en fonction de l'état de connexion :
  - Si l'utilisateur est connecté, affichage de son nom et prénom.
  - Si l'utilisateur est non connecté, affichage d'un bouton "Se connecter".
  - Si l'utilisateur est connecté, affichage d'un bouton "Se déconnecter".
- Affichage en fonction des rôles :
  - **ADMIN** : accès à un bouton "Admin" pour gérer l'administration.
  - **USER** : accès à un bouton "Profil" pour gérer son profil.
  - **BANNED** : affichage d'un message expliquant le bannissement et restriction des pages.

#### Gestion des entités e-commerce

- Création, lecture, mise à jour et suppression (CRUD) pour les entités suivantes :
  - `Commande`
  - `Produit`
  - `Categorie`
  - `Commentaire`

#### Design et visuels

- Utilisation des assets **Admin LTE** pour un design professionnel et intuitif.

#### Sécurisation

- Formulaires et routes sécurisés pour éviter tout accès non autorisé.
- Les routes liées au tableau de bord (dashboard) sont protégées et accessibles uniquement aux utilisateurs ayant le rôle `ADMIN`.

### Utilisateurs créés

| Rôle        | Email                    | Mot de passe |
|-------------|--------------------------|--------------|
| Administrateur | role_admin@example.com | password     |
| Banni       | role_banned@example.com  | password     |
| Utilisateur | role_user@example.com    | password     |

### Notes supplémentaires

- En cas de problème avec les fixtures, assurez-vous que les tables sont vides avant de relancer :
  ```bash
  php bin/console doctrine:schema:drop --force
  php bin/console doctrine:schema:update --force
  php bin/console doctrine:fixtures:load
  ```

