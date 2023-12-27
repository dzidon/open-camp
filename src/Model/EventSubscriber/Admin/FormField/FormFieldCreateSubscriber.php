<?php

namespace App\Model\EventSubscriber\Admin\FormField;

use App\Model\Entity\FormField;
use App\Model\Event\Admin\FormField\FormFieldCreateEvent;
use App\Model\Repository\FormFieldRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class FormFieldCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private FormFieldRepositoryInterface $repository;

    public function __construct(DataTransferRegistryInterface $dataTransfer, FormFieldRepositoryInterface $repository)
    {
        $this->dataTransfer = $dataTransfer;
        $this->repository = $repository;
    }

    #[AsEventListener(event: FormFieldCreateEvent::NAME, priority: 200)]
    public function onCreateFillEntity(FormFieldCreateEvent $event): void
    {
        $data = $event->getFormFieldData();
        $entity = new FormField($data->getName(), $data->getType(), $data->getLabel(), $data->getOptions());
        $this->dataTransfer->fillEntity($data, $entity);
        $event->setFormField($entity);
    }

    #[AsEventListener(event: FormFieldCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(FormFieldCreateEvent $event): void
    {
        $entity = $event->getFormField();
        $isFlush = $event->isFlush();
        $this->repository->saveFormField($entity, $isFlush);
    }
}