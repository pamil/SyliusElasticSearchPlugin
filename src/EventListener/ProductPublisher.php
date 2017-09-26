<?php

declare(strict_types=1);

namespace Sylius\ElasticSearchPlugin\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use SimpleBus\Message\Bus\MessageBus;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxon;
use Sylius\Component\Core\Model\ProductTranslation;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Component\Product\Model\ProductVariantTranslation;
use Sylius\ElasticSearchPlugin\Event\ProductCreated;
use Sylius\ElasticSearchPlugin\Event\ProductDeleted;
use Sylius\ElasticSearchPlugin\Event\ProductUpdated;

final class ProductPublisher
{
    /**
     * @var MessageBus
     */
    private $eventBus;

    /**
     * @var ProductInterface[]
     */
    private $scheduledInsertions = [];

    /**
     * @var ProductInterface[]
     */
    private $scheduledUpdates = [];

    /**
     * @var ProductInterface[]
     */
    private $scheduledDeletions = [];

    /**
     * @param MessageBus $eventBus
     */
    public function __construct(MessageBus $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    /**
     * @param OnFlushEventArgs $event
     */
    public function onFlush(OnFlushEventArgs $event): void
    {
        $scheduledInsertions = $event->getEntityManager()->getUnitOfWork()->getScheduledEntityInsertions();
        foreach ($scheduledInsertions as $entity) {
            $entity = $this->getProductFromEntity($entity);
            if ($entity instanceof ProductInterface && !isset($this->scheduledInsertions[$entity->getCode()])) {
                $this->scheduledInsertions[$entity->getCode()] = $entity;
            }
        }

        $scheduledUpdates = $event->getEntityManager()->getUnitOfWork()->getScheduledEntityUpdates();
        foreach ($scheduledUpdates as $entity) {
            $entity = $this->getProductFromEntity($entity);
            if ($entity instanceof ProductInterface && !isset($this->scheduledUpdates[$entity->getCode()])) {
                $this->scheduledUpdates[$entity->getCode()] = $entity;
            }
        }

        $scheduledDeletions = $event->getEntityManager()->getUnitOfWork()->getScheduledEntityDeletions();
        foreach ($scheduledDeletions as $entity) {
            if ($entity instanceof ProductInterface && !isset($this->scheduledDeletions[$entity->getCode()])) {
                $this->scheduledDeletions[$entity->getCode()] = $entity;
            }
        }
    }

    /**
     * @param PostFlushEventArgs $event
     */
    public function postFlush(PostFlushEventArgs $event): void
    {
        foreach ($this->scheduledInsertions as $product) {
            $this->eventBus->handle(ProductCreated::occur($product));
        }

        $this->scheduledInsertions = [];

        foreach ($this->scheduledUpdates as $product) {
            $this->eventBus->handle(ProductUpdated::occur($product));
        }

        $this->scheduledUpdates = [];

        foreach ($this->scheduledDeletions as $product) {
            $this->eventBus->handle(ProductDeleted::occur($product));
        }

        $this->scheduledDeletions = [];
    }

    /**
     * @param $entity
     *
     * @return ProductInterface
     */
    private function getProductFromEntity($entity): ?ProductInterface
    {
        if ($entity instanceof ProductVariantInterface) {
            return $entity->getProduct();
        }

        if ($entity instanceof ProductVariantTranslation) {
            return $entity->getTranslatable()->getProduct();
        }

        if ($entity instanceof ProductTranslation) {
            return $entity->getTranslatable();
        }

        if ($entity instanceof ChannelPricingInterface) {
            return $this->getProductFromEntity($entity->getProductVariant());
        }

        if ($entity instanceof ProductTaxon) {
            return $entity->getProduct();
        }

        if ($entity instanceof ProductAttributeValueInterface) {
            return $entity->getProduct();
        }

        if ($entity instanceof ProductInterface) {
            return $entity;
        }

        return null;
    }
}
