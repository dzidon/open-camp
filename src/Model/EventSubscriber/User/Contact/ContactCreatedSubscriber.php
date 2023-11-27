<?php

namespace App\Model\EventSubscriber\User\Contact;

use App\Model\Event\User\Contact\ContactCreatedEvent;
use App\Model\Repository\ContactRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ContactCreatedSubscriber
{
    private ContactRepositoryInterface $repository;

    public function __construct(ContactRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    #[AsEventListener(event: ContactCreatedEvent::NAME)]
    public function onCreatedSaveEntity(ContactCreatedEvent $event): void
    {
        $entity = $event->getContact();
        $this->repository->saveContact($entity, true);
    }
}