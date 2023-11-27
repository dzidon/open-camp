<?php

namespace App\Model\EventSubscriber\Admin\AttachmentConfig;

use App\Model\Entity\AttachmentConfig;
use App\Model\Event\Admin\AttachmentConfig\AttachmentConfigCreatedEvent;
use App\Model\Event\Admin\AttachmentConfig\AttachmentConfigCreateEvent;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class AttachmentConfigCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(DataTransferRegistryInterface $dataTransfer, EventDispatcherInterface $eventDispatcher)
    {
        $this->dataTransfer = $dataTransfer;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[AsEventListener(event: AttachmentConfigCreateEvent::NAME)]
    public function onCreateFillEntity(AttachmentConfigCreateEvent $event): void
    {
        $data = $event->getAttachmentConfigData();
        $entity = new AttachmentConfig($data->getName(), $data->getLabel(), $data->getMaxSize());
        $this->dataTransfer->fillEntity($data, $entity);

        $event = new AttachmentConfigCreatedEvent($data, $entity);
        $this->eventDispatcher->dispatch($event, AttachmentConfigCreatedEvent::NAME);
    }
}