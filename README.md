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

Monter ensuite sur le container grâce à la commande `make php` et executez les commandes suivantes :
  ```shell
  composer install
  bin/console doctrine:database:create
  bin/console doctrine:migrations:migrate
  bin/console assets:install
  ```
