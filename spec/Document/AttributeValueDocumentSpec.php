<?php

namespace spec\Sylius\ElasticSearchPlugin\Document;

use Sylius\ElasticSearchPlugin\Document\AttributeDocument;
use Sylius\ElasticSearchPlugin\Document\AttributeValueDocument;
use PhpSpec\ObjectBehavior;

final class AttributeValueDocumentSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AttributeValueDocument::class);
    }

    function it_has_value()
    {
        $this->setValue('Red');

        $this->getValue()->shouldReturn('Red');
    }

    function it_has_attribute()
    {
        $attribute = new AttributeDocument();
        $this->setAttribute($attribute);

        $this->getAttribute()->shouldReturn($attribute);
    }
}
