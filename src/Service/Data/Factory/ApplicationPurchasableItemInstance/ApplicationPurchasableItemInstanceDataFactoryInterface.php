<?php

namespace App\Service\Data\Factory\ApplicationPurchasableItemInstance;

use App\Library\Data\Admin\ApplicationPurchasableItemInstanceData as AdminApplicationPurchasableItemInstanceData;
use App\Library\Data\User\ApplicationPurchasableItemInstanceData as UserApplicationPurchasableItemInstanceData;
use App\Model\Entity\Application;
use App\Model\Entity\ApplicationPurchasableItem;

/**
 * Creates application purchasable item instance data.
 */
interface ApplicationPurchasableItemInstanceDataFactoryInterface
{
    /**
     * @param ApplicationPurchasableItem $applicationPurchasableItem
     * @return UserApplicationPurchasableItemInstanceData
     */
    public function createForUserModule(ApplicationPurchasableItem $applicationPurchasableItem): UserApplicationPurchasableItemInstanceData;

    /**
     * Creates an array of default instance data objects for each purchasable item in the given application.
     *
     * @param Application $application
     * @return UserApplicationPurchasableItemInstanceData[]
     */
    public function createDataArrayForUserModule(Application $application): array;

    /**
     * Creates an array of callables that instantiate default instance data objects for each purchasable
     * item in the given application.
     *
     * @param Application $application
     * @return callable[]
     */
    public function getDataCallableArrayForUserModule(Application $application): array;

    /**
     * @param ApplicationPurchasableItem $applicationPurchasableItem
     * @return AdminApplicationPurchasableItemInstanceData
     */
    public function createForAdminModule(ApplicationPurchasableItem $applicationPurchasableItem): AdminApplicationPurchasableItemInstanceData;

    /**
     * Creates an array of default instance data objects for each purchasable item in the given application.
     *
     * @param Application $application
     * @return AdminApplicationPurchasableItemInstanceData[]
     */
    public function createDataArrayForAdminModule(Application $application): array;

    /**
     * Creates an array of callables that instantiate default instance data objects for each purchasable
     * item in the given application.
     *
     * @param Application $application
     * @return callable[]
     */
    public function getDataCallableArrayForAdminModule(Application $application): array;
}