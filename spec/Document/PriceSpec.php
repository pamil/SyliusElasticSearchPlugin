<?php

namespace spec\Sylius\ElasticSearchPlugin\Document;

use Sylius\ElasticSearchPlugin\Document\Price;
use PhpSpec\ObjectBehavior;

final class PriceSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Price::class);
    }

    function it_has_amount()
    {
        $this->setAmount(1000);

        $this->getAmount()->shouldReturn(1000);
    }

    function it_has_currency()
    {
        $this->setCurrency('EUR');

        $this->getCurrency()->shouldReturn('EUR');
    }
}
