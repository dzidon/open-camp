<?php

namespace App\Form\DataTransfer\Transfer\User;

use App\Entity\User;
use App\Form\DataTransfer\Data\User\BillingData;
use App\Form\DataTransfer\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link BillingData} to {@link User} and vice versa.
 */
class BillingDataTransfer implements DataTransferInterface
{
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

        $billingData->setName($user->getName());
        $billingData->setCountry($user->getCountry());
        $billingData->setStreet($user->getStreet());
        $billingData->setTown($user->getTown());
        $billingData->setZip($user->getZip());
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

        $user->setName($billingData->getName());
        $user->setCountry($billingData->getCountry());
        $user->setStreet($billingData->getStreet());
        $user->setTown($billingData->getTown());
        $user->setZip($billingData->getZip());
    }
}