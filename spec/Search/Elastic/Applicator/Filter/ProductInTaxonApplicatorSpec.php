<?php

namespace spec\Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\Filter;

use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use Sylius\ElasticSearchPlugin\Document\ProductDocument;
use Sylius\ElasticSearchPlugin\Search\Criteria\Criteria;
use Sylius\ElasticSearchPlugin\Search\Criteria\Filtering\ProductInTaxonFilter;
use Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\Filter\ProductInTaxonApplicator;
use Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\SearchCriteriaApplicatorInterface;
use Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Query\QueryFactoryInterface;
use ONGR\ElasticsearchDSL\Search;
use PhpSpec\ObjectBehavior;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ProductInTaxonApplicatorSpec extends ObjectBehavior
{
    function let(QueryFactoryInterface $productInMainTaxon)
    {
        $this->beConstructedWith($productInMainTaxon);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProductInTaxonApplicator::class);
    }

    function it_is_search_criteria_applicator()
    {
        $this->shouldImplement(SearchCriteriaApplicatorInterface::class);
    }

    function it_applies_search_query_for_given_criteria(
        QueryFactoryInterface $productInMainTaxon,
        TermQuery $termQuery,
        Search $search
    ) {
        $criteria = Criteria::fromQueryParameters(ProductDocument::class, ['taxon_code' => 'mugs']);

        $productInMainTaxon->create($criteria->filtering()->fields())->willReturn($termQuery);

        $search->addPostFilter($termQuery, BoolQuery::SHOULD)->shouldBeCalled();

        $this->apply($criteria, $search);
    }

    function it_supports_taxon_code_paramter()
    {
        $criteria = Criteria::fromQueryParameters(ProductDocument::class, ['taxon_code' => 'mugs']);
        $this->supports($criteria)->shouldReturn(true);

        $criteria = Criteria::fromQueryParameters(ProductDocument::class, []);
        $this->supports($criteria)->shouldReturn(false);
    }
}
