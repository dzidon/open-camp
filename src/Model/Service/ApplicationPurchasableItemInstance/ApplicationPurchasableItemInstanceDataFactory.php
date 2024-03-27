<?php

namespace App\Model\Service\ApplicationPurchasableItemInstance;

use App\Library\Data\Common\ApplicationPurchasableItemInstanceData;
use App\Library\Data\Common\ApplicationPurchasableItemVariantData;
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
    public function createDataFromApplicationPurchasableItem(ApplicationPurchasableItem $applicationPurchasableItem): ApplicationPurchasableItemInstanceData
    {
        $application = $applicationPurchasableItem->getApplication();
        $isIndividualMode = $application->isPurchasableItemsIndividualMode();
        $maxAmount = $applicationPurchasableItem->getMaxAmount();

        if (!$isIndividualMode)
        {
            $maxAmount = $applicationPurchasableItem->getCalculatedMaxAmount();
        }

        $applicationPurchasableItemInstanceData = new ApplicationPurchasableItemInstanceData($maxAmount);

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

    /**
     * @inheritDoc
     */
    public function createDataArrayFromApplication(Application $application): array
    {
        $data = [];

        foreach ($application->getApplicationPurchasableItems() as $applicationPurchasableItem)
        {
            $idString = $applicationPurchasableItem->getId()->toRfc4122();
            $data[$idString] = $this->createDataFromApplicationPurchasableItem($applicationPurchasableItem);
        }

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function getDataCallableArrayFromApplication(Application $application): array
    {
        $factory = $this;
        $data = [];

        foreach ($application->getApplicationPurchasableItems() as $applicationPurchasableItem)
        {
            $idString = $applicationPurchasableItem->getId()->toRfc4122();
            $data[$idString] = function () use ($factory, $applicationPurchasableItem)
            {
                return $factory->createDataFromApplicationPurchasableItem($applicationPurchasableItem);
            };
        }

        return $data;
    }
}