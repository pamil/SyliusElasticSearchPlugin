<?php

namespace spec\Sylius\ElasticSearchPlugin\Projection;

use Doctrine\Common\Collections\ArrayCollection;
use ONGR\ElasticsearchBundle\Service\Manager;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\ElasticSearchPlugin\Document\ProductDocument;
use Sylius\ElasticSearchPlugin\Event\ProductCreated;
use Sylius\ElasticSearchPlugin\Factory\Document\ProductDocumentFactoryInterface;
use Sylius\ElasticSearchPlugin\Projection\ProductProjector;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class ProductProjectorSpec extends ObjectBehavior
{
    function let(Manager $manager, ProductDocumentFactoryInterface $factory)
    {
        $this->beConstructedWith($manager, $factory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProductProjector::class);
    }

    function it_saves_product_document_if_product_has_channel_defined(
        Manager $manager,
        ProductDocumentFactoryInterface $factory,
        ProductInterface $product,
        LocaleInterface $locale,
        ChannelInterface $channel
    ) {
        $channel->getLocales()->willReturn(new ArrayCollection([$locale->getWrappedObject()]));
        $product->getChannels()->willReturn(new ArrayCollection([$channel->getWrappedObject()]));

        $productDocument = new ProductDocument();
        $factory->create($product, $locale, $channel)->willReturn($productDocument);

        $manager->persist($productDocument)->shouldBeCalled();
        $manager->commit()->shouldBeCalled();

        $this->handleProductCreated(ProductCreated::occur($product->getWrappedObject()));
    }

    function it_does_not_save_product_document_if_product_has_not_channel_defined(
        Manager $manager,
        ProductDocumentFactoryInterface $factory,
        ProductInterface $product
    ) {
        $product->getChannels()->willReturn(new ArrayCollection([]));
        $factory->create(Argument::any(), Argument::any(), Argument::any())->shouldNotBeCalled();

        $manager->persist(Argument::any())->shouldNotBeCalled();
        $manager->commit()->shouldBeCalled();

        $this->handleProductCreated(ProductCreated::occur($product->getWrappedObject()));
    }

    function it_does_not_save_product_document_if_channel_has_not_locales_defined(
        Manager $manager,
        ProductDocumentFactoryInterface $factory,
        ProductInterface $product,
        ChannelInterface $channel
    ) {
        $channel->getLocales()->willReturn(new ArrayCollection([]));
        $product->getChannels()->willReturn(new ArrayCollection([$channel->getWrappedObject()]));

        $factory->create(Argument::any(), Argument::any(), Argument::any())->shouldNotBeCalled();

        $manager->persist(Argument::any())->shouldNotBeCalled();
        $manager->commit()->shouldBeCalled();

        $this->handleProductCreated(ProductCreated::occur($product->getWrappedObject()));
    }
}
