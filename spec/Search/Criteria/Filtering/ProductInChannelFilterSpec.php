<?php

namespace spec\Sylius\ElasticSearchPlugin\Search\Criteria\Filtering;

use Sylius\ElasticSearchPlugin\Search\Criteria\Filtering\ProductInChannelFilter;
use PhpSpec\ObjectBehavior;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ProductInChannelFilterSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('web_uk');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProductInChannelFilter::class);
    }

    function it_has_immutable_channel_code()
    {
        $this->getChannelCode()->shouldReturn('web_uk');
    }
}
