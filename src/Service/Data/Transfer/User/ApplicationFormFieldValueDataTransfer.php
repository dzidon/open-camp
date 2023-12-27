<?php

namespace App\Service\Data\Transfer\User;

use App\Library\Data\User\ApplicationFormFieldValueData;
use App\Model\Entity\ApplicationFormFieldValue;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link ApplicationFormFieldValueData} to {@link ApplicationFormFieldValue} and vice versa.
 */
class ApplicationFormFieldValueDataTransfer implements DataTransferInterface
{
    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof ApplicationFormFieldValueData && $entity instanceof ApplicationFormFieldValue;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var ApplicationFormFieldValueData $applicationFormFieldValueData */
        /** @var ApplicationFormFieldValue $applicationFormFieldValue */
        $applicationFormFieldValueData = $data;
        $applicationFormFieldValue = $entity;

        $applicationFormFieldValueData->setValue($applicationFormFieldValue->getValue());
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var ApplicationFormFieldValueData $applicationFormFieldValueData */
        /** @var ApplicationFormFieldValue $applicationFormFieldValue */
        $applicationFormFieldValueData = $data;
        $applicationFormFieldValue = $entity;

        $applicationFormFieldValue->setValue($applicationFormFieldValueData->getValue());
    }
}