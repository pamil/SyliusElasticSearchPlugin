<?php

namespace spec\Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Sort;

use Sylius\ElasticSearchPlugin\Search\Criteria\Ordering;
use Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Sort\SortByFieldQueryFactory;
use Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Sort\SortFactoryInterface;
use ONGR\ElasticsearchDSL\Sort\FieldSort;
use PhpSpec\ObjectBehavior;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class SortByFieldQueryFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(SortByFieldQueryFactory::class);
    }

    function it_is_sort_factory()
    {
        $this->shouldImplement(SortFactoryInterface::class);
    }

    function it_creates_descending_field_sort_query()
    {
        $ordering = Ordering::fromQueryParameters(['sort' => '-price']);

        $this->create($ordering)->shouldBeLike(new FieldSort('price.raw', 'desc'));
    }

    function it_creates_ascending_field_sort_query()
    {
        $ordering = Ordering::fromQueryParameters(['sort' => 'price']);

        $this->create($ordering)->shouldBeLike(new FieldSort('price.raw', 'asc'));
    }

    function it_creates_ascending_by_name_field_sort_query_by_default()
    {
        $ordering = Ordering::fromQueryParameters([]);

        $this->create($ordering)->shouldBeLike(new FieldSort('name.raw', 'asc'));
    }
}
