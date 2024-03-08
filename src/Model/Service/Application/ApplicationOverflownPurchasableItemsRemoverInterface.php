<?php

namespace App\Model\Service\Application;

use App\Model\Entity\Application;

/**
 * Removes purchasable items whose amounts are above max amount.
 */
interface ApplicationOverflownPurchasableItemsRemoverInterface
{
    /**
     * Removes purchasable items whose amounts are above max amount.
     *
     * @param Application $application
     * @return void
     */
    public function removeOverflownPurchasableItems(Application $application): void;
}