<?php

namespace spec\Sylius\ElasticSearchPlugin\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use SimpleBus\Message\Bus\MessageBus;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\ElasticSearchPlugin\Event\ProductCreated;
use Sylius\ElasticSearchPlugin\EventListener\ProductPublisher;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class ProductPublisherSpec extends ObjectBehavior
{
    function let(MessageBus $eventBus)
    {
        $this->beConstructedWith($eventBus);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProductPublisher::class);
    }

    function it_publishes_product_event(MessageBus $eventBus, LifecycleEventArgs $event, ProductInterface $product)
    {
        $event->getEntity()->willReturn($product);

        $eventBus->handle(ProductCreated::occur($product->getWrappedObject()))->shouldBeCalled();

        $this->postPersist($event);
    }

    function it_does_not_publish_product_event_if_entity_is_not_a_product(MessageBus $eventBus, LifecycleEventArgs $event)
    {
        $event->getEntity()->willReturn(new \stdClass());

        $eventBus->handle(Argument::any())->shouldNotBeCalled();

        $this->postPersist($event);
    }
}
