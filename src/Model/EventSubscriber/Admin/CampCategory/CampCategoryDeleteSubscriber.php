<?php

namespace App\Model\EventSubscriber\Admin\CampCategory;

use App\Model\Event\Admin\CampCategory\CampCategoryDeleteEvent;
use App\Model\Repository\CampCategoryRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CampCategoryDeleteSubscriber
{
    private CampCategoryRepositoryInterface $repository;

    public function __construct(CampCategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: CampCategoryDeleteEvent::NAME)]
    public function onDeleteRemoveEntity(CampCategoryDeleteEvent $event): void
    {
        $entity = $event->getCampCategory();
        $this->repository->removeCampCategory($entity, true);
    }
}