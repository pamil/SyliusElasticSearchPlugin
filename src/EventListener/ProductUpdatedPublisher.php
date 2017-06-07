<?php

namespace Sylius\ElasticSearchPlugin\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use SimpleBus\Message\Bus\MessageBus;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\ElasticSearchPlugin\Event\ProductUpdated;

final class ProductUpdatedPublisher
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
     * @param OnFlushEventArgs $event
     */
    public function onFlush(OnFlushEventArgs $event)
    {
        $scheduledInsertions = $event->getEntityManager()->getUnitOfWork()->getScheduledEntityUpdates();

        foreach ($scheduledInsertions as $entity) {
            if ($entity instanceof ProductInterface && $entity->isSimple()) {
                $this->eventBus->handle(ProductUpdated::occur($entity));
            }
        }
    }
}
