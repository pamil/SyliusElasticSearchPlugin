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
    
    ongr_filter_manager:
        managers:
            search_list:
                filters:
                    - channel
                    - taxon_slug
                    - price_range
                    - locale
                    - attribute_values
                    - paginator
                    - search
                repository: es.manager.default.product
        filters:
            channel:
                type: choice
                request_field: channel
                document_field: channel_code
            taxon_slug:
                type: choice
                request_field: taxon_slug
                document_field: taxons.slug
            price_range:
                type: range
                request_field: price
                document_field: price.amount
            locale:
                type: choice
                request_field: locale_code
                document_field: locale_code
            attribute_values:
                type: multi_choice
                request_field: attributeValues
                document_field: attribute_values.value
            paginator:
                type: pager
                document_field: ~
                request_field: page
                options:
                    count_per_page: 10
            search:
                type: match
                request_field: search
                document_field: name,description,attribute_values.value
                options:
                    operator: and
    ```

8. Import routing file:

    ```yaml
       sylius_search:
           resource: "@SyliusElasticSearchPlugin/Resources/config/routing.yml"
    ```

    ```yaml
       #routing.yml
       shop_api_product_show_catalog:
           path: /shop-api/taxons/{taxon_slug}/products
           methods: [GET]
           defaults:
               _controller: sylius_elastic_search.controller.search
               _format: json

       shop_api_product_show_catalog_all:
           path: /shop-api/products
           methods: [GET]
           defaults:
               _controller: sylius_elastic_search.controller.search
               _format: json
    ```

9. Example Request/Response:

To activate filter you need to pass in parameter (query, request, attribute) ``requested field`` see reference in configuration section.
For e.g:

```
    /shop-api/taxons/mugs/products?channel=WEB_DE&price=2000;3000
```

It will activate ``taxon_slug``, ``price_range`` and ``channel`` filter.

Request:

```
    /shop-api/taxons/mugs/products
```

Response:

```
{
    "items": [
        {
            "code": "LOGAN_MUG_CODE",
            "name": "Logan Mug",
            "slug": "logan-mug",
            "taxons": [
                {
                    "code": "MUG",
                    "slug": "mugs",
                    "position": 0,
                    "images": [],
                    "description": "@string@"
                }
            ],
            "variants": [
                {
                    "code": "LOGAN_MUG_CODE",
                    "name": "Logan Mug",
                    "price": {
                        "current": 1999,
                        "currency": "GBP"
                    },
                    "images": []
                }
            ],
            "attributes": [
                {
                    "code": "MUG_COLLECTION_CODE",
                    "name": "Mug collection",
                    "value": "HOLIDAY COLLECTION"
                },
                {
                    "code": "MUG_MATERIAL_CODE",
                    "name": "Mug material",
                    "value": "Wood"
                }
            ],
            "images": [],
            "channelCode": "WEB_GB",
            "localeCode": "en_GB",
            "mainTaxon": {
                "code": "MUG",
                "slug": "mugs",
                "images": [],
                "description": "@string@"
            }
        },
        {
            "code": "LOGAN_MUG_CODE",
            "name": "Logan Becher",
            "slug": "logan-becher",
            "taxons": [
                {
                    "code": "MUG",
                    "slug": "mugs",
                    "position": 0,
                    "images": [],
                    "description": "@string@"
                }
            ],
            "variants": [
                {
                    "code": "LOGAN_MUG_CODE",
                    "name": "Logan Becher",
                    "price": {
                        "current": 1999,
                        "currency": "GBP"
                    },
                    "images": []
                }
            ],
            "attributes": [
                {
                    "code": "MUG_COLLECTION_CODE",
                    "name": "Mug collection",
                    "value": "FEIERTAGSKOLLEKTION"
                },
                {
                    "code": "MUG_MATERIAL_CODE",
                    "name": "Mug material",
                    "value": "Holz"
                }
            ],
            "images": [],
            "channelCode": "WEB_GB",
            "localeCode": "de_DE",
            "mainTaxon": {
                "code": "MUG",
                "slug": "mugs",
                "images": [],
                "description": "@string@"
            }
        }
    ],
    "filters": {
        "channel": {
            "state": {
                "active": false,
                "urlParameters": [],
                "name": "channel",
                "options": []
            },
            "tags": [],
            "urlParameters": {
                "taxon_slug": "mugs"
            },
            "resetUrlParameters": {
                "taxon_slug": "mugs"
            },
            "name": "channel",
            "choices": [
                {
                    "active": false,
                    "default": false,
                    "urlParameters": {
                        "taxon_slug": "mugs",
                        "channel": "WEB_GB"
                    },
                    "label": "WEB_GB",
                    "count": 2
                }
            ]
        },
        "taxon_slug": {
            "state": {
                "active": true,
                "value": "mugs",
                "urlParameters": {
                    "taxon_slug": "mugs"
                },
                "name": "taxon_slug",
                "options": []
            },
            "tags": [],
            "urlParameters": {
                "taxon_slug": "mugs"
            },
            "resetUrlParameters": [],
            "name": "taxon_slug",
            "choices": [
                {
                    "active": true,
                    "default": false,
                    "urlParameters": [],
                    "label": "mugs",
                    "count": 2
                },
                {
                    "active": false,
                    "default": false,
                    "urlParameters": {
                        "taxon_slug": "t-shirts"
                    },
                    "label": "t-shirts",
                    "count": 2
                }
            ]
        },
        "price_range": {
            "state": {
                "active": false,
                "urlParameters": [],
                "name": "price_range",
                "options": []
            },
            "tags": [],
            "urlParameters": {
                "taxon_slug": "mugs"
            },
            "resetUrlParameters": {
                "taxon_slug": "mugs"
            },
            "name": "price_range",
            "minBounds": 1999,
            "maxBounds": 1999
        },
        "locale": {
            "state": {
                "active": false,
                "urlParameters": [],
                "name": "locale",
                "options": []
            },
            "tags": [],
            "urlParameters": {
                "taxon_slug": "mugs"
            },
            "resetUrlParameters": {
                "taxon_slug": "mugs"
            },
            "name": "locale",
            "choices": [
                {
                    "active": false,
                    "default": false,
                    "urlParameters": {
                        "taxon_slug": "mugs",
                        "locale_code": "de_DE"
                    },
                    "label": "de_DE",
                    "count": 1
                },
                {
                    "active": false,
                    "default": false,
                    "urlParameters": {
                        "taxon_slug": "mugs",
                        "locale_code": "en_GB"
                    },
                    "label": "en_GB",
                    "count": 1
                }
            ]
        },
        "attribute_values": {
            "state": {
                "active": false,
                "urlParameters": [],
                "name": "attribute_values",
                "options": []
            },
            "tags": [],
            "urlParameters": {
                "taxon_slug": "mugs"
            },
            "resetUrlParameters": {
                "taxon_slug": "mugs"
            },
            "name": "attribute_values",
            "choices": [
                {
                    "active": false,
                    "default": false,
                    "urlParameters": {
                        "taxon_slug": "mugs",
                        "attributeValues": [
                            "FEIERTAGSKOLLEKTION"
                        ]
                    },
                    "label": "FEIERTAGSKOLLEKTION",
                    "count": 1
                },
                {
                    "active": false,
                    "default": false,
                    "urlParameters": {
                        "taxon_slug": "mugs",
                        "attributeValues": [
                            "HOLIDAY COLLECTION"
                        ]
                    },
                    "label": "HOLIDAY COLLECTION",
                    "count": 1
                },
                {
                    "active": false,
                    "default": false,
                    "urlParameters": {
                        "taxon_slug": "mugs",
                        "attributeValues": [
                            "Holz"
                        ]
                    },
                    "label": "Holz",
                    "count": 1
                },
                {
                    "active": false,
                    "default": false,
                    "urlParameters": {
                        "taxon_slug": "mugs",
                        "attributeValues": [
                            "Wood"
                        ]
                    },
                    "label": "Wood",
                    "count": 1
                }
            ]
        },
        "paginator": {
            "state": {
                "active": false,
                "value": 1,
                "urlParameters": [],
                "name": "paginator",
                "options": []
            },
            "tags": [],
            "urlParameters": {
                "taxon_slug": "mugs"
            },
            "resetUrlParameters": {
                "taxon_slug": "mugs"
            },
            "name": "paginator",
            "currentPage": 1,
            "totalItems": 2,
            "maxPages": 10,
            "itemsPerPage": 10,
            "numPages": 1,
            "options": []
        },
        "search": {
            "state": {
                "active": false,
                "urlParameters": [],
                "name": "search",
                "options": []
            },
            "tags": [],
            "urlParameters": {
                "taxon_slug": "mugs"
            },
            "resetUrlParameters": {
                "taxon_slug": "mugs"
            },
            "name": "search"
        }
    }
}
```