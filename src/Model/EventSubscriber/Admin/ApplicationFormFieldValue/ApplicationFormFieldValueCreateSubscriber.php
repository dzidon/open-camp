<?php

namespace App\Model\EventSubscriber\Admin\ApplicationFormFieldValue;

use App\Model\Entity\ApplicationFormFieldValue;
use App\Model\Event\Admin\ApplicationFormFieldValue\ApplicationFormFieldValueCreateEvent;
use App\Model\Repository\ApplicationFormFieldValueRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationFormFieldValueCreateSubscriber
{
    private ApplicationFormFieldValueRepositoryInterface $applicationFormFieldValueRepository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(ApplicationFormFieldValueRepositoryInterface $applicationFormFieldValueRepository,
                                DataTransferRegistryInterface                $dataTransfer)
    {
        $this->applicationFormFieldValueRepository = $applicationFormFieldValueRepository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: ApplicationFormFieldValueCreateEvent::NAME, priority: 200)]
    public function onCreateFillEntity(ApplicationFormFieldValueCreateEvent $event): void
    {
        $data = $event->getApplicationFormFieldValueData();
        $application = $event->getApplication();
        $applicationCamper = $event->getApplicationCamper();

        $applicationFormFieldValue = new ApplicationFormFieldValue(
            $data->getType(),
            $data->getLabel(),
            $data->getHelp(),
            $data->getPriority(),
            $data->isRequired(),
            $application,
            $applicationCamper,
            $data->getOptions()
        );

        $this->dataTransfer->fillEntity($data, $applicationFormFieldValue);
        $event->setApplicationFormFieldValue($applicationFormFieldValue);
    }

    #[AsEventListener(event: ApplicationFormFieldValueCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(ApplicationFormFieldValueCreateEvent $event): void
    {
        $ApplicationFormFieldValue = $event->getApplicationFormFieldValue();
        $isFlush = $event->isFlush();
        $this->applicationFormFieldValueRepository->saveApplicationFormFieldValue($ApplicationFormFieldValue, $isFlush);
    }
}