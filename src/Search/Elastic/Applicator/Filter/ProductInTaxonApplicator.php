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
final class ProductInTaxonApplicator implements SearchCriteriaApplicatorInterface
{
    /**
     * @var QueryFactoryInterface
     */
    private $productInMainTaxonQueryFactory;

    /**
     * @param QueryFactoryInterface $productInMainTaxonQueryFactory
     */
    public function __construct(QueryFactoryInterface $productInMainTaxonQueryFactory)
    {
        $this->productInMainTaxonQueryFactory = $productInMainTaxonQueryFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Criteria $criteria, Search $search)
    {
        $search->addPostFilter($this->productInMainTaxonQueryFactory->create(
            $criteria->filtering()->fields()),
            BoolQuery::SHOULD
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Criteria $criteria)
    {
        return
            array_key_exists('taxon_code', $criteria->filtering()->fields()) &&
            null != $criteria->filtering()->fields()['taxon_code']
        ;
    }
}
