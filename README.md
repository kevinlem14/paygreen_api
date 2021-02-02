# PayGreen API

## Installation
Requis : Docker, docker-compose et MakeFile

Une fois le projet récupéré, se mettre à la racine de celui-ci et executer les commandes suivantes :
```shell
make build
make up
```

Ajouter un fichier `.env.dev.local` à la racine du projet avec le contenu suivant :
```yaml
DATABASE_URL="mysql://{USER}:{PASS}@mariadb:3306/paygreen_api?serverVersion=mariadb-10.3.22"
```

Notes :
- Penser à remplacer les variables {VAR}
- Pour cet exercice, le user et le mot de passe de la base de données seront root/root

Monter ensuite sur le container grâce à la commande `make php` et executer les commandes suivantes :
  ```shell
  composer install
  bin/console doctrine:database:create
  bin/console doctrine:migrations:migrate
  bin/console assets:install
  ```

## Utilisation

- Pour ajouter un utilisateur, utiliser la commande `bin/console app:create:user` dans le container.
- Pour interroger les routes, utiliser curl ou un outil comme Postman
- baseUrl = http://localhost:8080/ (hors du container)
- Pour les routes nécessitant une authentification, ajouter le header suivant à la requête :
    ```
    X-AUTH-TOKEN avec comme valeur le token API (à récupérer via la route d'authentification)
    ```

### Récupérer son token API :
```
Methode : POST
Route   : baseUrl/login
Form data :
    user[email]
    user[password]
Autorisation : Tous
```

### Ajouter une transaction :
```
Methode : POST
Route   : baseUrl/transactions
Form data :
    transaction[user]
    transaction[label]
Autorisation : ROLE_USER
```

### Lister les utilisateurs :
```
Methode : GET
Route   : baseUrl/users
Autorisation : ROLE_ADMIN
```

## Point à améliorer
- Ajout d'une API DOC (Exemple: NelmioApiDocBundle) permettant d'afficher les routes disponibles
- Ajout de test fonctionnel (Exemple: Avec PHPUnit) assurant la non regression des routes actuelles
- Catch des erreurs serveurs (exemple: manque de droit pour ajouter une transaction) pour les retourner proprement à l'utilisateur