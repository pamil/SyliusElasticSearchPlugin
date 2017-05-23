<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Sylius\ElasticSearchPlugin\Behat\Context\Domain\Shop;

use Behat\Behat\Context\Context;
use ONGR\ElasticsearchBundle\Result\DocumentIterator;
use Sylius\ElasticSearchPlugin\Document\Product;
use Sylius\ElasticSearchPlugin\Search\Criteria\Criteria;
use Sylius\ElasticSearchPlugin\Search\Criteria\Filtering\ProductHasOptionCodesFilter;
use Sylius\ElasticSearchPlugin\Search\Criteria\Filtering\ProductInChannelFilter;
use Sylius\ElasticSearchPlugin\Search\Criteria\Filtering\ProductInPriceRangeFilter;
use Sylius\ElasticSearchPlugin\Search\Criteria\Filtering\ProductInTaxonFilter;
use Sylius\ElasticSearchPlugin\Search\Criteria\SearchPhrase;
use Sylius\ElasticSearchPlugin\Search\SearchEngineInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Webmozart\Assert\Assert;

final class ProductContext implements Context
{
    /**
     * @var SearchEngineInterface
     */
    private $searchEngine;

    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @param SearchEngineInterface $searchEngine
     * @param SharedStorageInterface $sharedStorage
     */
    public function __construct(SearchEngineInterface $searchEngine, SharedStorageInterface $sharedStorage)
    {
        $this->searchEngine = $searchEngine;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @When I filter them by :mugTypeValue mug type
     */
    public function iFilterThemByDoubleMugType($mugTypeValue)
    {
        $criteria = Criteria::fromQueryParameters(Product::class, [
            new ProductHasOptionCodesFilter([sprintf('mug_type_%s', $mugTypeValue)]),
        ]);

        $this->match($criteria);
    }

    /**
     * @When I filter them by :mugTypeValue mug type or sticker size :stickerSizeValue
     */
    public function iFilterThemByDoubleMugTypeAndStickerSize($mugTypeValue, $stickerSizeValue)
    {
        $criteria = Criteria::fromQueryParameters(Product::class, [
            new ProductHasOptionCodesFilter([sprintf('mug_type_%s', $mugTypeValue)]),
            new ProductHasOptionCodesFilter([sprintf('sticker_size_%s', $stickerSizeValue)]),
        ]);

        $this->match($criteria);
    }

    /**
     * @When I filter them by stickier size :stickerSizeValue
     */
    public function iFilterThemByStickierSize($stickerSizeValue)
    {
        $criteria = Criteria::fromQueryParameters(Product::class, [
            new ProductHasOptionCodesFilter([sprintf('sticker_size_%s', $stickerSizeValue)]),
        ]);

        $this->match($criteria);
    }

    /**
     * @When /^I filter them by price between ("[^"]+") and ("[^"]+")$/
     */
    public function iFilterThemByPriceBetweenAnd($graterThan, $lessThan)
    {
        $criteria = Criteria::fromQueryParameters(Product::class, [new ProductInPriceRangeFilter($graterThan, $lessThan)]);
        $this->match($criteria);
    }

    /**
     * @When I view the list of the products without filtering
     */
    public function iViewTheListOfTheProductsWithoutFiltering()
    {
        $criteria = Criteria::fromQueryParameters(Product::class, []);
        $this->match($criteria);
    }

    /**
     * @When I filter them by channel :channelCode
     */
    public function iFilterThemByChannel($channelCode)
    {
        $criteria = Criteria::fromQueryParameters(Product::class, [new ProductInChannelFilter($channelCode)]);
        $this->match($criteria);
    }

    /**
     * @When /^I filter them by channel "([^"]+)" and price between ("[^"]+") and ("[^"]+")$/
     */
    public function iFilterThemByChannelAndPriceBetweenAnd($channelCode, $graterThan, $lessThan)
    {
        $criteria = Criteria::fromQueryParameters(Product::class, [
            new ProductInChannelFilter($channelCode),
            new ProductInPriceRangeFilter($graterThan, $lessThan),
        ]);

        $this->match($criteria);
    }

    /**
     * @When I filter them by :taxonCode taxon
     */
    public function iFilterThemByTaxon($taxonCode)
    {
        $criteria = Criteria::fromQueryParameters(Product::class, [new ProductInTaxonFilter($taxonCode)]);
        $this->match($criteria);
    }

    /**
     * @When I sort them by :field in :order order
     */
    public function iSortThemByNameInAscendingOrder($field, $order)
    {
        if ('descending' === $order) {
            $field = '-' . $field;
        }

        $criteria = Criteria::fromQueryParameters(Product::class, ['sort' => $field]);
        $this->match($criteria);
    }

    /**
     * @When I search for products with name :name
     */
    public function iSearchForProductsWithName($name)
    {
        $criteria = Criteria::fromQueryParameters(Product::class, [new SearchPhrase($name)]);
        $this->match($criteria);
    }

    /**
     * @Then I should see :numberOfProducts products on the list
     */
    public function iShouldSeeProductsOnTheList($numberOfProducts)
    {
        /** @var DocumentIterator $result */
        $result = $this->sharedStorage->get('search_result');

        Assert::eq($result->count(), $numberOfProducts);
    }

    /**
     * @Then /^I should see products in order like "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)"$/
     */
    public function iShouldSeeProductsInOrderLike(...$productNames)
    {
        /** @var DocumentIterator $searchResult */
        $searchResult = $this->sharedStorage->get('search_result');

        /**
         * @var int $position
         * @var Product $product
         */
        foreach ($searchResult as $position => $product) {
            if ($product->getName() !== $productNames[$position]) {
                throw new \RuntimeException(
                    sprintf(
                        'Sorting failed at position "%s" expected value was "%s", but got "%s"',
                        $position + 1,
                        $productNames[$position],
                        $product->getName()
                    )
                );
            }
        }
    }

    /**
     * @Then /^It should be "([^"]+)"$/
     * @Then /^It should be "([^"]+)", "([^"]+)"$/
     * @Then /^It should be "([^"]+)", "([^"]+)", "([^"]+)"$/
     * @Then /^It should be "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)"$/
     */
    public function itShouldBe(...$expectedProductNames)
    {
        /** @var DocumentIterator $searchResult */
        $searchResult = $this->sharedStorage->get('search_result');

        /** @var Product $product */
        foreach ($searchResult as $product) {
            Assert::oneOf($product->getName(), $expectedProductNames);
        }
    }

    /**
     * @param Criteria $criteria
     */
    private function match(Criteria $criteria)
    {
        $result = $this->searchEngine->match($criteria);

        $this->sharedStorage->set('search_result', $result);
    }
}
