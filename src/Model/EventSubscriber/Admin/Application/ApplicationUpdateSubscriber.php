<?php

namespace App\Model\EventSubscriber\Admin\Application;

use App\Model\Event\Admin\Application\ApplicationUpdateEvent;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Model\Service\Application\ApplicationStateChangedMailerInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationUpdateSubscriber
{
    private ApplicationRepositoryInterface $repository;

    private ApplicationStateChangedMailerInterface $applicationStateChangedMailer;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(ApplicationRepositoryInterface         $repository,
                                ApplicationStateChangedMailerInterface $applicationStateChangedMailer,
                                DataTransferRegistryInterface          $dataTransfer)
    {
        $this->repository = $repository;
        $this->applicationStateChangedMailer = $applicationStateChangedMailer;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: ApplicationUpdateEvent::NAME, priority: 400)]
    public function onUpdateFillEntity(ApplicationUpdateEvent $event): void
    {
        $data = $event->getApplicationData();
        $entity = $event->getApplication();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: ApplicationUpdateEvent::NAME, priority: 300)]
    public function onUpdateCachePrices(ApplicationUpdateEvent $event): void
    {
        $entity = $event->getApplication();
        $entity->cacheAllFullPrices();
    }

    #[AsEventListener(event: ApplicationUpdateEvent::NAME, priority: 200)]
    public function onUpdateSaveEntity(ApplicationUpdateEvent $event): void
    {
        $entity = $event->getApplication();
        $isFlush = $event->isFlush();
        $this->repository->saveApplication($entity, $isFlush);
    }

    #[AsEventListener(event: ApplicationUpdateEvent::NAME, priority: 100)]
    public function onUpdateSendEmail(ApplicationUpdateEvent $event): void
    {
        $entity = $event->getApplication();

        if ($event->isStateChange())
        {
            $this->applicationStateChangedMailer->sendEmail($entity);
        }
    }
}