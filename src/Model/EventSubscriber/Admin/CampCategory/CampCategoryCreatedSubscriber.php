<?php

namespace App\Model\EventSubscriber\Admin\CampCategory;

use App\Model\Event\Admin\CampCategory\CampCategoryCreatedEvent;
use App\Model\Repository\CampCategoryRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CampCategoryCreatedSubscriber
{
    private CampCategoryRepositoryInterface $repository;

    public function __construct(CampCategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: CampCategoryCreatedEvent::NAME)]
    public function onCreatedSaveEntity(CampCategoryCreatedEvent $event): void
    {
        $entity = $event->getCampCategory();
        $this->repository->saveCampCategory($entity, true);
    }
}