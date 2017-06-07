<?php

namespace spec\Sylius\ElasticSearchPlugin\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use SimpleBus\Message\Bus\MessageBus;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\ElasticSearchPlugin\Event\ProductUpdated;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class ProductUpdatedPublisherSpec extends ObjectBehavior
{
    function let(MessageBus $eventBus)
    {
        $this->beConstructedWith($eventBus);
    }

    function it_publishes_product_updated_events(
        MessageBus $eventBus,
        OnFlushEventArgs $event,
        ProductInterface $product,
        EntityManager $entityManager,
        UnitOfWork $unitOfWork
    ) {
        $product->isSimple()->willReturn(true);

        $event->getEntityManager()->willReturn($entityManager);
        $entityManager->getUnitOfWork()->willReturn($unitOfWork);

        $unitOfWork->getScheduledEntityUpdates()->willReturn([$product]);

        $eventBus->handle(ProductUpdated::occur($product->getWrappedObject()))->shouldBeCalled();

        $this->onFlush($event);
    }

    function it_does_not_publish_product_updated_event_if_entity_is_not_a_product(
        MessageBus $eventBus,
        OnFlushEventArgs $event,
        EntityManager $entityManager,
        UnitOfWork $unitOfWork
    ) {
        $event->getEntityManager()->willReturn($entityManager);
        $entityManager->getUnitOfWork()->willReturn($unitOfWork);

        $unitOfWork->getScheduledEntityUpdates()->willReturn([new \stdClass()]);

        $eventBus->handle(Argument::cetera())->shouldNotBeCalled();

        $this->onFlush($event);
    }

    function it_does_not_publish_product_updated_event_if_entity_is_not_a_simple_product(
        MessageBus $eventBus,
        OnFlushEventArgs $event,
        EntityManager $entityManager,
        UnitOfWork $unitOfWork,
        ProductInterface $product
    ) {
        $product->isSimple()->willReturn(false);

        $event->getEntityManager()->willReturn($entityManager);
        $entityManager->getUnitOfWork()->willReturn($unitOfWork);

        $unitOfWork->getScheduledEntityUpdates()->willReturn([$product]);

        $eventBus->handle(Argument::cetera())->shouldNotBeCalled();

        $this->onFlush($event);
    }
}
