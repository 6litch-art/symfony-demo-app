# .env

If you need to override env variables for your specific needs, do not edit .env or .env.dev directly.

Instead, prefer a new `.env.dev.local` file to overrive onl what's nedded. This file MUST NOT be commited

```
touch .env.dev.local
```

# Docker (Optionnal)

If you want to use docker, (local db and/or typesense)

Duplicate `docker-compose.override` and edit as needed specific config (ports, volumes, ...)

```
cp docker-compose.override.dist.yml docker-compose.override.yml
```

Then run containers

```
docker compose up
```

Database will be accessible (by default) from `http://localhost:8080`

# DB Setup

Import database dump (Update credentials as needed)

```
mysql -h 127.0.0.1 -u root -proot typesense < data/typesense.sql  
```

Default base containes approx. 170 products

# Setup

```
composer install
symfony server:start
```

# Files

Pour info, dans le dossier ./public/storage se trouve les image brutes des produits
A priori, tu ne t'en serviras que dans une deuxieme phase, si l'on met en place la partie frontend.

# Makefile

NB: Il y a un makefile dans la racine du projet. Pour nettoyer le cache tu peux juster lancer "make" ou sinon les commandes symfony habituelles

# Dependencies

Le projet inclue un bundle glitchr/base-bundle qui ajoute quelques fonctionnalites que j'ai developpe au fil du temps.
Ce bundle inclue quelques entites que j'utilise et dont les produits dependent pour alleger la couche application.

La base de donnee est en inheritage join avec une discriminator map
Notre philosophie est que le code doit etre facile a utiliser. 
Donc j'injecte des services directement dans les entites.

NB: Concernant le cache, j'ai volontairement desactivé le secondary cache layer de la BDD.

# Typesense

## Fork 

To work on the lib, create an issues and an MR, and checkout localy the branch.

In your composer.json add the folowing lines (edit path)
```
    "repositories": [{
        "type": "path",
        "url": "../typesense-api"
    }],  
```

Then change the target version desired, ("dev-" followed by the branche name) : 

```
    "glitchr/ux-typesense": "dev-1-add-infix-parameter"
```

And update

```
composer update glitchr/ux-typesense
```

## Configuration

In the .env file, configure the following keys : 

```
TYPESENSE_URL=https://vdy7jh6maispogb8p-1.a1.typesense.net:443 # Don't forget the port number
TYPESENSE_API_KEY="INSERT API KEY HERE"
```

## Indexation

To create/update collection : 

```
symfony console typesense:create
```

To force indexation : 

```
symfony console typesense:import
```

## Usage

Route : `app_typesense`

Url : `/ux`

Params : 

`q` : Query

`filter` : Facet filtering

`p` : Page to load

### Simple search

Search for products with "mur" : `/ux?q=mur`

### Pagination

Pagination is given in the result.

```
  "pagination": {
    "current": 1,
    "previousLink": null,
    "nextLink": "http://localhost/index.php/ux?q=mur&p=2",
    "totalPages": 2
  },
```

`current` : Current page number

`totalPages` : Total page count

In order to go to the next page, follow the `nextLink` value. Same for going back to previous page.

### Facets

Facets are returned in the result.

```
"facets": [
    {
      "counts": [
        {
          "count": 15,
          "highlighted": "Mur végétal",
          "value": "Mur végétal"
        },
        {
          "count": 5,
          "highlighted": "Blanc",
          "value": "Blanc"
        },
        [...]
        {
          "count": 2,
          "highlighted": "Papier peint tropical et fleuri",
          "value": "Papier peint tropical et fleuri"
        }
      ],
      "field_name": "keywords",
      "stats": {
        "total_values": 18
      }
    }
  ],
```

If we only want results for keyword "Blanc", we have the `filter` param.

Fill it with the facet name (keywords in our example) followed by the value : 

`/ux?q=mur&filter=keywords:Blanc`
