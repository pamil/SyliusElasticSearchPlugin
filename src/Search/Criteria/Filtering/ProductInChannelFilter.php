<?php

namespace Sylius\ElasticSearchPlugin\Search\Criteria\Filtering;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class ProductInChannelFilter
{
    /**
     * @var string
     */
    private $channelCode;

    /**
     * @param string $channelCode
     */
    public function __construct($channelCode)
    {
        $this->channelCode = $channelCode;
    }

    /**
     * @return string
     */
    public function getChannelCode()
    {
        return $this->channelCode;
    }
}
