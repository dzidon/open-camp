<?php

namespace App\Service\Mailer;

use App\Model\Entity\Application;

interface ApplicationCompletedMailerInterface
{
    public function sendEmail(Application $application): void;
}