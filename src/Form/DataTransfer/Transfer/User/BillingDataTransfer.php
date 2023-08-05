<?php

namespace App\Form\DataTransfer\Transfer\User;

use App\Form\DataTransfer\Data\User\BillingData;
use App\Form\DataTransfer\Transfer\DataTransferInterface;
use App\Model\Entity\User;

/**
 * Transfers data from {@link BillingData} to {@link User} and vice versa.
 */
class BillingDataTransfer implements DataTransferInterface
{
    private bool $isEuBusinessDataEnabled;

    public function __construct(bool $isEuBusinessDataEnabled)
    {
        $this->isEuBusinessDataEnabled = $isEuBusinessDataEnabled;
    }

    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof BillingData && $entity instanceof User;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var BillingData $billingData */
        /** @var User $user */
        $billingData = $data;
        $user = $entity;

        $billingData->setNameFirst($user->getNameFirst());
        $billingData->setNameLast($user->getNameLast());
        $billingData->setCountry($user->getCountry());
        $billingData->setStreet($user->getStreet());
        $billingData->setTown($user->getTown());
        $billingData->setZip($user->getZip());

        if ($this->isEuBusinessDataEnabled)
        {
            if ($user->getBusinessName() !== null || $user->getBusinessCin() !== null || $user->getBusinessVatId() !== null)
            {
                $billingData->setBusinessName($user->getBusinessName());
                $billingData->setBusinessCin($user->getBusinessCin());
                $billingData->setBusinessVatId($user->getBusinessVatId());

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

        if ($this->isEuBusinessDataEnabled)
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