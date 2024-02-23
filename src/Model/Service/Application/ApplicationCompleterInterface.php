<?php

namespace App\Model\Service\Application;

use App\Model\Entity\Application;
use App\Model\Entity\User;

/**
 * Marks application drafts as complete.
 */
interface ApplicationCompleterInterface
{
    /**
     * Marks application drafts as complete.
     *
     * @param Application $application
     * @param ?User $user
     * @return void
     */
    public function completeApplication(Application $application, ?User $user = null): void;
}