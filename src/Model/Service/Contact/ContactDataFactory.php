<?php

namespace App\Model\Service\Contact;

use App\Library\Data\User\ContactData;
use App\Model\Entity\Application;

/**
 * @inheritDoc
 */
class ContactDataFactory implements ContactDataFactoryInterface
{
    private bool $isEmailMandatory;

    private bool $isPhoneNumberMandatory;

    public function __construct(bool $isEmailMandatory, bool $isPhoneNumberMandatory)
    {
        $this->isEmailMandatory = $isEmailMandatory;
        $this->isPhoneNumberMandatory = $isPhoneNumberMandatory;
    }

    /**
     * @inheritDoc
     */
    public function createContactData(): ContactData
    {
        return new ContactData($this->isEmailMandatory, $this->isPhoneNumberMandatory);
    }

    /**
     * @inheritDoc
     */
    public function createContactDataFromApplication(Application $application): ContactData
    {
        $isEmailMandatory = $application->isEmailMandatory();
        $isPhoneNumberMandatory = $application->isPhoneNumberMandatory();

        return new ContactData($isEmailMandatory, $isPhoneNumberMandatory);
    }
}