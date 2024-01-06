<?php

namespace App\Model\Service\ApplicationPurchasableItemInstance;

use App\Library\Data\User\ApplicationPurchasableItemInstanceData;
use App\Library\Data\User\ApplicationPurchasableItemVariantData;
use App\Model\Entity\Application;
use App\Model\Entity\ApplicationPurchasableItem;

class ApplicationPurchasableItemInstanceDataFactory implements ApplicationPurchasableItemInstanceDataFactoryInterface
{
    public function createDataFromApplicationPurchasableItem(ApplicationPurchasableItem $applicationPurchasableItem): ApplicationPurchasableItemInstanceData
    {
        $applicationPurchasableItemInstanceData = new ApplicationPurchasableItemInstanceData($applicationPurchasableItem->getCalculatedMaxAmount());

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
    public function createDataFromApplication(Application $application): array
    {
        $data = [];

        foreach ($application->getApplicationPurchasableItems() as $applicationPurchasableItem)
        {
            $data[] = $this->createDataFromApplicationPurchasableItem($applicationPurchasableItem);
        }

        return $data;
    }
}