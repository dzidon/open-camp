<?php

namespace App\Model\EventSubscriber\User\Application;

use App\Model\Event\User\Application\ApplicationStepOneUpdateEvent;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationStepOneUpdateSubscriber
{
    private ApplicationRepositoryInterface $applicationRepository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(ApplicationRepositoryInterface $applicationRepository, DataTransferRegistryInterface $dataTransfer)
    {
        $this->applicationRepository = $applicationRepository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: ApplicationStepOneUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(ApplicationStepOneUpdateEvent $event): void
    {
        $data = $event->getApplicationStepOneData();
        $entity = $event->getApplication();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: ApplicationStepOneUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(ApplicationStepOneUpdateEvent $event): void
    {
        $application = $event->getApplication();
        $isFlush = $event->isFlush();
        $this->applicationRepository->saveApplication($application, $isFlush);
    }
}