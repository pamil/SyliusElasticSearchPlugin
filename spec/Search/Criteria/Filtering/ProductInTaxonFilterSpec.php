<?php

namespace spec\Sylius\ElasticSearchPlugin\Search\Criteria\Filtering;

use Sylius\ElasticSearchPlugin\Search\Criteria\Filtering\ProductInTaxonFilter;
use PhpSpec\ObjectBehavior;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ProductInTaxonFilterSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('mugs');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProductInTaxonFilter::class);
    }

    function it_has_immutable_taxon_code()
    {
        $this->getTaxonCode()->shouldReturn('mugs');
    }
}
