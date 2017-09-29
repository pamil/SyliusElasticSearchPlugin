<?php

declare(strict_types=1);

namespace spec\Sylius\ElasticSearchPlugin\Document;

use PhpSpec\ObjectBehavior;
use Sylius\ElasticSearchPlugin\Document\PriceDocument;

final class PriceDocumentSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PriceDocument::class);
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
