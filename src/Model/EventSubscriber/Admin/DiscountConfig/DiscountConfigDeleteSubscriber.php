<?php

namespace App\Model\EventSubscriber\Admin\DiscountConfig;

use App\Model\Event\Admin\DiscountConfig\DiscountConfigDeleteEvent;
use App\Model\Repository\DiscountConfigRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class DiscountConfigDeleteSubscriber
{
    private DiscountConfigRepositoryInterface $repository;

    public function __construct(DiscountConfigRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: DiscountConfigDeleteEvent::NAME)]
    public function onDeleteRemoveEntity(DiscountConfigDeleteEvent $event): void
    {
        $entity = $event->getDiscountConfig();
        $flush = $event->isFlush();
        $this->repository->removeDiscountConfig($entity, $flush);
    }
}