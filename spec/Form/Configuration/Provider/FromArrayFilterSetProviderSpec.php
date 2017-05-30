<?php

namespace spec\Sylius\ElasticSearchPlugin\Form\Configuration\Provider;

use Sylius\ElasticSearchPlugin\Exception\FilterSetConfigurationNotFoundException;
use Sylius\ElasticSearchPlugin\Form\Configuration\FilterSet;
use Sylius\ElasticSearchPlugin\Form\Configuration\Provider\FilterSetProviderInterface;
use Sylius\ElasticSearchPlugin\Form\Configuration\Provider\FromArrayFilterSetProvider;
use PhpSpec\ObjectBehavior;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class FromArrayFilterSetProviderSpec extends ObjectBehavior
{
    function let()
    {
        $configuration = ['mugs' => ['filters' => []]];

        $this->beConstructedWith($configuration);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FromArrayFilterSetProvider::class);
    }

    function it_is_filter_set_configuration_provider()
    {
        $this->shouldImplement(FilterSetProviderInterface::class);
    }

    function it_returns_filter_set_configuration()
    {
        $this->getFilterSetConfiguration('mugs')->shouldBeLike(FilterSet::createFromConfiguration('mugs', ['filters' => []]));
    }

    function it_throws_filter_set_configuration_not_found_exception_when_cannot_find_configuration()
    {
        $this->shouldThrow(FilterSetConfigurationNotFoundException::class)->during('getFilterSetConfiguration', ['default']);
    }
}
