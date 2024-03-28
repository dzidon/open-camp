<?php

namespace App\Model\EventSubscriber\Admin\ApplicationContact;

use App\Model\Event\Admin\ApplicationContact\ApplicationContactDeleteEvent;
use App\Model\Repository\ApplicationContactRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationContactDeleteSubscriber
{
    private ApplicationContactRepositoryInterface $repository;

    public function __construct(ApplicationContactRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: ApplicationContactDeleteEvent::NAME, priority: 200)]
    public function onDeleteRemoveEntity(ApplicationContactDeleteEvent $event): void
    {
        $entity = $event->getApplicationContact();
        $isFlush = $event->isFlush();
        $this->repository->removeApplicationContact($entity, $isFlush);
    }

    #[AsEventListener(event: ApplicationContactDeleteEvent::NAME, priority: 100)]
    public function onDeleteRemoveFromApplicationCollection(ApplicationContactDeleteEvent $event): void
    {
        $entity = $event->getApplicationContact();
        $application = $entity->getApplication();
        $application->removeApplicationContact($entity);
    }
}