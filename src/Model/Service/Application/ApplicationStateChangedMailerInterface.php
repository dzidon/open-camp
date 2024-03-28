<?php

namespace App\Model\Service\Application;

use App\Model\Entity\Application;

/**
 * Sends e-mails after changing the state of an application.
 */
interface ApplicationStateChangedMailerInterface
{
    /**
     * @param Application $application
     * @return void
     */
    public function sendEmail(Application $application): void;
}