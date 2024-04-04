<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\ApplicationPaymentData;
use App\Model\Entity\ApplicationPayment;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link ApplicationPaymentData} to {@link ApplicationPayment} and vice versa.
 */
class ApplicationPaymentDataTransfer implements DataTransferInterface
{
    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof ApplicationPaymentData && $entity instanceof ApplicationPayment;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var ApplicationPaymentData $applicationPaymentData */
        /** @var ApplicationPayment $applicationPayment */
        $applicationPaymentData = $data;
        $applicationPayment = $entity;

        $applicationPaymentData->setAmount($applicationPayment->getAmount());
        $applicationPaymentData->setType($applicationPayment->getType());
        $applicationPaymentData->setState($applicationPayment->getState());
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var ApplicationPaymentData $applicationPaymentData */
        /** @var ApplicationPayment $applicationPayment */
        $applicationPaymentData = $data;
        $applicationPayment = $entity;
        $newState = $applicationPaymentData->getState();

        $applicationPayment->setAmount($applicationPaymentData->getAmount());
        $applicationPayment->setType($applicationPaymentData->getType());

        if ($applicationPayment->canChangeToState($newState))
        {
            $applicationPayment->setState($newState);
        }
    }
}