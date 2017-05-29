<?php

namespace spec\Sylius\ElasticSearchPlugin\Document;

use Sylius\ElasticSearchPlugin\Document\TaxonCode;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class TaxonCodeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(TaxonCode::class);
    }

    function it_has_value()
    {
        $this->setValue('mug');

        $this->getValue()->shouldReturn('mug');
    }
}
