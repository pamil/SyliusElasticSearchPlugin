<?php

namespace spec\Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\Query;

use ONGR\ElasticsearchDSL\Query\FullText\MatchQuery;
use Sylius\ElasticSearchPlugin\Document\ProductDocument;
use Sylius\ElasticSearchPlugin\Search\Criteria\Criteria;
use Sylius\ElasticSearchPlugin\Search\Criteria\SearchPhrase;
use Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\Query\MatchProductByNameApplicator;
use Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\SearchCriteriaApplicatorInterface;
use Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Query\QueryFactoryInterface;
use ONGR\ElasticsearchDSL\Query\MatchAllQuery;
use ONGR\ElasticsearchDSL\Search;
use PhpSpec\ObjectBehavior;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class MatchProductByNameApplicatorSpec extends ObjectBehavior
{
    function let(QueryFactoryInterface $matchProductNameQueryFactory)
    {
        $this->beConstructedWith($matchProductNameQueryFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MatchProductByNameApplicator::class);
    }

    function it_is_criteria_search_applicator()
    {
        $this->shouldImplement(SearchCriteriaApplicatorInterface::class);
    }

    function it_applies_match_product_by_name_query(
        QueryFactoryInterface $matchProductNameQueryFactory,
        MatchQuery $matchQuery,
        Search $search
    ) {
        $criteria = Criteria::fromQueryParameters(ProductDocument::class, ['search' => 'banana']);
        $matchProductNameQueryFactory->create($criteria->filtering()->fields())->willReturn($matchQuery);
        $search->addQuery($matchQuery)->shouldBeCalled();

        $this->apply($criteria, $search);
    }

    function it_supports_search_parameter()
    {
        $criteria = Criteria::fromQueryParameters(ProductDocument::class, ['search' => 'banana']);
        $this->supports($criteria)->shouldReturn(true);

        $criteria = Criteria::fromQueryParameters(ProductDocument::class, []);
        $this->supports($criteria)->shouldReturn(false);
    }
}
