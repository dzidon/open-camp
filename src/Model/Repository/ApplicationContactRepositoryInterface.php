<?php

namespace App\Model\Repository;

use App\Model\Entity\ApplicationContact;

interface ApplicationContactRepositoryInterface
{
    /**
     * Saves an application contact.
     *
     * @param ApplicationContact $applicationContact
     * @param bool $flush
     * @return void
     */
    public function saveApplicationContact(ApplicationContact $applicationContact, bool $flush): void;

    /**
     * Removes an application contact.
     *
     * @param ApplicationContact $applicationContact
     * @param bool $flush
     * @return void
     */
    public function removeApplicationContact(ApplicationContact $applicationContact, bool $flush): void;
}