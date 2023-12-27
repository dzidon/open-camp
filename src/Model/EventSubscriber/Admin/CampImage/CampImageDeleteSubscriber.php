<?php

namespace App\Model\EventSubscriber\Admin\CampImage;

use App\Model\Event\Admin\CampImage\CampImageDeleteEvent;
use App\Model\Repository\CampImageRepositoryInterface;
use App\Model\Service\CampImage\CampImageFilesystemInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CampImageDeleteSubscriber
{
    private CampImageFilesystemInterface $campImageFilesystem;

    private CampImageRepositoryInterface $repository;

    public function __construct(CampImageFilesystemInterface $campImageFilesystem, CampImageRepositoryInterface $repository)
    {
        $this->campImageFilesystem = $campImageFilesystem;
        $this->repository = $repository;
    }

    #[AsEventListener(event: CampImageDeleteEvent::NAME, priority: 200)]
    public function onDeleteRemoveFile(CampImageDeleteEvent $event): void
    {
        $entity = $event->getCampImage();
        $this->campImageFilesystem->removeFile($entity);
    }

    #[AsEventListener(event: CampImageDeleteEvent::NAME, priority: 100)]
    public function onDeleteRemoveEntity(CampImageDeleteEvent $event): void
    {
        $entity = $event->getCampImage();
        $isFlush = $event->isFlush();
        $this->repository->removeCampImage($entity, $isFlush);
    }
}