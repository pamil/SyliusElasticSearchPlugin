<?php

namespace Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\Sort;

use Sylius\ElasticSearchPlugin\Search\Criteria\Criteria;
use Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\SearchCriteriaApplicatorInterface;
use Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Sort\SortFactoryInterface;
use ONGR\ElasticsearchDSL\Search;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class SortByFieldApplicator implements SearchCriteriaApplicatorInterface
{
    /**
     * @var SortFactoryInterface
     */
    private $sortByFieldQueryFactory;

    /**
     * @param SortFactoryInterface $sortByFieldQueryFactory
     */
    public function __construct(SortFactoryInterface $sortByFieldQueryFactory)
    {
        $this->sortByFieldQueryFactory = $sortByFieldQueryFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Criteria $criteria, Search $search)
    {
        $search->addSort($this->sortByFieldQueryFactory->create($criteria->ordering()));
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Criteria $criteria)
    {
        return null != $criteria->ordering()->field() && null != $criteria->ordering()->direction();
    }
}
