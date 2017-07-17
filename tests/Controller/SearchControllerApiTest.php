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
     * @before
     */
    protected function purgeElasticSearch()
    {
        $elasticSearchManager = static::$sharedKernel->getContainer()->get('es.manager.default');
        $elasticSearchManager->dropAndCreateIndex();
    }
}
