<?php

namespace App\Model\EventSubscriber\User\Contact;

use App\Model\Entity\Contact;
use App\Model\Event\User\Contact\ContactCreatedEvent;
use App\Model\Event\User\Contact\ContactCreateEvent;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ContactCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(DataTransferRegistryInterface $dataTransfer, EventDispatcherInterface $eventDispatcher)
    {
        $this->dataTransfer = $dataTransfer;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[AsEventListener(event: ContactCreateEvent::NAME)]
    public function onCreateFillEntity(ContactCreateEvent $event): void
    {
        $data = $event->getContactData();
        $user = $event->getUser();
        $entity = new Contact($data->getNameFirst(), $data->getNameLast(), $user, $data->getRole(), $data->getRoleOther());
        $this->dataTransfer->fillEntity($data, $entity);

        $event = new ContactCreatedEvent($data, $entity);
        $this->eventDispatcher->dispatch($event, ContactCreatedEvent::NAME);
    }
}