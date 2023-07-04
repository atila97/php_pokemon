# Sujet test Symfony / API

Le but du test est de pouvoir importer un fichier CSV d'une liste de Pokémon (fournis dans le test) et de créer une API avec [API Platform](https://api-platform.com/) afin d'effectuer des actions sur les données
* L’utilisation de la version de Symfony est libre (de préférence Symfony 5.x ou 6.x).
* Le choix du type de la base de donnée est également libre.

Une authentification sera nécessaire afin de pouvoir utiliser l'API sur certaines routes.


## Information 
```
 Symfony                                               
 -------------------- --------------------------------- 
  Version              5.4.25                                     
 -------------------- --------------------------------- 
  PHP                                                   
 -------------------- --------------------------------- 
  Version              8.0.29                           
  Architecture         64 bits                                       
 -------------------- --------------------------------- 
  Composer                                                   
 -------------------- --------------------------------- 
  Version              2.5.8                           
```

## Installation
Installez les dépendances symfony.

```
$ git clone https://github.com/atila97/php_pokemon.git
$ cd php_pokemon
$ git checkout dev
$ composer install

#Lancer le serveur symfony
$ symfony server:start

# Mettre à jour la basse de données
$ php bin/console doctrine:schema:update --force
```

Installez Node (`V 14+`)

```
$ npm install
$ npm run build
```

## Import du fichier
Importez les données à partir d'un fichier valide.

``$ bin/console app:import:csv FILEPATH``

## Interfaces
### Symfony Web Application (Session)
Il s'agit de l'application Web classique qui utilise la session symfony.

`` http://localhost:8000/``


### Vue app application (Token)
C'est une application web créée rapidement avec VueJS. Cette application utilise les API déjà créées et gère l'authentification à l'aide d'un Token.

`` http://localhost:8000/app``
