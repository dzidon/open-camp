<?php

namespace App\Model\EventSubscriber\User\Contact;

use App\Model\Entity\Contact;
use App\Model\Event\User\Contact\ContactCreateEvent;
use App\Model\Repository\ContactRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ContactCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private ContactRepositoryInterface $repository;

    public function __construct(DataTransferRegistryInterface $dataTransfer, ContactRepositoryInterface $repository)
    {
        $this->dataTransfer = $dataTransfer;
        $this->repository = $repository;
    }

    #[AsEventListener(event: ContactCreateEvent::NAME, priority: 200)]
    public function onCreateFillEntity(ContactCreateEvent $event): void
    {
        $data = $event->getContactData();
        $user = $event->getUser();
        $entity = new Contact($data->getNameFirst(), $data->getNameLast(), $user, $data->getRole(), $data->getRoleOther());
        $this->dataTransfer->fillEntity($data, $entity);
        $event->setContact($entity);
    }

    #[AsEventListener(event: ContactCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(ContactCreateEvent $event): void
    {
        $entity = $event->getContact();
        $isFlush = $event->isFlush();
        $this->repository->saveContact($entity, $isFlush);
    }
}