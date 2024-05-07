<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\CampCategoryData;
use App\Model\Entity\CampCategory;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link CampCategoryData} to {@link CampCategory} and vice versa.
 */
class CampCategoryDataTransfer implements DataTransferInterface
{
    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof CampCategoryData && $entity instanceof CampCategory;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var CampCategoryData $campCategoryData */
        /** @var CampCategory $campCategory */
        $campCategoryData = $data;
        $campCategory = $entity;

        $campCategoryData->setName($campCategory->getName());
        $campCategoryData->setUrlName($campCategory->getUrlName());
        $campCategoryData->setPriority($campCategory->getPriority());
        $campCategoryData->setParent($campCategory->getParent());
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var CampCategoryData $campCategoryData */
        /** @var CampCategory $campCategory */
        $campCategoryData = $data;
        $campCategory = $entity;

        $campCategory->setName($campCategoryData->getName());
        $campCategory->setUrlName($campCategoryData->getUrlName());
        $campCategory->setPriority($campCategoryData->getPriority());
        $campCategory->setParent($campCategoryData->getParent());
    }
}