<?php

namespace spec\Sylius\ElasticSearchPlugin\Search\Criteria;

use Sylius\ElasticSearchPlugin\Search\Criteria\Ordering;
use PhpSpec\ObjectBehavior;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class OrderingSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Ordering::class);
    }

    function it_can_be_created_from_query_parameters_with_default_direction()
    {
        $this->beConstructedThrough('fromQueryParameters', [[
            'sort' => 'code',
        ]]);

        $this->field()->shouldReturn('code.raw');
        $this->direction()->shouldReturn('asc');
    }

    function it_can_be_created_from_query_parameters()
    {
        $this->beConstructedThrough('fromQueryParameters', [[
            'sort' => '-code',
        ]]);

        $this->field()->shouldReturn('code.raw');
        $this->direction()->shouldReturn('desc');
    }
}
