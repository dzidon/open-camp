<?php

namespace App\Model\Service\ApplicationPurchasableItem;

use App\Model\Entity\Application;
use App\Model\Entity\ApplicationPurchasableItem;
use App\Model\Entity\CampDatePurchasableItem;

/**
 * @inheritDoc
 */
class ApplicationPurchasableItemFactory implements ApplicationPurchasableItemFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createApplicationPurchasableItem(CampDatePurchasableItem $campDatePurchasableItem, Application $application): ApplicationPurchasableItem
    {
        $purchasableItem = $campDatePurchasableItem->getPurchasableItem();
        $label = $purchasableItem->getLabel();
        $price = $purchasableItem->getPrice();
        $priority = $campDatePurchasableItem->getPriority();
        $description = $purchasableItem->getDescription();
        $maxAmount = $purchasableItem->getMaxAmount();
        $isGlobal = $purchasableItem->isGlobal();

        $validVariantValues = [];

        foreach ($purchasableItem->getPurchasableItemVariants() as $purchasableItemVariant)
        {
            $name = $purchasableItemVariant->getName();
            $values = [];

            foreach ($purchasableItemVariant->getPurchasableItemVariantValues() as $purchasableItemVariantValue)
            {
                $values[] = $purchasableItemVariantValue->getName();
            }

            $validVariantValues[$name] = $values;
        }

        return new ApplicationPurchasableItem(
            $label,
            $price,
            $maxAmount,
            $validVariantValues,
            $priority,
            $isGlobal,
            $purchasableItem,
            $application,
            $description
        );
    }
}