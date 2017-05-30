<?php

namespace Tests\Sylius\ElasticSearchPlugin\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use ONGR\ElasticsearchBundle\Service\Manager;

final class ElasticSearchContext implements Context
{
    /**
     * @var Manager
     */
    private $manager;

    /**
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @BeforeScenario
     */
    public function purge()
    {
        $this->manager->dropAndCreateIndex();
    }
}
