<?php

namespace App\Model\EventSubscriber\Admin\Camp;

use App\Model\Event\Admin\Camp\CampDeleteEvent;
use App\Model\Repository\CampImageRepositoryInterface;
use App\Model\Repository\CampRepositoryInterface;
use App\Model\Service\CampImage\CampImageFilesystemInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CampDeleteSubscriber
{
    private CampImageFilesystemInterface $campImageFilesystem;

    private CampImageRepositoryInterface $campImageRepository;

    private CampRepositoryInterface $repository;

    public function __construct(CampImageFilesystemInterface $campImageFilesystem,
                                CampImageRepositoryInterface $campImageRepository,
                                CampRepositoryInterface      $repository)
    {
        $this->campImageFilesystem = $campImageFilesystem;
        $this->campImageRepository = $campImageRepository;
        $this->repository = $repository;
    }

    #[AsEventListener(event: CampDeleteEvent::NAME, priority: 200)]
    public function onDeleteRemoveImageFiles(CampDeleteEvent $event): void
    {
        $entity = $event->getCamp();
        $campImages = $this->campImageRepository->findByCamp($entity);

        foreach ($campImages as $campImage)
        {
            $this->campImageFilesystem->removeFile($campImage);
        }
    }

    #[AsEventListener(event: CampDeleteEvent::NAME, priority: 100)]
    public function onDeleteRemoveEntity(CampDeleteEvent $event): void
    {
        $entity = $event->getCamp();
        $isFlush = $event->isFlush();
        $this->repository->removeCamp($entity, $isFlush);
    }
}