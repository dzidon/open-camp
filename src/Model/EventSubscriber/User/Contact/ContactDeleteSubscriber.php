<?php

namespace App\Model\EventSubscriber\User\Contact;

use App\Model\Event\User\Contact\ContactDeleteEvent;
use App\Model\Repository\ContactRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ContactDeleteSubscriber
{
    private ContactRepositoryInterface $repository;

    public function __construct(ContactRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: ContactDeleteEvent::NAME)]
    public function onDeleteRemoveEntity(ContactDeleteEvent $event): void
    {
        $entity = $event->getContact();
        $isFlush = $event->isFlush();
        $this->repository->removeContact($entity, $isFlush);
    }
}