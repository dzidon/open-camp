<?php

namespace App\Model\Service\ApplicationPurchasableItemInstance;

use App\Library\Data\Common\ApplicationPurchasableItemInstanceData;
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
     * Creates an array of default instance data objects for each purchasable item in the given application.
     *
     * @param Application $application
     * @return ApplicationPurchasableItemInstanceData[]
     */
    public function createDataArrayFromApplication(Application $application): array;

    /**
     * Creates an array of callables that instantiate default instance data objects for each purchasable
     * item in the given application.
     *
     * @param Application $application
     * @return callable[]
     */
    public function getDataCallableArrayFromApplication(Application $application): array;
}