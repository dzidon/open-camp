<?php

namespace App\Model\EventSubscriber\Admin\CampDate;

use App\Model\Event\Admin\CampDate\CampDateDeleteEvent;
use App\Model\Repository\CampDateRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class CampDateDeleteSubscriber
{
    private CampDateRepositoryInterface $repository;

    public function __construct(CampDateRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: CampDateDeleteEvent::NAME)]
    public function onDeleteRemoveEntity(CampDateDeleteEvent $event): void
    {
        $entity = $event->getCampDate();
        $this->repository->removeCampDate($entity, true);
    }
}