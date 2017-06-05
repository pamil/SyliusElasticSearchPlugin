<?php

namespace spec\Sylius\ElasticSearchPlugin\Document;

use Sylius\ElasticSearchPlugin\Document\Attribute;
use Sylius\ElasticSearchPlugin\Document\AttributeValue;
use PhpSpec\ObjectBehavior;

final class AttributeValueSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AttributeValue::class);
    }

    function it_has_value()
    {
        $this->setValue('Red');

        $this->getValue()->shouldReturn('Red');
    }

    function it_has_attribute()
    {
        $attribute = new Attribute();
        $this->setAttribute($attribute);

        $this->getAttribute()->shouldReturn($attribute);
    }
}
