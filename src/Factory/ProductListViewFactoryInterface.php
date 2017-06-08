<?php

namespace Sylius\ElasticSearchPlugin\Factory;

use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use Sylius\ElasticSearchPlugin\Controller\ProductListView;
use Sylius\ElasticSearchPlugin\Search\Criteria\Paginating;

interface ProductListViewFactoryInterface
{
    /**
     * @param DocumentIterator $result
     * @param Paginating $paginating
     *
     * @return ProductListView
     */
    public function createFromDocumentIteratorAndPaginating(DocumentIterator $result, Paginating $paginating);
}
