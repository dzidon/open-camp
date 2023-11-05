<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\CampDateFormFieldData;
use App\Model\Entity\CampDateFormField;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link CampDateFormFieldData} to {@link CampDateFormField} and vice versa.
 */
class CampDateFormFieldDataTransfer implements DataTransferInterface
{
    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof CampDateFormFieldData && $entity instanceof CampDateFormField;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var CampDateFormFieldData $campDateFormFieldData */
        /** @var CampDateFormField $campDateFormField */
        $campDateFormFieldData = $data;
        $campDateFormField = $entity;

        $campDateFormFieldData->setFormField($campDateFormField->getFormField());
        $campDateFormFieldData->setPriority($campDateFormField->getPriority());
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var CampDateFormFieldData $campDateFormFieldData */
        /** @var CampDateFormField $campDateFormField */
        $campDateFormFieldData = $data;
        $campDateFormField = $entity;

        $campDateFormField->setFormField($campDateFormFieldData->getFormField());
        $campDateFormField->setPriority($campDateFormFieldData->getPriority());
    }
}