<?php

namespace spec\Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\Filter;

use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use Sylius\ElasticSearchPlugin\Search\Criteria\Filtering\ProductHasOptionCodesFilter;
use Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\Filter\ProductHasMultipleOptionCodesApplicator;
use Sylius\ElasticSearchPlugin\Search\Elastic\Applicator\SearchCriteriaApplicatorInterface;
use Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Query\QueryFactoryInterface;
use ONGR\ElasticsearchDSL\Search;
use PhpSpec\ObjectBehavior;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ProductHasMultipleOptionCodesApplicatorSpec extends ObjectBehavior
{
    function let(QueryFactoryInterface $productHasOptionCodeQueryFactory)
    {
        $this->beConstructedWith($productHasOptionCodeQueryFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProductHasMultipleOptionCodesApplicator::class);
    }

    function it_is_criteria_search_applicator()
    {
        $this->shouldImplement(SearchCriteriaApplicatorInterface::class);
    }

    function it_applies_search_query_for_multiple_product_options(
        QueryFactoryInterface $productHasOptionCodeQueryFactory,
        TermQuery $mediumMugTermQuery,
        TermQuery $stickerSizeTermQuery,
        Search $search
    ) {
        $criteria = new ProductHasOptionCodesFilter(['medium_mug', 'sticker_size_1']);
        $productHasOptionCodeQueryFactory->create(['option_value_code' => 'medium_mug'])->willReturn($mediumMugTermQuery);
        $productHasOptionCodeQueryFactory->create(['option_value_code' => 'sticker_size_1'])->willReturn($stickerSizeTermQuery);

        $search->addPostFilter($mediumMugTermQuery, BoolQuery::SHOULD)->shouldBeCalled();
        $search->addPostFilter($stickerSizeTermQuery, BoolQuery::SHOULD)->shouldBeCalled();

        $this->apply($criteria, $search);
    }

    function it_applies_search_query_for_single_product_option(
        QueryFactoryInterface $productHasOptionCodeQueryFactory,
        TermQuery $mediumMugTermQuery,
        Search $search
    ) {
        $criteria = new ProductHasOptionCodesFilter(['medium_mug']);
        $productHasOptionCodeQueryFactory->create(['option_value_code' => 'medium_mug'])->willReturn($mediumMugTermQuery);

        $search->addPostFilter($mediumMugTermQuery, BoolQuery::SHOULD)->shouldBeCalled();

        $this->apply($criteria, $search);
    }
}
