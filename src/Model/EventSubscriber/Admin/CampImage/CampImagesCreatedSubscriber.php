<?php

namespace App\Model\EventSubscriber\Admin\CampImage;

use App\Model\Event\Admin\CampImage\CampImagesCreatedEvent;
use App\Model\Repository\CampImageRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CampImagesCreatedSubscriber
{
    private CampImageRepositoryInterface $repository;

    public function __construct(CampImageRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: CampImagesCreatedEvent::NAME)]
    public function onCreatedSaveEntities(CampImagesCreatedEvent $event): void
    {
        $entities = $event->getCampImages();

        foreach ($entities as $key => $entity)
        {
            $this->repository->saveCampImage($entity, $key === array_key_last($entities));
        }
    }
}