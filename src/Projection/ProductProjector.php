<?php

declare(strict_types=1);

namespace Sylius\ElasticSearchPlugin\Projection;

use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use ONGR\ElasticsearchBundle\Service\Manager;
use ONGR\ElasticsearchBundle\Service\Repository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\ElasticSearchPlugin\Document\ProductDocument;
use Sylius\ElasticSearchPlugin\Event\ProductCreated;
use Sylius\ElasticSearchPlugin\Event\ProductDeleted;
use Sylius\ElasticSearchPlugin\Event\ProductUpdated;
use Sylius\ElasticSearchPlugin\Factory\ProductDocumentFactoryInterface;

final class ProductProjector
{
    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var ProductDocumentFactoryInterface
     */
    private $productDocumentFactory;

    /**
     * @var Repository
     */
    private $productDocumentRepository;

    /**
     * @param Manager $manager
     * @param ProductDocumentFactoryInterface $productDocumentFactory
     */
    public function __construct(Manager $manager, ProductDocumentFactoryInterface $productDocumentFactory)
    {
        $this->manager = $manager;
        $this->productDocumentFactory = $productDocumentFactory;
        $this->productDocumentRepository = $this->manager->getRepository(ProductDocument::class);;
    }

    /**
     * @param ProductCreated $event
     */
    public function handleProductCreated(ProductCreated $event): void
    {
        $product = $event->product();
        $channels = $product->getChannels();

        /** @var ChannelInterface $channel */
        foreach ($channels as $channel) {
            $locales = $channel->getLocales();
            foreach ($locales as $locale) {
                $productDocument = $this->productDocumentFactory->create(
                    $product,
                    $locale,
                    $channel
                );

                $this->manager->persist($productDocument);
            }
        }

        $this->manager->commit();
    }

    /**
     * We create a new product documents with updated data and remove old once
     *
     * @param ProductUpdated $event
     */
    public function handleProductUpdated(ProductUpdated $event): void
    {
        $product = $event->product();

        $this->scheduleCreatingNewProductDocument($product);
        $this->scheduleRemovingOldProductDocuments($product);

        $this->manager->commit();
    }

    /**
     * We remove deleted product
     *
     * @param ProductDeleted $event
     */
    public function handleProductDeleted(ProductDeleted $event): void
    {
        $product = $event->product();

        $this->scheduleRemovingOldProductDocuments($product);

        $this->manager->commit();
    }

    protected function scheduleCreatingNewProductDocument(ProductInterface $product): void
    {
        /** @var ChannelInterface[] $channels */
        $channels = $product->getChannels();

        foreach ($channels as $channel) {
            /** @var LocaleInterface[] $locales */
            $locales = $channel->getLocales();

            foreach ($locales as $locale) {
                $this->manager->persist($this->productDocumentFactory->create(
                    $product,
                    $locale,
                    $channel
                ));
            }
        }
    }

    protected function scheduleRemovingOldProductDocuments(ProductInterface $product): void
    {
        /** @var DocumentIterator|ProductDocument[] $currentProductDocuments */
        $currentProductDocuments = $this->productDocumentRepository->findBy(['id' => $product->getId()]);

        foreach ($currentProductDocuments as $sameCodeProductDocument) {
            $this->manager->remove($sameCodeProductDocument);
        }
    }
}
