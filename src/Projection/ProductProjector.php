<?php

namespace Sylius\ElasticSearchPlugin\Projection;

use ONGR\ElasticsearchBundle\Service\Manager;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\ElasticSearchPlugin\Event\ProductCreated;
use Sylius\ElasticSearchPlugin\Factory\ProductFactoryInterface;

final class ProductProjector
{
    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var ProductFactoryInterface
     */
    private $productDocumentFactory;

    /**
     * @param Manager $manager
     * @param ProductFactoryInterface $productDocumentFactory
     */
    public function __construct(Manager $manager, ProductFactoryInterface $productDocumentFactory)
    {
        $this->manager = $manager;
        $this->productDocumentFactory = $productDocumentFactory;
    }

    /**
     * @param ProductCreated $event
     */
    public function handleProductCreated(ProductCreated $event)
    {
        $product = $event->product();
        $channels = $product->getChannels();

        /** @var ChannelInterface $channel */
        foreach ($channels as $channel) {
            $locales = $channel->getLocales();
            foreach ($locales as $locale) {
                $productDocument = $this->productDocumentFactory->createFromSyliusSimpleProductModel(
                    $product,
                    $locale,
                    $channel
                );

                $this->manager->persist($productDocument);
            }
        }

        $this->manager->commit();
    }
}
