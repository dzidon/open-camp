<?php

namespace App\Model\Service\Application;

use App\Model\Entity\Application;

/**
 * Sets application drafts as complete.
 */
interface ApplicationCompleterInterface
{
    /**
     * Sets application drafts as complete.
     *
     * @param Application $application
     * @return void
     */
    public function completeApplication(Application $application): void;
}