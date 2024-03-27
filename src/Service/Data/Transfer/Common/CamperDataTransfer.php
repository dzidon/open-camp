<?php

namespace App\Service\Data\Transfer\Common;

use App\Library\Data\Common\CamperData;
use App\Model\Entity\Camper;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link CamperData} to {@link Camper} and vice versa.
 */
class CamperDataTransfer implements DataTransferInterface
{
    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof CamperData && $entity instanceof Camper;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var CamperData $camperData */
        /** @var Camper $camper */
        $camperData = $data;
        $camper = $entity;

        $camperData->setNameFirst($camper->getNameFirst());
        $camperData->setNameLast($camper->getNameLast());
        $camperData->setBornAt($camper->getBornAt());
        $camperData->setGender($camper->getGender());
        $camperData->setDietaryRestrictions($camper->getDietaryRestrictions());
        $camperData->setHealthRestrictions($camper->getHealthRestrictions());
        $camperData->setMedication($camper->getMedication());

        if ($camperData->isNationalIdentifierEnabled())
        {
            if ($camper->getNationalIdentifier() === null)
            {
                $camperData->setIsNationalIdentifierAbsent(true);
            }
            else
            {
                $camperData->setNationalIdentifier($camper->getNationalIdentifier());
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var CamperData $camperData */
        /** @var Camper $camper */
        $camperData = $data;
        $camper = $entity;

        $camper->setNameFirst($camperData->getNameFirst());
        $camper->setNameLast($camperData->getNameLast());
        $camper->setBornAt($camperData->getBornAt());
        $camper->setGender($camperData->getGender());
        $camper->setDietaryRestrictions($camperData->getDietaryRestrictions());
        $camper->setHealthRestrictions($camperData->getHealthRestrictions());
        $camper->setMedication($camperData->getMedication());

        if ($camperData->isNationalIdentifierEnabled())
        {
            if ($camperData->isNationalIdentifierAbsent())
            {
                $camper->setNationalIdentifier(null);
            }
            else
            {
                $camper->setNationalIdentifier($camperData->getNationalIdentifier());
            }
        }
    }
}