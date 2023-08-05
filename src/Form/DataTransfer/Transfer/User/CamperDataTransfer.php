<?php

namespace App\Form\DataTransfer\Transfer\User;

use App\Form\DataTransfer\Data\User\CamperData;
use App\Form\DataTransfer\Transfer\DataTransferInterface;
use App\Model\Entity\Camper;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Transfers data from {@link CamperData} to {@link Camper} and vice versa.
 */
class CamperDataTransfer implements DataTransferInterface
{
    private bool $isSaleCamperSiblingsEnabled;
    private bool $isNationalIdentifierEnabled;

    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor,
                                bool $isSaleCamperSiblingsEnabled,
                                bool $isNationalIdentifierEnabled)
    {
        $this->propertyAccessor = $propertyAccessor;

        $this->isSaleCamperSiblingsEnabled = $isSaleCamperSiblingsEnabled;
        $this->isNationalIdentifierEnabled = $isNationalIdentifierEnabled;
    }

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
        $camperData->setSiblings($camper->getSiblings());

        if ($this->isNationalIdentifierEnabled)
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

        if ($this->isSaleCamperSiblingsEnabled)
        {
            $this->propertyAccessor->setValue($camper, 'siblings', $camperData->getSiblings());
        }

        if ($this->isNationalIdentifierEnabled)
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