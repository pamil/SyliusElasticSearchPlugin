<?php

namespace spec\Sylius\ElasticSearchPlugin\Document;

use ONGR\ElasticsearchBundle\Collection\Collection;
use Sylius\ElasticSearchPlugin\Document\Price;
use Sylius\ElasticSearchPlugin\Document\Product;
use PhpSpec\ObjectBehavior;
use Sylius\ElasticSearchPlugin\Document\TaxonCode;

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
        $price = new Price();
        $this->setPrice($price);

        $this->getPrice()->shouldReturn($price);
    }

    function it_has_main_taxon_code()
    {
        $taxonCode = new TaxonCode();
        $this->setMainTaxonCode($taxonCode);

        $this->getMainTaxonCode()->shouldReturn($taxonCode);
    }

    function it_has_taxon_codes()
    {
        $taxonCodes = new Collection();
        $this->setTaxonCodes($taxonCodes);

        $this->getTaxonCodes()->shouldReturn($taxonCodes);
    }

    function it_has_attributes()
    {
        $attributeValues = new Collection();
        $this->setAttributeValues($attributeValues);

        $this->getAttributeValues()->shouldReturn($attributeValues);
    }
}
