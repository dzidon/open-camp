<?php

namespace App\Model\EventSubscriber\User\Application;

use App\Model\Entity\User;
use App\Model\Event\User\Application\ApplicationStepThreeUpdateEvent;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Model\Service\Application\ApplicationCompleterInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;

class ApplicationStepThreeUpdateSubscriber
{
    private ApplicationRepositoryInterface $applicationRepository;

    private ApplicationCompleterInterface $applicationCompleter;

    private RequestStack $requestStack;

    private Security $security;

    private string $lastCompletedApplicationIdSessionKey;

    public function __construct(ApplicationRepositoryInterface $applicationRepository,
                                ApplicationCompleterInterface  $applicationCompleter,
                                RequestStack                   $requestStack,
                                Security                       $security,
                                string                         $lastCompletedApplicationIdSessionKey)
    {
        $this->applicationRepository = $applicationRepository;
        $this->applicationCompleter = $applicationCompleter;
        $this->requestStack = $requestStack;
        $this->security = $security;
        $this->lastCompletedApplicationIdSessionKey = $lastCompletedApplicationIdSessionKey;
    }

    #[AsEventListener(event: ApplicationStepThreeUpdateEvent::NAME, priority: 300)]
    public function onCompleteUpdateApplication(ApplicationStepThreeUpdateEvent $event): void
    {
        /** @var User|null $user */
        $user = $this->security->getUser();
        $application = $event->getApplication();
        $this->applicationCompleter->completeApplication($application, $user);
    }

    #[AsEventListener(event: ApplicationStepThreeUpdateEvent::NAME, priority: 200)]
    public function onCompleteSaveApplication(ApplicationStepThreeUpdateEvent $event): void
    {
        $application = $event->getApplication();
        $isFlush = $event->isFlush();
        $this->applicationRepository->saveApplication($application, $isFlush);
    }

    #[AsEventListener(event: ApplicationStepThreeUpdateEvent::NAME, priority: 100)]
    public function onCompleteSetSessionVariable(ApplicationStepThreeUpdateEvent $event): void
    {
        $application = $event->getApplication();
        $applicationIdString = $application
            ->getId()
            ->toRfc4122()
        ;

        $currentRequest = $this->requestStack->getCurrentRequest();
        $session = $currentRequest->getSession();
        $session->set($this->lastCompletedApplicationIdSessionKey, $applicationIdString);
    }
}