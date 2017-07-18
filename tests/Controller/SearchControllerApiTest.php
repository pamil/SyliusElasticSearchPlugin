<?php

declare(strict_types=1);

namespace Tests\Sylius\ElasticSearchPlugin\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class SearchControllerApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_shows_paginated_product_list()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/products', [], [], ['ACCEPT' => 'application/json']);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_list_page', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_paginated_product_list_by_mugs()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/taxon-products/MUG', [], [], ['ACCEPT' => 'application/json']);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'mugs_list_page', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_paginated_product_list_by_en_gb_channel()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/products', ['channel' => 'WEB_GB'], [], ['ACCEPT' => 'application/json']);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_list_page_by_en_gb_channel', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_paginated_product_list_by_attributes()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/products', ['attributes' => ['Mug material' => ['Wood']]], [], ['ACCEPT' => 'application/json']);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_list_page_by_wood_material', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_paginated_product_list_with_limit()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/products', ['limit' => 1], [], ['ACCEPT' => 'application/json']);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'limited_product_list_page', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_first_page_of_paginated_product_list()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/products', ['limit' => 3, 'page' => 1], [], ['ACCEPT' => 'application/json']);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'first_page_of_limited_product_list_page', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_second_page_of_paginated_product_list()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/products', ['limit' => 3, 'page' => 2], [], ['ACCEPT' => 'application/json']);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'second_page_of_limited_product_list_page', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_product_list_by_price_range()
    {
        $this->loadFixturesFromFile('shop_with_different_prices.yml');

        $this->client->request('GET', '/shop-api/products', ['price' => '1000;4999'], [], ['ACCEPT' => 'application/json']);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_list_page_by_price_range', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_paginated_product_list_by_attributes_and_locale()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/products', ['attributes' => ['Mug material' => ['Wood']], 'locale' => 'en_GB'], [], ['ACCEPT' => 'application/json']);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_list_page_by_wood_material_and_en_GB_locale', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_paginated_product_list_by_name_using_search_query()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/products', ['search' => 'hat', 'locale' => 'en_GB'], [], ['ACCEPT' => 'application/json']);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_list_page_by_name', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_shows_paginated_product_list_by_attribute_using_search_query()
    {
        $this->loadFixturesFromFile('shop.yml');

        $this->client->request('GET', '/shop-api/products', ['search' => 'Wood', 'locale' => 'en_GB'], [], ['ACCEPT' => 'application/json']);

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'product_list_page_by_wood_material_and_en_GB_locale_using_search_query', Response::HTTP_OK);
    }

    /**
     * @before
     */
    protected function purgeElasticSearch()
    {
        $elasticSearchManager = static::$sharedKernel->getContainer()->get('es.manager.default');
        $elasticSearchManager->dropAndCreateIndex();
    }
}
