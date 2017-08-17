<?php
declare(strict_types=1);

namespace Sylius\ElasticSearchPlugin\Filter\ViewData;

use ONGR\FilterManagerBundle\Filter\ViewData\PagerAwareViewData as DefaultPagerAwareViewData;

final class PagerAwareViewData extends DefaultPagerAwareViewData
{
    /**
     * @var int
     */
    private $limit = 10;

    /**
     * @param int $totalItems
     * @param int $currentPage
     * @param int $itemsPerPage
     * @param int $maxPages
     */
    public function setData($totalItems, $currentPage, $itemsPerPage = 10, $maxPages = 10)
    {
        parent::setData($totalItems, $currentPage, $itemsPerPage, $maxPages);

        $this->limit = $itemsPerPage;
    }

    /**
     * {@inheritdoc}
     */
    public function getSerializableData()
    {
        $data = parent::getSerializableData();

        $data['pager']['limit'] = $this->limit;

        return $data;
    }
}
