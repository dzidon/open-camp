<?php

namespace App\Model\EventSubscriber\Admin\ApplicationPayment;

use App\Model\Event\Admin\ApplicationPayment\ApplicationPaymentOfflineDeleteEvent;
use App\Model\Repository\ApplicationPaymentRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationPaymentOfflineDeleteSubscriber
{
    private ApplicationPaymentRepositoryInterface $repository;

    public function __construct(ApplicationPaymentRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: ApplicationPaymentOfflineDeleteEvent::NAME, priority: 200)]
    public function onDeleteRemoveEntity(ApplicationPaymentOfflineDeleteEvent $event): void
    {
        $entity = $event->getApplicationPayment();
        $isFlush = $event->isFlush();
        $this->repository->removeApplicationPayment($entity, $isFlush);
    }

    #[AsEventListener(event: ApplicationPaymentOfflineDeleteEvent::NAME, priority: 100)]
    public function onDeleteRemoveFromApplicationCollection(ApplicationPaymentOfflineDeleteEvent $event): void
    {
        $entity = $event->getApplicationPayment();
        $application = $entity->getApplication();
        $application->removeApplicationPayment($entity);
    }
}