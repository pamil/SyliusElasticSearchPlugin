<?php

namespace Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\Query;

use Sylius\ElasticSearchPlugin\Search\Criteria\SearchPhrase;
use Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\SearchCriteriaApplicator;
use Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Query\QueryFactoryInterface;
use ONGR\ElasticsearchDSL\Search;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class MatchProductByNameApplicator extends SearchCriteriaApplicator
{
    /**
     * @var QueryFactoryInterface
     */
    private $matchProductNameQueryFactory;

    /**
     * @var QueryFactoryInterface
     */
    private $emptyCriteriaQueryFactory;

    /**
     * @param QueryFactoryInterface $matchProductNameQueryFactory
     * @param QueryFactoryInterface $emptyCriteriaQueryFactory
     */
    public function __construct(
        QueryFactoryInterface $matchProductNameQueryFactory,
        QueryFactoryInterface $emptyCriteriaQueryFactory
    ) {
        $this->matchProductNameQueryFactory = $matchProductNameQueryFactory;
        $this->emptyCriteriaQueryFactory = $emptyCriteriaQueryFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function applySearchPhrase(SearchPhrase $searchPhrase, Search $search)
    {
        if (null != $searchPhrase->getPhrase()) {

            $search->addQuery($this->matchProductNameQueryFactory->create(['phrase' => $searchPhrase->getPhrase()]));

            return;
        }

        $search->addQuery($this->emptyCriteriaQueryFactory->create());
    }
}
