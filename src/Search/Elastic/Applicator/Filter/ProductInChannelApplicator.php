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
final class ProductInChannelApplicator implements SearchCriteriaApplicatorInterface
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
    public function apply(Criteria $criteria, Search $search)
    {
        $search->addPostFilter($this->productInChannelQueryFactory->create($criteria->filtering()->fields()), BoolQuery::MUST);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Criteria $criteria)
    {
        return
            array_key_exists('channel_code', $criteria->filtering()->fields()) &&
            null !== $criteria->filtering()->fields()['channel_code']
        ;
    }
}
