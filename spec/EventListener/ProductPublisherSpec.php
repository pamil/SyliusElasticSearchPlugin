<?php

declare(strict_types=1);

namespace spec\Sylius\ElasticSearchPlugin\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SimpleBus\Message\Bus\MessageBus;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductVariantTranslationInterface;
use Sylius\ElasticSearchPlugin\Event\ProductCreated;
use Sylius\ElasticSearchPlugin\Event\ProductDeleted;
use Sylius\ElasticSearchPlugin\Event\ProductUpdated;
use Sylius\ElasticSearchPlugin\EventListener\ProductPublisher;

final class ProductPublisherSpec extends ObjectBehavior
{
    function let(
        MessageBus $eventBus,
        OnFlushEventArgs $onFlushEvent,
        PostFlushEventArgs $postFlushEvent,
        EntityManager $entityManager,
        UnitOfWork $unitOfWork,
        ProductInterface $product
    ): void {
        $this->beConstructedWith($eventBus);

        $onFlushEvent->getEntityManager()->willReturn($entityManager);
        $postFlushEvent->getEntityManager()->willReturn($entityManager);

        $entityManager->getUnitOfWork()->willReturn($unitOfWork);

        $unitOfWork->getScheduledEntityInsertions()->willReturn([]);
        $unitOfWork->getScheduledEntityUpdates()->willReturn([]);
        $unitOfWork->getScheduledEntityDeletions()->willReturn([]);

        $product->getCode()->willReturn('PRODUCT_CODE');
    }

    function it_publishes_product_created_event_when_product_is_created(
        MessageBus $eventBus,
        OnFlushEventArgs $onFlushEvent,
        PostFlushEventArgs $postFlushEvent,
        UnitOfWork $unitOfWork,
        ProductInterface $product
    ): void {
        $unitOfWork->getScheduledEntityInsertions()->willReturn([$product]);

        $eventBus->handle(ProductCreated::occur($product->getWrappedObject()))->shouldBeCalled();

        $this->onFlush($onFlushEvent);
        $this->postFlush($postFlushEvent);
    }

    function it_publishes_product_updated_event_when_product_is_updated(
        MessageBus $eventBus,
        OnFlushEventArgs $onFlushEvent,
        PostFlushEventArgs $postFlushEvent,
        UnitOfWork $unitOfWork,
        ProductInterface $product
    ): void {
        $unitOfWork->getScheduledEntityUpdates()->willReturn([$product]);

        $eventBus->handle(ProductUpdated::occur($product->getWrappedObject()))->shouldBeCalled();

        $this->onFlush($onFlushEvent);
        $this->postFlush($postFlushEvent);
    }

    function it_publishes_product_updated_event_when_product_translation_is_updated(
        MessageBus $eventBus,
        OnFlushEventArgs $onFlushEvent,
        PostFlushEventArgs $postFlushEvent,
        UnitOfWork $unitOfWork,
        ProductTranslationInterface $productTranslation,
        ProductInterface $product
    ): void {
        $unitOfWork->getScheduledEntityUpdates()->willReturn([$productTranslation]);

        $productTranslation->getTranslatable()->willReturn($product);

        $eventBus->handle(ProductUpdated::occur($product->getWrappedObject()))->shouldBeCalled();

        $this->onFlush($onFlushEvent);
        $this->postFlush($postFlushEvent);
    }

    function it_publishes_product_updated_event_when_product_variant_is_updated(
        MessageBus $eventBus,
        OnFlushEventArgs $onFlushEvent,
        PostFlushEventArgs $postFlushEvent,
        UnitOfWork $unitOfWork,
        ProductVariantInterface $productVariant,
        ProductInterface $product
    ): void {
        $unitOfWork->getScheduledEntityUpdates()->willReturn([$productVariant]);

        $productVariant->getProduct()->willReturn($product);

        $eventBus->handle(ProductUpdated::occur($product->getWrappedObject()))->shouldBeCalled();

        $this->onFlush($onFlushEvent);
        $this->postFlush($postFlushEvent);
    }

    function it_publishes_product_updated_event_when_product_variant_translation_is_updated(
        MessageBus $eventBus,
        OnFlushEventArgs $onFlushEvent,
        PostFlushEventArgs $postFlushEvent,
        UnitOfWork $unitOfWork,
        ProductVariantTranslationInterface $productVariantTranslation,
        ProductVariantInterface $productVariant,
        ProductInterface $product
    ): void {
        $unitOfWork->getScheduledEntityUpdates()->willReturn([$productVariantTranslation]);

        $productVariantTranslation->getTranslatable()->willReturn($productVariant);
        $productVariant->getProduct()->willReturn($product);

        $eventBus->handle(ProductUpdated::occur($product->getWrappedObject()))->shouldBeCalled();

        $this->onFlush($onFlushEvent);
        $this->postFlush($postFlushEvent);
    }

    function it_publishes_product_updated_event_when_product_variant_channel_pricing_is_updated(
        MessageBus $eventBus,
        OnFlushEventArgs $onFlushEvent,
        PostFlushEventArgs $postFlushEvent,
        UnitOfWork $unitOfWork,
        ChannelPricingInterface $productVariantChannelPricing,
        ProductVariantInterface $productVariant,
        ProductInterface $product
    ): void {
        $unitOfWork->getScheduledEntityUpdates()->willReturn([$productVariantChannelPricing]);

        $productVariantChannelPricing->getProductVariant()->willReturn($productVariant);
        $productVariant->getProduct()->willReturn($product);

        $eventBus->handle(ProductUpdated::occur($product->getWrappedObject()))->shouldBeCalled();

        $this->onFlush($onFlushEvent);
        $this->postFlush($postFlushEvent);
    }

    function it_publishes_product_updated_event_when_product_taxon_is_updated(
        MessageBus $eventBus,
        OnFlushEventArgs $onFlushEvent,
        PostFlushEventArgs $postFlushEvent,
        UnitOfWork $unitOfWork,
        ProductTaxonInterface $productTaxon,
        ProductInterface $product
    ): void {
        $unitOfWork->getScheduledEntityUpdates()->willReturn([$productTaxon]);

        $productTaxon->getProduct()->willReturn($product);

        $eventBus->handle(ProductUpdated::occur($product->getWrappedObject()))->shouldBeCalled();

        $this->onFlush($onFlushEvent);
        $this->postFlush($postFlushEvent);
    }

    function it_publishes_product_deleted_event_when_product_is_deleted(
        MessageBus $eventBus,
        OnFlushEventArgs $onFlushEvent,
        PostFlushEventArgs $postFlushEvent,
        UnitOfWork $unitOfWork,
        ProductInterface $product
    ): void {
        $unitOfWork->getScheduledEntityDeletions()->willReturn([$product]);

        $eventBus->handle(ProductDeleted::occur($product->getWrappedObject()))->shouldBeCalled();

        $this->onFlush($onFlushEvent);
        $this->postFlush($postFlushEvent);
    }
}
