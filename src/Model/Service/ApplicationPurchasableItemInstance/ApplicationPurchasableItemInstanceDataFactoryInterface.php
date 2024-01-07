<?php

namespace App\Model\Service\ApplicationPurchasableItemInstance;

use App\Library\Data\User\ApplicationPurchasableItemInstanceData;
use App\Model\Entity\Application;
use App\Model\Entity\ApplicationPurchasableItem;

/**
 * Creates application purchasable item instance data.
 */
interface ApplicationPurchasableItemInstanceDataFactoryInterface
{
    /**
     * @param ApplicationPurchasableItem $applicationPurchasableItem
     * @return ApplicationPurchasableItemInstanceData
     */
    public function createDataFromApplicationPurchasableItem(ApplicationPurchasableItem $applicationPurchasableItem): ApplicationPurchasableItemInstanceData;

    /**
     * @param Application $application
     * @return ApplicationPurchasableItemInstanceData[]
     */
    public function createDataFromApplication(Application $application): array;
}