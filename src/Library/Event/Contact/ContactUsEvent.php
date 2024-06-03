<?php

namespace App\Library\Event\Contact;

use App\Library\Data\User\ContactUsData;
use App\Model\Event\AbstractModelEvent;

class ContactUsEvent extends AbstractModelEvent
{
    public const NAME = 'contact_us';

    private ContactUsData $contactUsData;

    public function __construct(ContactUsData $contactUsData)
    {
        $this->contactUsData = $contactUsData;
    }

    public function getContactUsData(): ContactUsData
    {
        return $this->contactUsData;
    }

    public function setContactUsData(ContactUsData $contactUsData): self
    {
        $this->contactUsData = $contactUsData;

        return $this;
    }
}