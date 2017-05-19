<?php

namespace Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\Filter;

use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use Sylius\ElasticSearchPlugin\Search\Criteria\Filtering\ProductInChannelFilter;
use Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\SearchCriteriaApplicator;
use Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Query\QueryFactoryInterface;
use ONGR\ElasticsearchDSL\Search;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class ProductInChannelApplicator extends SearchCriteriaApplicator
{
    /**
     * @var QueryFactoryInterface
     */
    private $productInChannelQueryFactory;

    /**
     * @param QueryFactoryInterface $productInChannelQueryFactory
     */
    public function __construct(QueryFactoryInterface $productInChannelQueryFactory)
    {
        $this->productInChannelQueryFactory = $productInChannelQueryFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function applyProductInChannelFilter(ProductInChannelFilter $inChannelFilter, Search $search)
    {
        $search->addPostFilter($this->productInChannelQueryFactory->create(['channel_code' => $inChannelFilter->getChannelCode()]), BoolQuery::MUST);
    }
}
