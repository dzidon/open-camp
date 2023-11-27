<?php

namespace App\Model\EventSubscriber\Admin\CampDate;

use App\Model\Event\Admin\CampDate\CampDateCreatedEvent;
use App\Model\Repository\CampDateRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CampDateCreatedSubscriber
{
    private CampDateRepositoryInterface $repository;

    public function __construct(CampDateRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: CampDateCreatedEvent::NAME)]
    public function onCreatedSaveEntity(CampDateCreatedEvent $event): void
    {
        $entity = $event->getCampDate();
        $this->repository->saveCampDate($entity, true);
    }
}