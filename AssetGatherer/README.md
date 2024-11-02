# AssetGatherer Documentation

Le package `AssetGatherer` permet de collecter des ressources (images, CSS, JavaScript, etc.) en fonction de règles définies pour chaque requête HTTP. Ces règles sont configurées dans un fichier YAML. Les ressources sont organisées en groupes appelés *bundles*, qui peuvent regrouper des fichiers de différents types selon les sections de l'application.

## Table des Matières

- [Installation](#installation)
- [Configuration](#configuration)
  - [Structure du Fichier YAML](#structure-du-fichier-yaml)
- [Utilisation](#utilisation)
  - [Chargement de la Configuration](#chargement-de-la-configuration)
  - [Collecte des Ressources](#collecte-des-ressources)
  - [Récupération des Ressources Collectées](#récupération-des-ressources-collectées)
- [Exemples](#exemples)
  - [Exemple Complet](#exemple-complet)
- [Exécution des Tests](#exécution-des-tests)

## Installation

1. **Installer le composant YAML de Symfony** pour pouvoir lire les configurations YAML :
   ```bash
   composer require symfony/yaml
   ```

2. **Installer PHPUnit** pour les tests unitaires :
   ```bash
   composer require --dev phpunit/phpunit
   ```

## Configuration

La configuration du package `AssetGatherer` se fait via un fichier YAML. Chaque *bundle* peut définir des répertoires et types de fichiers spécifiques, ainsi que des règles conditionnelles en fonction de la requête HTTP (URL, headers, paramètres de requête).

### Structure du Fichier YAML

Un fichier YAML typique se présente comme suit. Il se nomme généralement `bundles.yaml` et se trouve dans le dossier `config` :

```yaml
# config/bundles.yaml
homepage:
  images:
    directories: ['path/to/homepage/images']
    extensions: ['jpg', 'png']
  css:
    directories: ['path/to/homepage/css']
    extensions: ['css']
  rules:
    pathContains: '/homepage'

dashboard:
  javascript:
    directories: ['path/to/dashboard/js']
    extensions: ['js']
  rules:
    query:
      admin: 'true'
```

Dans cet exemple :
- **`homepage`** est un bundle qui inclut des images et du CSS. Ce bundle est chargé si le chemin de la requête contient `/homepage`.
- **`dashboard`** est un bundle qui inclut du JavaScript. Ce bundle est chargé uniquement si le paramètre de requête `admin` est défini sur `true`.

### Exemple de Règle de Bundle avec Plusieurs Paramètres de la Query

Voici un exemple de configuration d'un *bundle* utilisant plusieurs paramètres de la `query`. Ce *bundle* ne se charge que si plusieurs conditions basées sur les paramètres de la requête sont satisfaites.

Ajoutez cet exemple à la section **Structure du Fichier YAML** dans la documentation.

```yaml
# config/bundles.yaml
admin_dashboard:
  javascript:
    directories: ['path/to/admin_dashboard/js']
    extensions: ['js']
  css:
    directories: ['path/to/admin_dashboard/css']
    extensions: ['css']
  rules:
    query:
      admin: 'true'
      view: 'dashboard'

## Utilisation

### Chargement de la Configuration

Chargez la configuration du fichier YAML :

```php
$assetGatherer = new AssetGatherer();
$assetGatherer->loadConfiguration('config/bundles.yaml');
```

### Collecte des Ressources

Pour collecter les ressources en fonction d'une requête HTTP, fournissez les données de la requête sous forme de tableau :

```php
$request = [
    'path' => '/homepage',
    'headers' => [
        'User-Agent' => 'Chrome',
        'Accept' => 'text/html'
    ],
    'query' => []
];

$assetGatherer->gatherAssetsForRequest($request);
```

### Récupération des Ressources Collectées

Une fois les ressources collectées, vous pouvez les récupérer avec la méthode `getAssets()`. Cette méthode permet de récupérer toutes les ressources ou uniquement celles d'un bundle spécifique.

```php
// Récupérer toutes les ressources collectées
$allAssets = $assetGatherer->getAssets();

// Récupérer uniquement les ressources du bundle 'homepage'
$homepageAssets = $assetGatherer->getAssets('homepage');
```

## Exemples

### Exemple Complet

Voici un exemple complet qui comprend le chargement de la configuration, la collecte de ressources pour une requête, et la récupération des ressources collectées.

```php
// Instanciez AssetGatherer et chargez la configuration
$assetGatherer = new AssetGatherer();
$assetGatherer->loadConfiguration('config/bundles.yaml');

// Simulez une requête HTTP
$request = [
    'path' => '/homepage',
    'headers' => [
        'User-Agent' => 'Chrome',
        'Accept' => 'text/html'
    ],
    'query' => []
];

// Collectez les ressources en fonction de la requête
$assetGatherer->gatherAssetsForRequest($request);

// Récupérez et affichez les ressources collectées
$assets = $assetGatherer->getAssets();
print_r($assets);
```

Avec cette configuration, les ressources sous `path/to/homepage/images` et `path/to/homepage/css` seront collectées si le chemin contient `/homepage`.

## Exécution des Tests

Des tests sont inclus pour vérifier que `AssetGatherer` fonctionne correctement.

1. **Configurez les répertoires et fichiers d'exemples** comme indiqué dans la documentation de test.

2. **Exécutez les tests** avec PHPUnit :
   ```bash
   vendor/bin/phpunit tests/AssetGathererTest.php
   ```

Pour plus d'informations sur PHPUnit, consultez la [documentation PHPUnit](https://phpunit.de/).
