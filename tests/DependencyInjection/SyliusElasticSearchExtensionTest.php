<?php

declare(strict_types=1);

namespace Tests\Sylius\ElasticSearchPlugin\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Sylius\ElasticSearchPlugin\DependencyInjection\SyliusElasticSearchExtension;
use Sylius\ElasticSearchPlugin\Document\AttributeDocument;
use Sylius\ElasticSearchPlugin\Document\AttributeValueDocument;
use Sylius\ElasticSearchPlugin\Document\ImageDocument;
use Sylius\ElasticSearchPlugin\Document\PriceDocument;
use Sylius\ElasticSearchPlugin\Document\ProductDocument;
use Sylius\ElasticSearchPlugin\Document\TaxonDocument;

final class SyliusElasticSearchExtensionTest extends AbstractExtensionTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions()
    {
        return [new SyliusElasticSearchExtension()];
    }

    /**
     * @test
     */
    public function it_defines_document_classes_parameters()
    {
        $this->load([]);

        $this->assertContainerBuilderHasParameter('sylius_elastic_search.document.product.class', ProductDocument::class);
        $this->assertContainerBuilderHasParameter('sylius_elastic_search.document.attribute.class', AttributeDocument::class);
        $this->assertContainerBuilderHasParameter('sylius_elastic_search.document.attribute_value.class', AttributeValueDocument::class);
        $this->assertContainerBuilderHasParameter('sylius_elastic_search.document.image.class', ImageDocument::class);
        $this->assertContainerBuilderHasParameter('sylius_elastic_search.document.price.class', PriceDocument::class);
        $this->assertContainerBuilderHasParameter('sylius_elastic_search.document.taxon.class', TaxonDocument::class);
    }
}
