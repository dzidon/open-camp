<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\DiscountConfigData;
use App\Library\Data\Admin\DiscountRecurringCamperConfigData;
use App\Library\Data\Admin\DiscountSiblingConfigData;
use App\Model\Entity\DiscountConfig;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link DiscountConfigData} to {@link DiscountConfig} and vice versa.
 */
class DiscountConfigDataTransfer implements DataTransferInterface
{
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof DiscountConfigData && $entity instanceof DiscountConfig;
    }

    public function fillData(object $data, object $entity): void
    {
        /** @var DiscountConfigData $discountConfigData */
        /** @var DiscountConfig $discountConfig */
        $discountConfigData = $data;
        $discountConfig = $entity;

        $discountConfigData->setName($discountConfig->getName());

        foreach ($discountConfig->getRecurringCampersConfig() as $option)
        {
            $discountRecurringCamperConfigData = new DiscountRecurringCamperConfigData();
            $discountRecurringCamperConfigData->setFrom($option['from']);
            $discountRecurringCamperConfigData->setTo($option['to']);
            $discountRecurringCamperConfigData->setDiscount($option['discount']);

            $discountConfigData->addDiscountRecurringCamperConfigData($discountRecurringCamperConfigData);
        }

        foreach ($discountConfig->getSiblingsConfig() as $option)
        {
            $discountSiblingConfigData = new DiscountSiblingConfigData();
            $discountSiblingConfigData->setFrom($option['from']);
            $discountSiblingConfigData->setTo($option['to']);
            $discountSiblingConfigData->setDiscount($option['discount']);

            $discountConfigData->addDiscountSiblingConfigData($discountSiblingConfigData);
        }
    }

    public function fillEntity(object $data, object $entity): void
    {
        /** @var DiscountConfigData $discountConfigData */
        /** @var DiscountConfig $discountConfig */
        $discountConfigData = $data;
        $discountConfig = $entity;

        $discountConfig->setName($discountConfigData->getName());

        $recurringCampersConfig = [];
        $siblingsConfig = [];

        foreach ($discountConfigData->getDiscountRecurringCamperConfigsData() as $discountRecurringCamperConfigsDatum)
        {
            $recurringCampersConfig[] = [
                'from'     => $discountRecurringCamperConfigsDatum->getFrom(),
                'to'       => $discountRecurringCamperConfigsDatum->getTo(),
                'discount' => $discountRecurringCamperConfigsDatum->getDiscount(),
            ];
        }

        foreach ($discountConfigData->getDiscountSiblingConfigsData() as $discountSiblingConfigsDatum)
        {
            $siblingsConfig[] = [
                'from'     => $discountSiblingConfigsDatum->getFrom(),
                'to'       => $discountSiblingConfigsDatum->getTo(),
                'discount' => $discountSiblingConfigsDatum->getDiscount(),
            ];
        }

        $discountConfig->setRecurringCampersConfig($recurringCampersConfig);
        $discountConfig->setSiblingsConfig($siblingsConfig);
    }
}