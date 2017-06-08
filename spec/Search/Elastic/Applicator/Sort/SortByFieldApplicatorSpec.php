<?php

namespace spec\Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\Sort;

use Sylius\ElasticSearchPlugin\Document\ProductDocument;
use Sylius\ElasticSearchPlugin\Search\Criteria\Criteria;
use Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\SearchCriteriaApplicatorInterface;
use Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\Sort\SortByFieldApplicator;
use Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Sort\SortFactoryInterface;
use ONGR\ElasticsearchDSL\Search;
use ONGR\ElasticsearchDSL\Sort\FieldSort;
use PhpSpec\ObjectBehavior;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class SortByFieldApplicatorSpec extends ObjectBehavior
{
    function let(SortFactoryInterface $sortByFieldSortFactory)
    {
        $this->beConstructedWith($sortByFieldSortFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SortByFieldApplicator::class);
    }

    function it_is_search_criteria_applicator()
    {
        $this->shouldImplement(SearchCriteriaApplicatorInterface::class);
    }

    function it_applies_sort_query_to_search_with_given_sorting(
        SortFactoryInterface $sortByFieldSortFactory,
        Search $search,
        FieldSort $fieldSort
    ) {
        $criteria = Criteria::fromQueryParameters(ProductDocument::class, ['sort' => '-name']);
        $sortByFieldSortFactory->create($criteria->ordering())->willReturn($fieldSort);
        $search->addSort($fieldSort)->shouldBeCalled();

        $this->apply($criteria, $search);
    }
}
