<?php

namespace App\Model\EventSubscriber\User\Application;

use App\Model\Event\User\Application\ApplicationStepThreeUpdateEvent;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Model\Service\Application\ApplicationCompleterInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;

class ApplicationStepThreeUpdateSubscriber
{
    private ApplicationRepositoryInterface $applicationRepository;

    private ApplicationCompleterInterface $applicationCompleter;

    private RequestStack $requestStack;

    private string $lastCompletedApplicationIdSessionKey;

    public function __construct(ApplicationRepositoryInterface $applicationRepository,
                                ApplicationCompleterInterface  $applicationCompleter,
                                RequestStack                   $requestStack,
                                string                         $lastCompletedApplicationIdSessionKey)
    {
        $this->applicationRepository = $applicationRepository;
        $this->applicationCompleter = $applicationCompleter;
        $this->requestStack = $requestStack;
        $this->lastCompletedApplicationIdSessionKey = $lastCompletedApplicationIdSessionKey;
    }

    #[AsEventListener(event: ApplicationStepThreeUpdateEvent::NAME, priority: 300)]
    public function onCompleteUpdateApplication(ApplicationStepThreeUpdateEvent $event): void
    {
        $application = $event->getApplication();
        $this->applicationCompleter->completeApplication($application);
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