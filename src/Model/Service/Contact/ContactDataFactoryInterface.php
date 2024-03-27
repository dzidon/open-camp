<?php

namespace App\Model\Service\Contact;

use App\Library\Data\Common\ContactData;

/**
 * Creates contact data.
 */
interface ContactDataFactoryInterface
{
    /**
     * Creates new contact data.
     *
     * @param bool $isEmailMandatory
     * @param bool $isPhoneNumberMandatory
     * @return ContactData
     */
    public function createContactData(bool $isEmailMandatory, bool $isPhoneNumberMandatory): ContactData;

    /**
     * Gets a callable that creates new contact data.
     *
     * @param bool $isEmailMandatory
     * @param bool $isPhoneNumberMandatory
     * @return callable
     */
    public function getCreateContactDataCallable(bool $isEmailMandatory, bool $isPhoneNumberMandatory): callable;
}