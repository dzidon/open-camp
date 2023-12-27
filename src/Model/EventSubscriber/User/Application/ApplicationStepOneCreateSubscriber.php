<?php

namespace App\Model\EventSubscriber\User\Application;

use App\Model\Event\User\Application\ApplicationStepOneCreateEvent;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Model\Service\Application\ApplicationFactoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationStepOneCreateSubscriber
{
    private ApplicationRepositoryInterface $applicationRepository;

    private ApplicationFactoryInterface $applicationFactory;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(ApplicationRepositoryInterface $applicationRepository,
                                ApplicationFactoryInterface    $applicationFactory,
                                DataTransferRegistryInterface  $dataTransfer)
    {
        $this->applicationRepository = $applicationRepository;
        $this->applicationFactory = $applicationFactory;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: ApplicationStepOneCreateEvent::NAME, priority: 200)]
    public function onCreateInstantiateEntity(ApplicationStepOneCreateEvent $event): void
    {
        $data = $event->getApplicationStepOneData();
        $campDate = $event->getCampDate();
        $user = $event->getUser();
        $application = $this->applicationFactory->createApplication($data, $campDate, $user);
        $this->dataTransfer->fillEntity($data, $application);
        $event->setApplication($application);
    }

    #[AsEventListener(event: ApplicationStepOneCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(ApplicationStepOneCreateEvent $event): void
    {
        $application = $event->getApplication();
        $isFlush = $event->isFlush();
        $this->applicationRepository->saveApplication($application, $isFlush);
    }
}