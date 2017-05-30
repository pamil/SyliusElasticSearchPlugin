<?php

namespace spec\Sylius\ElasticSearchPlugin\Search\Criteria;

use Sylius\ElasticSearchPlugin\Search\Criteria\Criteria;
use Sylius\ElasticSearchPlugin\Search\Criteria\Filtering;
use Sylius\ElasticSearchPlugin\Search\Criteria\Ordering;
use Sylius\ElasticSearchPlugin\Search\Criteria\Paginating;
use PhpSpec\ObjectBehavior;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class CriteriaSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Criteria::class);
    }

    function it_is_created_from_query_parameters_and_resource_alias()
    {
        $this->beConstructedThrough('fromQueryParameters', ['sylius.product', [
            'page' => 2,
            'per_page' => 50,
            'sort' => '-price',
            'option' => 'red',
        ]]);

        $this->documentClass()->shouldReturn('sylius.product');
        $this->filtering()->shouldBeLike(Filtering::fromQueryParameters([
            'page' => 2,
            'per_page' => 50,
            'sort' => '-price',
            'option' => 'red',
        ]));
        $this->paginating()->shouldBeLike(Paginating::fromQueryParameters([
            'page' => 2,
            'per_page' => 50,
            'sort' => '-price',
            'option' => 'red',
        ]));
        $this->ordering()->shouldBeLike(Ordering::fromQueryParameters([
            'page' => 2,
            'per_page' => 50,
            'sort' => '-price',
            'option' => 'red',
        ]));
    }
}
