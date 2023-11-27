<?php

namespace App\Model\EventSubscriber\Admin\AttachmentConfig;

use App\Model\Event\Admin\AttachmentConfig\AttachmentConfigDeleteEvent;
use App\Model\Repository\AttachmentConfigRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class AttachmentConfigDeleteSubscriber
{
    private AttachmentConfigRepositoryInterface $repository;

    public function __construct(AttachmentConfigRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: AttachmentConfigDeleteEvent::NAME)]
    public function onDeleteRemoveEntity(AttachmentConfigDeleteEvent $event): void
    {
        $entity = $event->getAttachmentConfig();
        $this->repository->removeAttachmentConfig($entity, true);
    }
}