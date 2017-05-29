<?php

namespace spec\Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Query;

use ONGR\ElasticsearchDSL\Query\FullText\MatchQuery;
use Sylius\ElasticSearchPlugin\Exception\MissingQueryParameterException;
use Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Query\MatchProductNameQueryFactory;
use Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Query\QueryFactoryInterface;
use PhpSpec\ObjectBehavior;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class MatchProductNameQueryFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MatchProductNameQueryFactory::class);
    }

    function it_is_query_factory()
    {
        $this->shouldImplement(QueryFactoryInterface::class);
    }

    function it_creates_match_query_with_name_field_by_default()
    {
        $this->create(['search' => 'banana'])->shouldBeLike(new MatchQuery('name', 'banana'));
    }

    function it_cannot_be_created_without_search_parameter()
    {
        $this->shouldThrow(MissingQueryParameterException::class)->during('create', []);
    }

    function it_cannot_be_created_with_search_parameter()
    {
        $this->shouldThrow(MissingQueryParameterException::class)->during('create', [[]]);
    }

    function it_cannot_be_created_with_empty_search_parameter()
    {
        $this->shouldThrow(MissingQueryParameterException::class)->during('create', [['phrase' => null]]);
    }

    function it_cannot_be_created_with_empty_string_search_parameter()
    {
        $this->shouldThrow(MissingQueryParameterException::class)->during('create', [['phrase' => '']]);
    }
}
