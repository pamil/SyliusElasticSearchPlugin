Sylius ElasticSearchPlugin
==========================
Elastic search for Sylius.
[![Build status on Linux](https://img.shields.io/travis/Sylius/SyliusElasticSearchPlugin/master.svg)](https://travis-ci.org/Sylius/SyliusElasticSearchPlugin)

## Usage

1. Install it:

    ```bash
    $ composer require sylius/elastic-search-plugin
    ```

2. Install elastic search server:

    ```bash
    $ brew install elasticsearch@5.0
    ```

3. Run elastic search server:

    ```bash
    $ elasticsearch
    ```

4. Add this bundle to `AppKernel.php`:

    ```php
     new \ONGR\ElasticsearchBundle\ONGRElasticsearchBundle(),
     new \SimpleBus\SymfonyBridge\SimpleBusCommandBusBundle(),
     new \SimpleBus\SymfonyBridge\SimpleBusEventBusBundle(),
     new \ONGR\FilterManagerBundle\ONGRFilterManagerBundle(),
    ```

5. Create/Setup database:

    ```bash
    $ app/console do:da:cr
    $ app/console do:sch:cr
    $ app/console syl:fix:lo
    ```

7. Import config file in `app/config/config.yml` for default filter set configuration:

    ```yaml
    imports:
       - { resource: "@SyliusElasticSearchPlugin/Resources/config/app/config.yml" }
    ```
    For more configuration take a look at [ONGRFilterManager](http://docs.ongr.io/FilterManagerBundle)

8. Configure ONGR bundle in `app/config/config.yml`:

    ```yaml
    ongr_elasticsearch:
        managers:
            default:
                index:
                    index_name: sylius
                mappings:
                    SyliusElasticSearchPlugin: {}
    ```

8. Import routing files in `app/config/routing.yml`:

    ```yaml
    sylius_search:
        resource: "@SyliusElasticSearchPlugin/Resources/config/routing.yml"
    ```
