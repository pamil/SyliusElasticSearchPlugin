<?php

namespace Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\Filter;

use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use Sylius\ElasticSearchPlugin\Search\Criteria\Filtering\ProductInPriceRangeFilter;
use Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\SearchCriteriaApplicator;
use Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Query\QueryFactoryInterface;
use ONGR\ElasticsearchDSL\Search;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class ProductInPriceRangeApplicator extends SearchCriteriaApplicator
{
    /**
     * @var QueryFactoryInterface
     */
    private $productInPriceRangeQueryFactory;

    /**
     * @param QueryFactoryInterface $productInPriceRangeQueryFactory
     */
    public function __construct(QueryFactoryInterface $productInPriceRangeQueryFactory)
    {
        $this->productInPriceRangeQueryFactory = $productInPriceRangeQueryFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function applyProductInPriceRangeFilter(ProductInPriceRangeFilter $inPriceRangeFilter, Search $search)
    {
        $search->addPostFilter(
            $this->productInPriceRangeQueryFactory->create([
                'product_price_range' => [
                    'grater_than' => $inPriceRangeFilter->getGraterThan(),
                    'less_than' => $inPriceRangeFilter->getLessThan()
                ]
            ]),
            BoolQuery::MUST
        );
    }
}
