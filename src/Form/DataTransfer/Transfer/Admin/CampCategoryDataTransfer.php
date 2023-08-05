<?php

namespace App\Form\DataTransfer\Transfer\Admin;

use App\Form\DataTransfer\Data\Admin\CampCategoryData;
use App\Form\DataTransfer\Transfer\DataTransferInterface;
use App\Model\Entity\CampCategory;

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

        $campCategoryData->setId($campCategory->getId());
        $campCategoryData->setName($campCategory->getName());
        $campCategoryData->setUrlName($campCategory->getUrlName());
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
        $campCategory->setParent($campCategoryData->getParent());
    }
}