<?php

namespace App\Form\DataTransfer\Transfer\User;

use App\Form\DataTransfer\Data\User\CamperData;
use App\Form\DataTransfer\Transfer\DataTransferInterface;
use App\Model\Entity\Camper;

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

        $camperData->setName($camper->getName());
        $camperData->setBornAt($camper->getBornAt());
        $camperData->setGender($camper->getGender());
        $camperData->setDietaryRestrictions($camper->getDietaryRestrictions());
        $camperData->setHealthRestrictions($camper->getHealthRestrictions());
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

        $camper->setName($camperData->getName());
        $camper->setBornAt($camperData->getBornAt());
        $camper->setGender($camperData->getGender());
        $camper->setDietaryRestrictions($camperData->getDietaryRestrictions());
        $camper->setHealthRestrictions($camperData->getHealthRestrictions());
    }
}