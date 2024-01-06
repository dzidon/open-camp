<?php

namespace App\Model\EventSubscriber\User\ApplicationPurchasableItem;

use App\Model\Event\User\ApplicationPurchasableItem\ApplicationPurchasableItemDeleteEvent;
use App\Model\Repository\ApplicationPurchasableItemRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationPurchasableItemDeleteSubscriber
{
    private ApplicationPurchasableItemRepositoryInterface $repository;

    public function __construct(ApplicationPurchasableItemRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: ApplicationPurchasableItemDeleteEvent::NAME)]
    public function onDeleteRemoveEntity(ApplicationPurchasableItemDeleteEvent $event): void
    {
        $entity = $event->getApplicationPurchasableItem();
        $flush = $event->isFlush();
        $this->repository->removeApplicationPurchasableItem($entity, $flush);
    }
}