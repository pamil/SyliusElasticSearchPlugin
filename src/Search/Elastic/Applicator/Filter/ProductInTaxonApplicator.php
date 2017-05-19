<?php

namespace Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\Filter;

use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use Sylius\ElasticSearchPlugin\Search\Criteria\Filtering\ProductInTaxonFilter;
use Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\SearchCriteriaApplicator;
use Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Query\QueryFactoryInterface;
use ONGR\ElasticsearchDSL\Search;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class ProductInTaxonApplicator extends SearchCriteriaApplicator
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
    public function applyProductInTaxonFilter(ProductInTaxonFilter $inTaxonFilter, Search $search)
    {
        $search->addPostFilter($this->productInMainTaxonQueryFactory->create(['taxon_code' => $inTaxonFilter->getTaxonCode()]), BoolQuery::SHOULD);
    }
}
