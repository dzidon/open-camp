<?php

namespace App\Service\Data\Transfer\Common;

use App\Library\Data\Common\ContactData;
use App\Model\Entity\ApplicationContact;
use App\Service\Data\Transfer\DataTransferInterface;

/**
 * Transfers data from {@link ContactData} to {@link ApplicationContact} and vice versa.
 */
class ApplicationContactDataTransfer implements DataTransferInterface
{
    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof ContactData && $entity instanceof ApplicationContact;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var ContactData $contactData */
        /** @var ApplicationContact $applicationContact */
        $contactData = $data;
        $applicationContact = $entity;

        $contactData->setNameFirst($applicationContact->getNameFirst());
        $contactData->setNameLast($applicationContact->getNameLast());
        $contactData->setEmail($applicationContact->getEmail());
        $contactData->setPhoneNumber($applicationContact->getPhoneNumber());
        $contactData->setRole($applicationContact->getRole());
        $contactData->setRoleOther($applicationContact->getRoleOther());
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var ContactData $contactData */
        /** @var ApplicationContact $applicationContact */
        $contactData = $data;
        $applicationContact = $entity;

        $applicationContact->setNameFirst($contactData->getNameFirst());
        $applicationContact->setNameLast($contactData->getNameLast());
        $applicationContact->setEmail($contactData->getEmail());
        $applicationContact->setPhoneNumber($contactData->getPhoneNumber());
        $applicationContact->setRole($contactData->getRole(), $contactData->getRoleOther());
    }
}