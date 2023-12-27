<?php

namespace App\Model\EventSubscriber\User\ApplicationFormFieldValue;

use App\Model\Event\User\ApplicationFormFieldValue\ApplicationFormFieldValueUpdateEvent;
use App\Model\Repository\ApplicationFormFieldValueRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationFormFieldValueUpdateSubscriber
{
    private ApplicationFormFieldValueRepositoryInterface $applicationFormFieldValueRepository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(ApplicationFormFieldValueRepositoryInterface $ApplicationFormFieldValueRepository,
                                DataTransferRegistryInterface                $dataTransfer)
    {
        $this->applicationFormFieldValueRepository = $ApplicationFormFieldValueRepository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: ApplicationFormFieldValueUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(ApplicationFormFieldValueUpdateEvent $event): void
    {
        $data = $event->getApplicationFormFieldValueData();
        $entity = $event->getApplicationFormFieldValue();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: ApplicationFormFieldValueUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(ApplicationFormFieldValueUpdateEvent $event): void
    {
        $entity = $event->getApplicationFormFieldValue();
        $isFlush = $event->isFlush();
        $this->applicationFormFieldValueRepository->saveApplicationFormFieldValue($entity, $isFlush);
    }
}