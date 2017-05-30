<?php

namespace spec\Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Query;

use Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Query\EmptyCriteriaQueryFactory;
use Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Query\QueryFactoryInterface;
use ONGR\ElasticsearchDSL\Query\MatchAllQuery;
use PhpSpec\ObjectBehavior;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class EmptyCriteriaQueryFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(EmptyCriteriaQueryFactory::class);
    }

    function it_is_query_factory()
    {
        $this->shouldImplement(QueryFactoryInterface::class);
    }

    function it_creates_query()
    {
        $this->create()->shouldBeLike(new MatchAllQuery());
    }
}
