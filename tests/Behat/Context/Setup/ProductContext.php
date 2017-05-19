<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Sylius\ElasticSearchPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ProductContext implements Context
{
    /**
     * @Given the store has :mugsNumber Mugs, :stickersNumber Stickers and :booksNumber Books
     */
    public function theStoreHasAboutMugsAndStickers($mugsNumber, $stickersNumber, $booksNumber)
    {

    }
}
