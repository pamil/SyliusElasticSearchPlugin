<?php

namespace Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\Query;

use Sylius\ElasticSearchPlugin\Search\Criteria\Criteria;
use Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\SearchCriteriaApplicatorInterface;
use Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Query\QueryFactoryInterface;
use ONGR\ElasticsearchDSL\Search;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class MatchProductByNameApplicator implements SearchCriteriaApplicatorInterface
{
    /**
     * @var QueryFactoryInterface
     */
    private $matchProductNameQueryFactory;

    /**
     * @param QueryFactoryInterface $matchProductNameQueryFactory
     */
    public function __construct(QueryFactoryInterface $matchProductNameQueryFactory)
    {
        $this->matchProductNameQueryFactory = $matchProductNameQueryFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Criteria $criteria, Search $search)
    {
        $search->addQuery($this->matchProductNameQueryFactory->create($criteria->filtering()->fields()));
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Criteria $criteria)
    {
        return
            array_key_exists('search', $criteria->filtering()->fields()) &&
            null != $criteria->filtering()->fields()['search']
        ;
    }
}
