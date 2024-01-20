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
        $campData->setDescriptionShort($camp->getDescriptionShort());
        $campData->setDescriptionLong($camp->getDescriptionLong());
        $campData->setPriority($camp->getPriority());
        $campData->setIsFeatured($camp->isFeatured());
        $campData->setIsHidden($camp->isHidden());
        $campData->setCampCategory($camp->getCampCategory());

        $campData->setStreet($camp->getStreet());
        $campData->setTown($camp->getTown());
        $campData->setZip($camp->getZip());
        $campData->setCountry($camp->getCountry());

        if ($campData->getStreet() !== null || $campData->getTown() !== null || $campData->getZip() !== null || $campData->getCountry() !== null)
        {
            $campData->setIsAddressPresent(true);
        }
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
        $camp->setPriority($campData->getPriority());
        $camp->setIsFeatured($campData->isFeatured());
        $camp->setIsHidden($campData->isHidden());
        $camp->setCampCategory($campData->getCampCategory());

        if ($campData->isAddressPresent())
        {
            $camp->setStreet($campData->getStreet());
            $camp->setTown($campData->getTown());
            $camp->setZip($campData->getZip());
            $camp->setCountry($campData->getCountry());
        }
        else
        {
            $camp->setStreet(null);
            $camp->setTown(null);
            $camp->setZip(null);
            $camp->setCountry(null);
        }
    }
}