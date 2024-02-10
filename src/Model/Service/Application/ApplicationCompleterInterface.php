<?php

namespace App\Model\Service\Application;

use App\Model\Entity\Application;

/**
 * Marks application drafts as complete.
 */
interface ApplicationCompleterInterface
{
    /**
     * Marks application drafts as complete.
     *
     * @param Application $application
     * @return void
     */
    public function completeApplication(Application $application): void;
}