<?php

namespace Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\Sort;

use Sylius\ElasticSearchPlugin\Search\Criteria\Ordering;
use Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\SearchCriteriaApplicator;
use Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Sort\SortFactoryInterface;
use ONGR\ElasticsearchDSL\Search;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class SortByFieldApplicator extends SearchCriteriaApplicator
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
    public function applyOrdering(Ordering $ordering, Search $search)
    {
        $search->addSort($this->sortByFieldQueryFactory->create($ordering));
    }
}
