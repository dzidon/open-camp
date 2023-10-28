<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\CampData;
use App\Model\Entity\Camp;
use App\Service\Data\Transfer\DataTransferInterface;

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

        $campData->setName($camp->getName());
        $campData->setUrlName($camp->getUrlName());
        $campData->setAgeMin($camp->getAgeMin());
        $campData->setAgeMax($camp->getAgeMax());
        $campData->setStreet($camp->getStreet());
        $campData->setTown($camp->getTown());
        $campData->setZip($camp->getZip());
        $campData->setCountry($camp->getCountry());
        $campData->setDescriptionShort($camp->getDescriptionShort());
        $campData->setDescriptionLong($camp->getDescriptionLong());
        $campData->setFeaturedPriority($camp->getFeaturedPriority());
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
        $camp->setStreet($campData->getStreet());
        $camp->setTown($campData->getTown());
        $camp->setZip($campData->getZip());
        $camp->setCountry($campData->getCountry());
        $camp->setDescriptionShort($campData->getDescriptionShort());
        $camp->setDescriptionLong($campData->getDescriptionLong());
        $camp->setFeaturedPriority($campData->getFeaturedPriority());
        $camp->setCampCategory($campData->getCampCategory());
    }
}