<?php

namespace App\Model\EventSubscriber\User\ApplicationContact;

use App\Model\Event\User\ApplicationContact\ApplicationContactDeleteEvent;
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
        $flush = $event->isFlush();
        $this->repository->removeApplicationContact($entity, $flush);
    }

    #[AsEventListener(event: ApplicationContactDeleteEvent::NAME, priority: 100)]
    public function onDeleteRemoveFromApplicationCollection(ApplicationContactDeleteEvent $event): void
    {
        $entity = $event->getApplicationContact();
        $application = $entity->getApplication();
        $application->removeApplicationContact($entity);
    }
}