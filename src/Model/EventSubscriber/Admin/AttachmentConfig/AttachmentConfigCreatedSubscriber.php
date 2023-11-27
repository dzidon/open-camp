<?php

namespace App\Model\EventSubscriber\Admin\AttachmentConfig;

use App\Model\Event\Admin\AttachmentConfig\AttachmentConfigCreatedEvent;
use App\Model\Repository\AttachmentConfigRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class AttachmentConfigCreatedSubscriber
{
    private AttachmentConfigRepositoryInterface $repository;

    public function __construct(AttachmentConfigRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: AttachmentConfigCreatedEvent::NAME)]
    public function onCreatedSaveEntity(AttachmentConfigCreatedEvent $event): void
    {
        $entity = $event->getAttachmentConfig();
        $this->repository->saveAttachmentConfig($entity, true);
    }
}