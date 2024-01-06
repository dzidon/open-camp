<?php

namespace App\Model\Repository;

use App\Model\Entity\ApplicationPurchasableItemInstance;

interface ApplicationPurchasableItemInstanceRepositoryInterface
{
    /**
     * Saves an application purchasable item instance.
     *
     * @param ApplicationPurchasableItemInstance $applicationPurchasableItemInstance
     * @param bool $flush
     * @return void
     */
    public function saveApplicationPurchasableItemInstance(ApplicationPurchasableItemInstance $applicationPurchasableItemInstance, bool $flush): void;

    /**
     * Removes an application purchasable item instance.
     *
     * @param ApplicationPurchasableItemInstance $applicationPurchasableItemInstance
     * @param bool $flush
     * @return void
     */
    public function removeApplicationPurchasableItemInstance(ApplicationPurchasableItemInstance $applicationPurchasableItemInstance, bool $flush): void;
}