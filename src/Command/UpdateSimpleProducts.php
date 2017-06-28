<?php

declare(strict_types=1);

namespace Sylius\ElasticSearchPlugin\Command;

use ONGR\ElasticsearchBundle\Service\Manager;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\ElasticSearchPlugin\Factory\ProductDocumentFactoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class UpdateSimpleProducts extends Command
{
    /**
     * @var ProductRepositoryInterface
     */
    private $syliusProductRepository;

    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var ProductDocumentFactoryInterface
     */
    private $productDocumentFactory;

    /**
     * @param ProductRepositoryInterface $syliusProductRepository
     * @param Manager $manager
     * @param ProductDocumentFactoryInterface $productDocumentFactory
     */
    public function __construct(
        ProductRepositoryInterface $syliusProductRepository,
        Manager $manager,
        ProductDocumentFactoryInterface $productDocumentFactory
    ) {
        $this->syliusProductRepository = $syliusProductRepository;
        $this->manager = $manager;
        $this->productDocumentFactory = $productDocumentFactory;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('sylius:elastic-search:update-simple-product')
            ->setDescription('Update simple products in elastic search index. Warning it will drop whole index')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->manager->dropAndCreateIndex();

        $syliusProducts = $this->syliusProductRepository->findAll();
        $syliusSimpleProducts = array_filter($syliusProducts, function (ProductInterface $product) {
            return $product->isSimple();
        });

        $productDocumentCreatedCount = 0;
        /** @var ProductInterface[] $syliusSimpleProducts */
        foreach ($syliusSimpleProducts as $syliusProduct) {
            $channels = $syliusProduct->getChannels();

            /** @var ChannelInterface $channel */
            foreach ($channels as $channel) {
                $locales = $channel->getLocales();
                foreach ($locales as $locale) {
                    $productDocument = $this->productDocumentFactory->createFromSyliusSimpleProductModel(
                        $syliusProduct,
                        $locale,
                        $channel
                    );

                    $productDocumentCreatedCount++;
                    $this->manager->persist($productDocument);
                }
            }
        }

        $this->manager->commit();

        $output->writeln(
            sprintf(
                'Index recreated, sylius simple products: %s, product documents created: %s',
                count($syliusSimpleProducts),
                $productDocumentCreatedCount
            )
        );
    }
}
