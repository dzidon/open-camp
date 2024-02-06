<?php

namespace App\Model\EventSubscriber\User\Application;

use App\Model\Event\User\Application\ApplicationStepThreeUpdateEvent;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Model\Service\Application\ApplicationCompleterInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationStepThreeUpdateSubscriber
{
    private ApplicationRepositoryInterface $applicationRepository;

    private ApplicationCompleterInterface $applicationCompleter;

    public function __construct(ApplicationRepositoryInterface $applicationRepository,
                                ApplicationCompleterInterface  $applicationCompleter)
    {
        $this->applicationRepository = $applicationRepository;
        $this->applicationCompleter = $applicationCompleter;
    }

    #[AsEventListener(event: ApplicationStepThreeUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(ApplicationStepThreeUpdateEvent $event): void
    {
        $application = $event->getApplication();
        $this->applicationCompleter->completeApplication($application);
    }

    #[AsEventListener(event: ApplicationStepThreeUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(ApplicationStepThreeUpdateEvent $event): void
    {
        $application = $event->getApplication();
        $isFlush = $event->isFlush();
        $this->applicationRepository->saveApplication($application, $isFlush);
    }
}