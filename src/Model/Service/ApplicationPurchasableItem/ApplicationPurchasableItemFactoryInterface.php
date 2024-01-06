<?php

namespace App\Model\Service\ApplicationPurchasableItem;

use App\Model\Entity\Application;
use App\Model\Entity\ApplicationPurchasableItem;
use App\Model\Entity\CampDatePurchasableItem;

interface ApplicationPurchasableItemFactoryInterface
{
    /**
     * @param CampDatePurchasableItem $campDatePurchasableItem
     * @param Application $application
     * @return ApplicationPurchasableItem
     */
    public function createApplicationPurchasableItem(CampDatePurchasableItem $campDatePurchasableItem, Application $application): ApplicationPurchasableItem;
}