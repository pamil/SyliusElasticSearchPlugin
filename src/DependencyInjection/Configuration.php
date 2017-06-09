<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\ElasticSearchPlugin\DependencyInjection;

use Sylius\ElasticSearchPlugin\Document\AttributeDocument;
use Sylius\ElasticSearchPlugin\Document\AttributeValueDocument;
use Sylius\ElasticSearchPlugin\Document\ImageDocument;
use Sylius\ElasticSearchPlugin\Document\PriceDocument;
use Sylius\ElasticSearchPlugin\Document\ProductDocument;
use Sylius\ElasticSearchPlugin\Document\TaxonDocument;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_elastic_search');

        $this->buildFilterSetNode($rootNode);
        $this->buildDocumentClassesNode($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function buildDocumentClassesNode(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('document_classes')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('product')->defaultValue(ProductDocument::class)->end()
                        ->scalarNode('attribute')->defaultValue(AttributeDocument::class)->end()
                        ->scalarNode('attribute_value')->defaultValue(AttributeValueDocument::class)->end()
                        ->scalarNode('image')->defaultValue(ImageDocument::class)->end()
                        ->scalarNode('price')->defaultValue(PriceDocument::class)->end()
                        ->scalarNode('taxon')->defaultValue(TaxonDocument::class)->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function buildFilterSetNode(ArrayNodeDefinition $rootNode)
    {
        $filterSetNode = $rootNode
            ->children()
                ->arrayNode('filter_sets')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
            ->validate()
                ->ifEmpty()
                ->thenInvalid('"%s" cannot be empty')
            ->end()
        ;

        $this->buildFiltersNode($filterSetNode);
    }

    /**
     * @param ArrayNodeDefinition $filterSetNode
     */
    private function buildFiltersNode(ArrayNodeDefinition $filterSetNode)
    {
        $filtersNode = $filterSetNode
            ->children()
                ->arrayNode('filters')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
            ->validate()
                ->ifEmpty()
                ->thenInvalid('"%s"" cannot be empty')
            ->end()
        ;

        $this->buildFilterNode($filtersNode);
    }

    /**
     * @param ArrayNodeDefinition $filtersNode
     */
    private function buildFilterNode(ArrayNodeDefinition $filtersNode)
    {
        $filtersNode
            ->children()
                ->scalarNode('type')->cannotBeEmpty()->end()
                ->arrayNode('options')
                    ->useAttributeAsKey('name')
                    ->prototype('scalar')
        ;
    }
}
