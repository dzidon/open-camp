<?php

namespace App\Service\Data\Transfer\Admin;

use App\Library\Data\Admin\FormFieldData;
use App\Model\Entity\FormField;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link FormFieldData} to {@link FormField} and vice versa.
 */
class FormFieldDataTransfer implements DataTransferInterface
{
    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof FormFieldData && $entity instanceof FormField;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var FormFieldData $formFieldData */
        /** @var FormField $formField */
        $formFieldData = $data;
        $formField = $entity;

        $formFieldData->setName($formField->getName());
        $formFieldData->setType($formField->getType(), $formField->getOptions());
        $formFieldData->setLabel($formField->getLabel());
        $formFieldData->setHelp($formField->getHelp());
        $formFieldData->setIsRequired($formField->isRequired());
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var FormFieldData $formFieldData */
        /** @var FormField $formField */
        $formFieldData = $data;
        $formField = $entity;

        $formField->setName($formFieldData->getName());
        $formField->setType($formFieldData->getType(), $formFieldData->getOptions());
        $formField->setLabel($formFieldData->getLabel());
        $formField->setHelp($formFieldData->getHelp());
        $formField->setIsRequired($formFieldData->isRequired());
    }
}