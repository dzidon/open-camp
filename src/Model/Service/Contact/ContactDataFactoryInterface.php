<?php

namespace App\Model\Service\Contact;

use App\Library\Data\User\ContactData;
use App\Model\Entity\Application;

/**
 * Creates contact data.
 */
interface ContactDataFactoryInterface
{
    /**
     * Creates new contact data.
     *
     * @return ContactData
     */
    public function createContactData(): ContactData;

    /**
     * Creates contact data from an application.
     *
     * @param Application $application
     * @return ContactData
     */
    public function createContactDataFromApplication(Application $application): ContactData;
}