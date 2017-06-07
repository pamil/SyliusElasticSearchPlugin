<?php

namespace Tests\Sylius\ElasticSearchPlugin;

use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\ElasticSearchPlugin\Event\ProductUpdated;

final class ProductUpdatedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    function it_is_immutable_fact_of_product_updation()
    {
        /** @var ProductInterface|ObjectProphecy $product */
        $product = $this->prophesize(ProductInterface::class);
        $event = ProductUpdated::occur($product->reveal());

        $this->assertEquals($product->reveal(), $event->product());
    }
}
