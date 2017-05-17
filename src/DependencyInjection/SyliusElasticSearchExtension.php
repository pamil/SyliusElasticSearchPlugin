<?php

namespace Sylius\ElasticSearchPlugin\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SyliusElasticSearchExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('services.xml');
        $this->createFilterSetsParameter($config['filter_sets'], $container);
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function createFilterSetsParameter(array $config, ContainerBuilder $container)
    {
        $container->setParameter('lakion_sylius_elastic_search.filter_sets', $config);
    }
}
