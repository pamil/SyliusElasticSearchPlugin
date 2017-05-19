<?php

namespace spec\Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Query;

use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use Sylius\ElasticSearchPlugin\Exception\MissingQueryParameterException;
use Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Query\ProductInChannelQueryFactory;
use Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Query\QueryFactoryInterface;
use PhpSpec\ObjectBehavior;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ProductInChannelQueryFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ProductInChannelQueryFactory::class);
    }

    function it_is_query_factory()
    {
        $this->shouldImplement(QueryFactoryInterface::class);
    }

    function it_creates_product_in_channel_query()
    {
        $this->create(['channel_code' => 'web'])->shouldBeLike(new TermQuery('channelCode', 'web'));
    }

    function it_cannot_be_created_without_channel_code()
    {
        $this->shouldThrow(MissingQueryParameterException::class)->during('create', []);
    }
}
