<?php

namespace App\Model\EventSubscriber\Admin\FormField;

use App\Model\Entity\FormField;
use App\Model\Event\Admin\FormField\FormFieldCreatedEvent;
use App\Model\Event\Admin\FormField\FormFieldCreateEvent;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FormFieldCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(DataTransferRegistryInterface $dataTransfer, EventDispatcherInterface $eventDispatcher)
    {
        $this->dataTransfer = $dataTransfer;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[AsEventListener(event: FormFieldCreateEvent::NAME)]
    public function onCreateFillEntity(FormFieldCreateEvent $event): void
    {
        $data = $event->getFormFieldData();
        $entity = new FormField($data->getName(), $data->getType(), $data->getLabel(), $data->getOptions());
        $this->dataTransfer->fillEntity($data, $entity);

        $event = new FormFieldCreatedEvent($data, $entity);
        $this->eventDispatcher->dispatch($event, FormFieldCreatedEvent::NAME);
    }
}