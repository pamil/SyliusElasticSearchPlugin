<?php

namespace Sylius\ElasticSearchPlugin\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use SimpleBus\Message\Bus\MessageBus;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\ElasticSearchPlugin\Event\ProductCreated;

final class ProductPublisher
{
    /**
     * @var MessageBus
     */
    private $eventBus;

    /**
     * @param MessageBus $eventBus
     */
    public function __construct(MessageBus $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postPersist(LifecycleEventArgs $event)
    {
        /** @var ProductInterface $product */
        $product = $event->getEntity();

        if ($product instanceof ProductInterface && $product->isSimple()) {
            $this->eventBus->handle(ProductCreated::occur($product));
        }
    }
}
