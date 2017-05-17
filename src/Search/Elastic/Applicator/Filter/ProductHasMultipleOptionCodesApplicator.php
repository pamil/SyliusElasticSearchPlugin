<?php

namespace Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\Filter;

use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use Sylius\ElasticSearchPlugin\Search\Criteria\Filtering\ProductHasOptionCodesFilter;
use Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\SearchCriteriaApplicator;
use Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Query\QueryFactoryInterface;
use ONGR\ElasticsearchDSL\Search;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ProductHasMultipleOptionCodesApplicator extends SearchCriteriaApplicator
{
    /**
     * @var QueryFactoryInterface
     */
    private $productHasOptionCodeQueryFactory;

    /**
     * {@inheritdoc}
     */
    public function __construct(QueryFactoryInterface $productHasOptionCodeQueryFactory)
    {
        $this->productHasOptionCodeQueryFactory = $productHasOptionCodeQueryFactory;
    }

    /**
     * @param ProductHasOptionCodesFilter $codesFilter
     * @param Search $search
     */
    public function applyProductHasOptionCodesFilter(ProductHasOptionCodesFilter $codesFilter, Search $search)
    {
        foreach ($codesFilter->getCodes() as $code) {
            $search->addPostFilter(
                $this->productHasOptionCodeQueryFactory->create(['option_value_code' => $code]),
                BoolQuery::SHOULD
            );
        }
    }
}
