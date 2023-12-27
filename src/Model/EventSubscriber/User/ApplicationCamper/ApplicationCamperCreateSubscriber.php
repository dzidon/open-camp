<?php

namespace App\Model\EventSubscriber\User\ApplicationCamper;

use App\Model\Entity\ApplicationCamper;
use App\Model\Event\User\ApplicationCamper\ApplicationCamperCreateEvent;
use App\Model\Repository\ApplicationCamperRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationCamperCreateSubscriber
{
    private ApplicationCamperRepositoryInterface $ApplicationCamperRepository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(ApplicationCamperRepositoryInterface $ApplicationCamperRepository, DataTransferRegistryInterface $dataTransfer)
    {
        $this->ApplicationCamperRepository = $ApplicationCamperRepository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: ApplicationCamperCreateEvent::NAME, priority: 200)]
    public function onCreateFillEntity(ApplicationCamperCreateEvent $event): void
    {
        $data = $event->getApplicationCamperData();
        $camperData = $data->getCamperData();
        $application = $event->getApplication();
        $applicationCamper = new ApplicationCamper(
            $camperData->getNameFirst(),
            $camperData->getNameLast(),
            $camperData->getGender(),
            $camperData->getBornAt(),
            $application
        );

        $this->dataTransfer->fillEntity($data, $applicationCamper);
        $event->setApplicationCamper($applicationCamper);
    }

    #[AsEventListener(event: ApplicationCamperCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(ApplicationCamperCreateEvent $event): void
    {
        $applicationCamper = $event->getApplicationCamper();
        $isFlush = $event->isFlush();
        $this->ApplicationCamperRepository->saveApplicationCamper($applicationCamper, $isFlush);
    }
}