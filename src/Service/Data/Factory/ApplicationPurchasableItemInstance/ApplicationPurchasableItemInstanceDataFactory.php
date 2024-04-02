<?php

namespace App\Service\Data\Factory\ApplicationPurchasableItemInstance;

use App\Library\Data\Admin\ApplicationPurchasableItemInstanceData as AdminApplicationPurchasableItemInstanceData;
use App\Library\Data\Common\ApplicationPurchasableItemVariantData;
use App\Library\Data\User\ApplicationPurchasableItemInstanceData as UserApplicationPurchasableItemInstanceData;
use App\Model\Entity\Application;
use App\Model\Entity\ApplicationPurchasableItem;

/**
 * @inheritDoc
 */
class ApplicationPurchasableItemInstanceDataFactory implements ApplicationPurchasableItemInstanceDataFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createForUserModule(ApplicationPurchasableItem $applicationPurchasableItem): UserApplicationPurchasableItemInstanceData
    {
        return $this->createFromApplicationPurchasableItem($applicationPurchasableItem, true);
    }

    /**
     * @inheritDoc
     */
    public function createDataArrayForUserModule(Application $application): array
    {
        $data = [];

        foreach ($application->getApplicationPurchasableItems() as $applicationPurchasableItem)
        {
            $idString = $applicationPurchasableItem->getId()->toRfc4122();
            $data[$idString] = $this->createForUserModule($applicationPurchasableItem);
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function getDataCallableArrayForUserModule(Application $application): array
    {
        $factory = $this;
        $data = [];

        foreach ($application->getApplicationPurchasableItems() as $applicationPurchasableItem)
        {
            $idString = $applicationPurchasableItem->getId()->toRfc4122();
            $data[$idString] = function () use ($factory, $applicationPurchasableItem)
            {
                return $factory->createForUserModule($applicationPurchasableItem);
            };
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function createForAdminModule(ApplicationPurchasableItem $applicationPurchasableItem): AdminApplicationPurchasableItemInstanceData
    {
        return $this->createFromApplicationPurchasableItem($applicationPurchasableItem, false);
    }

    /**
     * @inheritDoc
     */
    public function createDataArrayForAdminModule(Application $application): array
    {
        $data = [];

        foreach ($application->getApplicationPurchasableItems() as $applicationPurchasableItem)
        {
            $idString = $applicationPurchasableItem->getId()->toRfc4122();
            $data[$idString] = $this->createForAdminModule($applicationPurchasableItem);
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function getDataCallableArrayForAdminModule(Application $application): array
    {
        $factory = $this;
        $data = [];

        foreach ($application->getApplicationPurchasableItems() as $applicationPurchasableItem)
        {
            $idString = $applicationPurchasableItem->getId()->toRfc4122();
            $data[$idString] = function () use ($factory, $applicationPurchasableItem)
            {
                return $factory->createForAdminModule($applicationPurchasableItem);
            };
        }

        return $data;
    }

    private function createFromApplicationPurchasableItem(
        ApplicationPurchasableItem $applicationPurchasableItem, bool $forUserModule
    ): UserApplicationPurchasableItemInstanceData|AdminApplicationPurchasableItemInstanceData
    {
        $dataClass = AdminApplicationPurchasableItemInstanceData::class;

        if ($forUserModule)
        {
            $dataClass = UserApplicationPurchasableItemInstanceData::class;
        }

        $application = $applicationPurchasableItem->getApplication();
        $isIndividualMode = $application->isPurchasableItemsIndividualMode();
        $maxAmount = $applicationPurchasableItem->getMaxAmount();

        if (!$isIndividualMode)
        {
            $maxAmount = $applicationPurchasableItem->getCalculatedMaxAmount();
        }

        $applicationPurchasableItemInstanceData = new $dataClass($maxAmount);

        if (!$applicationPurchasableItem->hasMultipleVariants())
        {
            return $applicationPurchasableItemInstanceData;
        }

        foreach ($applicationPurchasableItem->getValidVariantValues() as $variant => $values)
        {
            $applicationPurchasableItemVariantData = new ApplicationPurchasableItemVariantData($variant, $values);
            $applicationPurchasableItemInstanceData->addApplicationPurchasableItemVariantsDatum($applicationPurchasableItemVariantData);
        }

        return $applicationPurchasableItemInstanceData;
    }
}