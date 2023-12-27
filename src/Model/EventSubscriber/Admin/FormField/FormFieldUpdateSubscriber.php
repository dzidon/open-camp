<?php

namespace App\Model\EventSubscriber\Admin\FormField;

use App\Model\Event\Admin\FormField\FormFieldUpdateEvent;
use App\Model\Repository\FormFieldRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class FormFieldUpdateSubscriber
{
    private FormFieldRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(FormFieldRepositoryInterface  $repository,
                                DataTransferRegistryInterface $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: FormFieldUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(FormFieldUpdateEvent $event): void
    {
        $data = $event->getFormFieldData();
        $entity = $event->getFormField();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: FormFieldUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(FormFieldUpdateEvent $event): void
    {
        $entity = $event->getFormField();
        $isFlush = $event->isFlush();
        $this->repository->saveFormField($entity, $isFlush);
    }
}