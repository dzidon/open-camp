<?php

namespace App\Model\Service\Application;

use App\Model\Entity\Application;
use App\Model\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @inheritDoc
 */
class ApplicationCompleter implements ApplicationCompleterInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @inheritDoc
     */
    public function completeApplication(Application $application): void
    {
        if (!$application->canBeCompleted())
        {
            return;
        }

        /** @var null|User $authenticatedUser */
        $authenticatedUser = $this->security->getUser();

        if ($authenticatedUser !== null)
        {
            $application->setUser($authenticatedUser);
        }

        $application->setIsDraft(false);
    }
}