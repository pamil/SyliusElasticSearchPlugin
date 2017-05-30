<?php

namespace spec\Sylius\ElasticSearchPlugin\Search\Criteria;

use Sylius\ElasticSearchPlugin\Search\Criteria\Paginating;
use PhpSpec\ObjectBehavior;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class PaginatingSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Paginating::class);
    }

    function it_can_be_created_form_query_parameters_with_default_values_if_parameters_are_empty()
    {
        $this->beConstructedThrough('fromQueryParameters', [[]]);

        $this->currentPage()->shouldReturn(1);
        $this->itemsPerPage()->shouldReturn(10);
        $this->offset()->shouldReturn(0);
    }

    function it_can_be_created_from_query_parameters_with_default_values_if_parameters_are_not_valid()
    {
        $this->beConstructedThrough('fromQueryParameters', [[
            'page' => -1,
            'limit' => -100,
        ]]);

        $this->currentPage()->shouldReturn(1);
        $this->itemsPerPage()->shouldReturn(10);
        $this->offset()->shouldReturn(0);
    }

    function it_can_be_created_from_query_parameters()
    {
        $this->beConstructedThrough('fromQueryParameters', [[
            'page' => 2,
            'limit' => 50,
        ]]);

        $this->currentPage()->shouldReturn(2);
        $this->itemsPerPage()->shouldReturn(50);
        $this->offset()->shouldReturn(50);
    }
}
