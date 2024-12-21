
# README

## Application Symfony - Instructions d'utilisation

Cette application Symfony utilise une base de données SQLite intégrée, ce qui signifie qu'il n'est pas nécessaire de configurer une base de données externe. Tout ce dont vous avez besoin est inclus dans le projet pour une mise en route rapide et facile.

### Prérequis

- PHP >= 8.1
- Composer
- Symfony CLI (facultatif, mais recommandé)

### Installation

1. **Cloner le projet**
   ```bash
   git clone <URL_DU_PROJET>
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
   - Les tables nécessaires pour l'application
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

### Utilisateurs créés

| Rôle        | Email                    | Mot de passe |
|-------------|--------------------------|--------------|
| Administrateur | role_admin@example.com | password     |
| Banni       | role_banned@example.com  | password     |
| Utilisateur | role_user@example.com    | password     |

### Fonctionnalités

- Base de données SQLite légère et portable.
- Données de test préconfigurées grâce aux fixtures.
- Prise en charge des rôles utilisateur : Admin, Banned, User.

### Notes supplémentaires

- En cas de problème avec les fixtures, assurez-vous que les tables sont vides avant de relancer :
  ```bash
  php bin/console doctrine:schema:drop --force
  php bin/console doctrine:schema:update --force
  php bin/console doctrine:fixtures:load
  ```

- Pour tester les différents cas d'utilisation, connectez-vous avec les emails et le mot de passe fournis ci-dessus.

Bonne exploration de l'application !
