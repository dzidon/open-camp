<?php

namespace App\Form\DataTransfer\Transfer\User;

use App\Form\DataTransfer\Data\User\ContactData;
use App\Form\DataTransfer\Transfer\DataTransferInterface;
use App\Model\Entity\Contact;

/**
 * Transfers data from {@link ContactData} to {@link Contact} and vice versa.
 */
class ContactDataTransfer implements DataTransferInterface
{
    /**
     * @inheritDoc
     */
    public function supports(object $data, object $entity): bool
    {
        return $data instanceof ContactData && $entity instanceof Contact;
    }

    /**
     * @inheritDoc
     */
    public function fillData(object $data, object $entity): void
    {
        /** @var ContactData $contactData */
        /** @var Contact $contact */
        $contactData = $data;
        $contact = $entity;

        $contactData->setNameFirst($contact->getNameFirst());
        $contactData->setNameLast($contact->getNameLast());
        $contactData->setEmail($contact->getEmail());
        $contactData->setPhoneNumber($contact->getPhoneNumber());
        $contactData->setRole($contact->getRole());
    }

    /**
     * @inheritDoc
     */
    public function fillEntity(object $data, object $entity): void
    {
        /** @var ContactData $contactData */
        /** @var Contact $contact */
        $contactData = $data;
        $contact = $entity;

        $contact->setNameFirst($contactData->getNameFirst());
        $contact->setNameLast($contactData->getNameLast());
        $contact->setEmail($contactData->getEmail());
        $contact->setPhoneNumber($contactData->getPhoneNumber());
        $contact->setRole($contactData->getRole());
    }
}