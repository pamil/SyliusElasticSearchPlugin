<?php

namespace spec\Sylius\ElasticSearchPlugin\Command;

use ONGR\ElasticsearchBundle\Service\Manager;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\ElasticSearchPlugin\Command\UpdateSimpleProducts;
use PhpSpec\ObjectBehavior;
use Sylius\ElasticSearchPlugin\Document\ProductDocument;
use Sylius\ElasticSearchPlugin\Factory\ProductDocumentFactoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class UpdateSimpleProductsSpec extends ObjectBehavior
{
    function let(
        ProductRepositoryInterface $syliusProductRepository,
        Manager $manager,
        ProductDocumentFactoryInterface $productDocumentFactory
    ) {
        $this->beConstructedWith($syliusProductRepository, $manager, $productDocumentFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UpdateSimpleProducts::class);
    }

    function it_is_symfony_command()
    {
        $this->shouldHaveType(Command::class);
    }

    function it_recreates_index_with_fresh_product_documents(
        ProductRepositoryInterface $syliusProductRepository,
        Manager $manager,
        ProductDocumentFactoryInterface $productDocumentFactory,
        ProductDocument $productDocument,
        ProductInterface $product,
        ChannelInterface $channel,
        LocaleInterface $locale,
        InputInterface $input,
        OutputInterface $output
    ) {
        $manager->dropAndCreateIndex()->shouldBeCalled();
        $syliusProductRepository->findAll()->willReturn([$product]);
        $product->isSimple()->willReturn(true);
        $product->getChannels()->willReturn([$channel]);
        $channel->getLocales()->willReturn([$locale]);

        $productDocumentFactory
            ->createFromSyliusSimpleProductModel($product, $locale, $channel)
            ->willReturn($productDocument)
        ;

        $manager->persist($productDocument)->shouldBeCalled();
        $manager->commit()->shouldBeCalled();

        $output->writeln('Index recreated, sylius simple products: 1, product documents created: 1')->shouldBeCalled();

        $this->run($input, $output);
    }

    function it_recreates_index_with_fresh_only_simple_product_documents(
        ProductRepositoryInterface $syliusProductRepository,
        Manager $manager,
        ProductDocumentFactoryInterface $productDocumentFactory,
        ProductDocument $productDocument,
        ProductInterface $simpleProduct,
        ProductInterface $notSimpleProduct,
        ChannelInterface $channel,
        LocaleInterface $locale,
        InputInterface $input,
        OutputInterface $output
    ) {
        $manager->dropAndCreateIndex()->shouldBeCalled();
        $syliusProductRepository->findAll()->willReturn([$simpleProduct, $notSimpleProduct]);
        $notSimpleProduct->isSimple()->willReturn(false);
        $simpleProduct->isSimple()->willReturn(true);
        $simpleProduct->getChannels()->willReturn([$channel]);
        $channel->getLocales()->willReturn([$locale]);

        $productDocumentFactory
            ->createFromSyliusSimpleProductModel($simpleProduct, $locale, $channel)
            ->willReturn($productDocument)
        ;

        $manager->persist($productDocument)->shouldBeCalled();
        $manager->commit()->shouldBeCalled();

        $output->writeln('Index recreated, sylius simple products: 1, product documents created: 1')->shouldBeCalled();

        $this->run($input, $output);
    }

    function it_recreates_index_with_fresh_product_documents_for_many_channels_and_locales(
        ProductRepositoryInterface $syliusProductRepository,
        Manager $manager,
        ProductDocumentFactoryInterface $productDocumentFactory,
        ProductDocument $productDocument,
        ProductInterface $simpleProduct,
        ProductInterface $notSimpleProduct,
        ChannelInterface $firstChannel,
        ChannelInterface $secondChannel,
        LocaleInterface $firstLocale,
        LocaleInterface $secondLocale,
        InputInterface $input,
        OutputInterface $output
    ) {
        $manager->dropAndCreateIndex()->shouldBeCalled();
        $syliusProductRepository->findAll()->willReturn([$simpleProduct, $notSimpleProduct]);
        $notSimpleProduct->isSimple()->willReturn(false);
        $simpleProduct->isSimple()->willReturn(true);
        $simpleProduct->getChannels()->willReturn([$firstChannel, $secondChannel]);
        $firstChannel->getLocales()->willReturn([$firstLocale, $secondLocale]);
        $secondChannel->getLocales()->willReturn([$firstLocale, $secondLocale]);

        $productDocumentFactory
            ->createFromSyliusSimpleProductModel($simpleProduct, $firstLocale, $firstChannel)
            ->willReturn($productDocument)
        ;

        $productDocumentFactory
            ->createFromSyliusSimpleProductModel($simpleProduct, $secondLocale, $firstChannel)
            ->willReturn($productDocument)
        ;

        $productDocumentFactory
            ->createFromSyliusSimpleProductModel($simpleProduct, $firstLocale, $secondChannel)
            ->willReturn($productDocument)
        ;

        $productDocumentFactory
            ->createFromSyliusSimpleProductModel($simpleProduct, $secondLocale, $secondChannel)
            ->willReturn($productDocument)
        ;

        $manager->persist($productDocument)->shouldBeCalled();
        $manager->commit()->shouldBeCalled();

        $output->writeln('Index recreated, sylius simple products: 1, product documents created: 4')->shouldBeCalled();

        $this->run($input, $output);
    }
}
