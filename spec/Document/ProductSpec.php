<?php

namespace spec\Sylius\ElasticSearchPlugin\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\ElasticSearchPlugin\Document\Attribute;
use Sylius\ElasticSearchPlugin\Document\Product;
use PhpSpec\ObjectBehavior;

final class ProductSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Product::class);
    }

    function it_has_code()
    {
        $this->setCode('Mug');

        $this->getCode()->shouldReturn('Mug');
    }

    function it_has_name()
    {
        $this->setName('Big Mug');

        $this->getName()->shouldReturn('Big Mug');
    }

    function it_has_channel_code()
    {
        $this->setChannelCode('WEB');

        $this->getChannelCode()->shouldReturn('WEB');
    }

    function it_has_locale_code()
    {
        $this->setLocaleCode('en');

        $this->getLocaleCode()->shouldReturn('en');
    }

    function it_has_description()
    {
        $this->setDescription('Lorem ipsum');

        $this->getDescription()->shouldReturn('Lorem ipsum');
    }

    function it_has_price()
    {
        $this->setPrice(1000);

        $this->getPrice()->shouldReturn(1000);
    }

    function it_has_taxon_code()
    {
        $this->setTaxonCode('Tree');

        $this->getTaxonCode()->shouldReturn('Tree');
    }

    function it_has_attributes()
    {
        $attributes = new ArrayCollection([new Attribute()]);
        $this->setAttributes($attributes);

        $this->getAttributes()->shouldReturn($attributes);
    }
}
