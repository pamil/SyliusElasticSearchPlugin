<?php

namespace Sylius\ElasticSearchPlugin\Form\Configuration\Provider;

use Sylius\ElasticSearchPlugin\Exception\FilterSetConfigurationNotFoundException;
use Zend\Stdlib\PriorityQueue;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class FilterSetProvider implements FilterSetProviderInterface
{
    /**
     * @var PriorityQueue
     */
    private $filterSetProviders;

    public function __construct()
    {
        $this->filterSetProviders = new PriorityQueue();
    }

    /**
     * @param FilterSetProviderInterface $filterSetProvider
     * @param int $priority
     */
    public function addFilterSetProvider(FilterSetProviderInterface $filterSetProvider, $priority = 1)
    {
        $this->filterSetProviders->insert($filterSetProvider, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterSetConfiguration($filterSetName)
    {
        /** @var FilterSetProviderInterface $filterSetProvider */
        foreach ($this->filterSetProviders as $filterSetProvider) {
            try {
                return $filterSetProvider->getFilterSetConfiguration($filterSetName);
            } catch (FilterSetConfigurationNotFoundException $configurationNotFoundException) {
                continue;
            }
        }

        throw new FilterSetConfigurationNotFoundException();
    }
}
