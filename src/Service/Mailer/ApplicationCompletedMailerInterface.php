<?php

namespace App\Service\Mailer;

use App\Model\Entity\Application;

/**
 * Sends e-mails after completing an application.
 */
interface ApplicationCompletedMailerInterface
{
    /**
     * @param Application $application
     * @return void
     */
    public function sendEmail(Application $application): void;
}