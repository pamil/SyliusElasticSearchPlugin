<?php

namespace Sylius\ElasticSearchPlugin\Factory;

use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use ONGR\FilterManagerBundle\Search\SearchResponse;
use Sylius\ElasticSearchPlugin\Controller\ProductListView;
use Sylius\ElasticSearchPlugin\Search\Criteria\Paginating;

interface ProductListViewFactoryInterface
{
    /**
     * @param SearchResponse $response
     *
     * @return ProductListView
     */
    public function createFromSearchResponse(SearchResponse $response);
}
