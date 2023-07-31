<?php

namespace App\Form\DataTransfer\Transfer\Admin;

use App\Form\DataTransfer\Data\Admin\CampData;
use App\Form\DataTransfer\Transfer\DataTransferInterface;
use App\Model\Entity\Camp;

/**
 * Transfers data from {@link CampData} to {@link Camp} and vice versa.
 */
class CampDataTransfer implements DataTransferInterface
{
    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof CampData && $entity instanceof Camp;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var CampData $campData */
        /** @var Camp $camp */
        $campData = $data;
        $camp = $entity;

        $campData->setId($camp->getId());
        $campData->setName($camp->getName());
        $campData->setUrlName($camp->getUrlName());
        $campData->setAgeMin($camp->getAgeMin());
        $campData->setAgeMax($camp->getAgeMax());
        $campData->setDescriptionShort($camp->getDescriptionShort());
        $campData->setDescriptionLong($camp->getDescriptionLong());
        $campData->setCampCategory($camp->getCampCategory());
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var CampData $campData */
        /** @var Camp $camp */
        $campData = $data;
        $camp = $entity;

        $camp->setName($campData->getName());
        $camp->setUrlName($campData->getUrlName());
        $camp->setAgeMin($campData->getAgeMin());
        $camp->setAgeMax($campData->getAgeMax());
        $camp->setDescriptionShort($campData->getDescriptionShort());
        $camp->setDescriptionLong($campData->getDescriptionLong());
        $camp->setCampCategory($campData->getCampCategory());
    }
}