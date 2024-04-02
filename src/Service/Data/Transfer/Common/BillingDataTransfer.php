<?php

namespace App\Service\Data\Transfer\Common;

use App\Library\Data\Common\BillingData;
use App\Model\Entity\Application;
use App\Model\Entity\User;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link BillingData} to {@link User} or {@link Application} and vice versa.
 */
class BillingDataTransfer implements DataTransferInterface
{
    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof BillingData && ($entity instanceof User || $entity instanceof Application);
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var BillingData $billingData */
        /** @var User|Application $entity */
        $billingData = $data;

        $billingData->setNameFirst($entity->getNameFirst());
        $billingData->setNameLast($entity->getNameLast());
        $billingData->setCountry($entity->getCountry());
        $billingData->setStreet($entity->getStreet());
        $billingData->setTown($entity->getTown());
        $billingData->setZip($entity->getZip());

        if ($billingData->isEuBusinessDataEnabled())
        {
            if ($entity->getBusinessName() !== null || $entity->getBusinessCin() !== null || $entity->getBusinessVatId() !== null)
            {
                $billingData->setBusinessName($entity->getBusinessName());
                $billingData->setBusinessCin($entity->getBusinessCin());
                $billingData->setBusinessVatId($entity->getBusinessVatId());

                $billingData->setIsCompany(true);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var BillingData $billingData */
        /** @var User $user */
        $billingData = $data;
        $user = $entity;

        $user->setNameFirst($billingData->getNameFirst());
        $user->setNameLast($billingData->getNameLast());
        $user->setCountry($billingData->getCountry());
        $user->setStreet($billingData->getStreet());
        $user->setTown($billingData->getTown());
        $user->setZip($billingData->getZip());

        if ($billingData->isEuBusinessDataEnabled())
        {
            if ($billingData->isCompany())
            {
                $user->setBusinessName($billingData->getBusinessName());
                $user->setBusinessCin($billingData->getBusinessCin());
                $user->setBusinessVatId($billingData->getBusinessVatId());
            }
            else
            {
                $user->setBusinessName(null);
                $user->setBusinessCin(null);
                $user->setBusinessVatId(null);
            }
        }
    }
}