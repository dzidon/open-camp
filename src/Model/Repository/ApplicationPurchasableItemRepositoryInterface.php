<?php

namespace App\Model\Repository;

use App\Model\Entity\ApplicationPurchasableItem;

interface ApplicationPurchasableItemRepositoryInterface
{
    /**
     * Saves an application purchasable item.
     *
     * @param ApplicationPurchasableItem $ApplicationPurchasableItem
     * @param bool $flush
     * @return void
     */
    public function saveApplicationPurchasableItem(ApplicationPurchasableItem $ApplicationPurchasableItem, bool $flush): void;

    /**
     * Removes an application purchasable item.
     *
     * @param ApplicationPurchasableItem $ApplicationPurchasableItem
     * @param bool $flush
     * @return void
     */
    public function removeApplicationPurchasableItem(ApplicationPurchasableItem $ApplicationPurchasableItem, bool $flush): void;
}