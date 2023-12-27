<?php

namespace App\Model\EventSubscriber\Admin\CampDateAttachmentConfig;

use App\Model\Event\Admin\CampDateAttachmentConfig\CampDateAttachmentConfigDeleteEvent;
use App\Model\Repository\CampDateAttachmentConfigRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CampDateAttachmentConfigDeleteSubscriber
{
    private CampDateAttachmentConfigRepositoryInterface $repository;

    public function __construct(CampDateAttachmentConfigRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: CampDateAttachmentConfigDeleteEvent::NAME, priority: 200)]
    public function onDeleteRemoveEntity(CampDateAttachmentConfigDeleteEvent $event): void
    {
        $entity = $event->getCampDateAttachmentConfig();
        $flush = $event->isFlush();
        $this->repository->removeCampDateAttachmentConfig($entity, $flush);
    }

    #[AsEventListener(event: CampDateAttachmentConfigDeleteEvent::NAME, priority: 100)]
    public function onDeleteRemoveFromCampDateCollection(CampDateAttachmentConfigDeleteEvent $event): void
    {
        $entity = $event->getCampDateAttachmentConfig();
        $campDate = $entity->getCampDate();
        $campDate->removeCampDateAttachmentConfig($entity);
    }
}