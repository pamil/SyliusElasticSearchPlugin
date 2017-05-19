<?php

namespace spec\Sylius\ElasticSearchPlugin\Search\Criteria\Filtering;

use Sylius\ElasticSearchPlugin\Search\Criteria\Filtering\ProductHasOptionCodesFilter;
use PhpSpec\ObjectBehavior;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ProductHasOptionCodesFilterSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(['mug_type_double', 'mug_type_small']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProductHasOptionCodesFilter::class);
    }

    function it_has_immutable_option_codes()
    {
        $this->getCodes()->shouldReturn(['mug_type_double', 'mug_type_small']);
    }
}
