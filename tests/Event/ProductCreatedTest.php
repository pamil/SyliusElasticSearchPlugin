<?php

namespace Tests\Sylius\ElasticSearchPlugin;

use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\ElasticSearchPlugin\Event\ProductCreated;

final class ProductCreatedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    function it_is_immutable_fact_of_product_creation()
    {
        /** @var ProductInterface|ObjectProphecy $product */
        $product = $this->prophesize(ProductInterface::class);
        $event = ProductCreated::occur($product->reveal());

        $this->assertEquals($product->reveal(), $event->product());
    }
}
