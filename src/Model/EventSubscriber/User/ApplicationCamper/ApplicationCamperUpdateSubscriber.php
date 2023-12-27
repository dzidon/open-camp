<?php

namespace App\Model\EventSubscriber\User\ApplicationCamper;

use App\Model\Event\User\ApplicationCamper\ApplicationCamperUpdateEvent;
use App\Model\Repository\ApplicationCamperRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationCamperUpdateSubscriber
{
    private ApplicationCamperRepositoryInterface $ApplicationCamperRepository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(ApplicationCamperRepositoryInterface $ApplicationCamperRepository, DataTransferRegistryInterface $dataTransfer)
    {
        $this->ApplicationCamperRepository = $ApplicationCamperRepository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: ApplicationCamperUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(ApplicationCamperUpdateEvent $event): void
    {
        $data = $event->getApplicationCamperData();
        $entity = $event->getApplicationCamper();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: ApplicationCamperUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(ApplicationCamperUpdateEvent $event): void
    {
        $entity = $event->getApplicationCamper();
        $isFlush = $event->isFlush();
        $this->ApplicationCamperRepository->saveApplicationCamper($entity, $isFlush);
    }
}