<?php

declare(strict_types=1);

namespace Sylius\ElasticSearchPlugin\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use SimpleBus\Message\Bus\MessageBus;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslation;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductTaxon;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Component\Product\Model\ProductVariantTranslation;
use Sylius\Component\Product\Model\ProductVariantTranslationInterface;
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
    private $scheduledProducts = [];


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
    public function onFlush(OnFlushEventArgs $event)
    {
        $scheduledInsertions = $event->getEntityManager()->getUnitOfWork()->getScheduledEntityInsertions();

        foreach ($scheduledInsertions as $entity) {
            $entity = $this->getProductFromEntity($entity);
            if ($entity instanceof ProductInterface && !isset($this->scheduledProducts[$entity->getId()])) {
                $this->scheduledProducts[$entity->getId()] = $entity;
            }
        }

        $scheduledUpdates = $event->getEntityManager()->getUnitOfWork()->getScheduledEntityUpdates();

        foreach ($scheduledUpdates as $entity) {
            $entity = $this->getProductFromEntity($entity);
            if ($entity instanceof ProductInterface && !isset($this->scheduledUpdates[$entity->getId()])) {
                $this->scheduledUpdates[$entity->getId()] = $entity;
            }
        }

        $scheduledDeletions = $event->getEntityManager()->getUnitOfWork()->getScheduledEntityDeletions();

        foreach ($scheduledDeletions as $entity) {
            /** We delete only if the product itself was removed */
            if ($entity instanceof ProductInterface && !isset($this->scheduledDeletions[$entity->getId()])) {
                $this->scheduledDeletions[$entity->getId()] = $entity;
            }
        }
    }

    /**
     * @param PostFlushEventArgs $event
     */
    public function postFlush(PostFlushEventArgs $event)
    {
        foreach ($this->scheduledProducts as $product) {
            $this->eventBus->handle(ProductCreated::occur($product));
        }

        $this->scheduledProducts = [];

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
     * @return ProductInterface|\Sylius\Component\Product\Model\ProductInterface|\Sylius\Component\Resource\Model\TranslatableInterface
     */
    protected function getProductFromEntity($entity): ?ProductInterface
    {
        if ($entity instanceof ProductVariantInterface) {
            $entity = $entity->getProduct();
        } elseif ($entity instanceof ProductVariantTranslation) {
            $entity = $entity->getTranslatable()->getProduct();
        } elseif ($entity instanceof ProductTranslation) {
            $entity = $entity->getTranslatable();
        } elseif ($entity instanceof ChannelPricingInterface) {
            $entity = $entity->getProductVariant()->getProduct();
        } elseif ($entity instanceof ChannelPricingInterface) {
            $entity = $entity->getProductVariant()->getProduct();
        } elseif ($entity instanceof ProductTaxon) {
            $entity = $entity->getProduct();
        } elseif ($entity instanceof ProductAttributeValueInterface) {
            $entity = $entity->getProduct();
        } elseif ($entity instanceof ProductInterface) {
            return $entity;
        } else {
            $entity = null;
        }

        return $entity;
    }
}
