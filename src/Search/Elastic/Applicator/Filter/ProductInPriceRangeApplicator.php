<?php

namespace Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\Filter;

use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use Sylius\ElasticSearchPlugin\Search\Criteria\Criteria;
use Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\SearchCriteriaApplicatorInterface;
use Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Query\QueryFactoryInterface;
use ONGR\ElasticsearchDSL\Search;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class ProductInPriceRangeApplicator implements SearchCriteriaApplicatorInterface
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
    public function apply(Criteria $criteria, Search $search)
    {
        $search->addPostFilter(
            $this->productInPriceRangeQueryFactory->create($criteria->filtering()->fields()),
            BoolQuery::MUST
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Criteria $criteria)
    {
        return
            array_key_exists('product_price_range', $criteria->filtering()->fields()) &&
            array_key_exists('grater_than', $criteria->filtering()->fields()['product_price_range']) &&
            array_key_exists('less_than', $criteria->filtering()->fields()['product_price_range']) &&
            null != $criteria->filtering()->fields()['product_price_range']['grater_than'] &&
            null != $criteria->filtering()->fields()['product_price_range']['less_than']
        ;
    }
}
