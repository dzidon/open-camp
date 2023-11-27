<?php

namespace App\Model\EventSubscriber\Admin\CampImage;

use App\Model\Event\Admin\CampImage\CampImageDeleteEvent;
use App\Model\Repository\CampImageRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CampImageDeleteSubscriber
{
    private CampImageRepositoryInterface $repository;

    public function __construct(CampImageRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: CampImageDeleteEvent::NAME)]
    public function onDeleteRemoveEntity(CampImageDeleteEvent $event): void
    {
        $entity = $event->getCampImage();
        $this->repository->removeCampImage($entity, true);
    }
}