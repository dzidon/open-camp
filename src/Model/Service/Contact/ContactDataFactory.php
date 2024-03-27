<?php

namespace App\Model\Service\Contact;

use App\Library\Data\Common\ContactData;

/**
 * @inheritDoc
 */
class ContactDataFactory implements ContactDataFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function createContactData(bool $isEmailMandatory, bool $isPhoneNumberMandatory): ContactData
    {
        return new ContactData($isEmailMandatory, $isPhoneNumberMandatory);
    }

    /**
     * @inheritDoc
     */
    public function getCreateContactDataCallable(bool $isEmailMandatory, bool $isPhoneNumberMandatory): callable
    {
        $factory = $this;

        return function () use ($factory, $isEmailMandatory, $isPhoneNumberMandatory)
        {
            return $factory->createContactData($isEmailMandatory, $isPhoneNumberMandatory);
        };
    }
}