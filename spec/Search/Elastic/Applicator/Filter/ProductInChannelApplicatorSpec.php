<?php

namespace spec\Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\Filter;

use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\Joining\NestedQuery;
use Sylius\ElasticSearchPlugin\Document\ProductDocument;
use Sylius\ElasticSearchPlugin\Search\Criteria\Criteria;
use Sylius\ElasticSearchPlugin\Search\Criteria\Filtering\ProductInChannelFilter;
use Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\Filter\ProductInChannelApplicator;
use Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\SearchCriteriaApplicatorInterface;
use Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Query\QueryFactoryInterface;
use ONGR\ElasticsearchDSL\Search;
use PhpSpec\ObjectBehavior;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ProductInChannelApplicatorSpec extends ObjectBehavior
{
    function let(QueryFactoryInterface $productInChannelQueryFactory)
    {
        $this->beConstructedWith($productInChannelQueryFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProductInChannelApplicator::class);
    }

    function it_is_search_criteria_applicator()
    {
        $this->shouldImplement(SearchCriteriaApplicatorInterface::class);
    }

    function it_applies_search_criteria_with_channel_code(
        QueryFactoryInterface $productInChannelQueryFactory,
        Search $search,
        NestedQuery $nestedQuery
    ) {
        $criteria = Criteria::fromQueryParameters(ProductDocument::class, ['channel_code' => 'web']);
        $productInChannelQueryFactory->create($criteria->filtering()->fields())->willReturn($nestedQuery);
        $search->addPostFilter($nestedQuery, BoolQuery::MUST)->shouldBeCalled();

        $this->apply($criteria, $search);
    }

    function it_supports_channel_code_parameter()
    {
        $criteria = Criteria::fromQueryParameters(ProductDocument::class, ['channel_code' => 'web']);

        $this->supports($criteria)->shouldReturn(true);

        $criteria = Criteria::fromQueryParameters(ProductDocument::class, ['taxon_code' => 'tree']);

        $this->supports($criteria)->shouldReturn(false);
    }
}
