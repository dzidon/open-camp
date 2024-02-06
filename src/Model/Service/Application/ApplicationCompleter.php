<?php

namespace App\Model\Service\Application;

use App\Model\Entity\Application;
use App\Model\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @inheritDoc
 */
class ApplicationCompleter implements ApplicationCompleterInterface
{
    private Security $security;

    private RequestStack $requestStack;

    private string $lastCompletedApplicationIdSessionKey;

    public function __construct(Security     $security,
                                RequestStack $requestStack,
                                string       $lastCompletedApplicationIdSessionKey)
    {
        $this->security = $security;
        $this->requestStack = $requestStack;
        $this->lastCompletedApplicationIdSessionKey = $lastCompletedApplicationIdSessionKey;
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

        $applicationIdString = $application
            ->getId()
            ->toRfc4122()
        ;

        /** @var null|User $authenticatedUser */
        $authenticatedUser = $this->security->getUser();

        if ($authenticatedUser !== null)
        {
            $application->setUser($authenticatedUser);
        }
        else
        {
            $session = $this->getSession();
            $session->set($this->lastCompletedApplicationIdSessionKey, $applicationIdString);
        }

        $application->setIsDraft(false);
    }

    private function getSession(): SessionInterface
    {
        $currentRequest = $this->requestStack->getCurrentRequest();

        return $currentRequest->getSession();
    }
}