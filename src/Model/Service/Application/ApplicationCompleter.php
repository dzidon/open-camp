<?php

namespace App\Model\Service\Application;

use App\Model\Entity\Application;
use App\Model\Entity\User;

/**
 * @inheritDoc
 */
class ApplicationCompleter implements ApplicationCompleterInterface
{
    /**
     * @inheritDoc
     */
    public function completeApplication(Application $application, ?User $user = null): void
    {
        if (!$application->canBeCompleted())
        {
            return;
        }

        if ($user !== null)
        {
            $application->setUser($user);
        }

        $application->setIsDraft(false);
    }
}