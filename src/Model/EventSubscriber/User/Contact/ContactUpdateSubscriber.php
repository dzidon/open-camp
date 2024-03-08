<?php

namespace App\Model\EventSubscriber\User\Contact;

use App\Model\Event\User\Contact\ContactUpdateEvent;
use App\Model\Repository\ContactRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ContactUpdateSubscriber
{
    private ContactRepositoryInterface $repository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(ContactRepositoryInterface    $repository,
                                DataTransferRegistryInterface $dataTransfer)
    {
        $this->repository = $repository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: ContactUpdateEvent::NAME, priority: 200)]
    public function onUpdateFillEntity(ContactUpdateEvent $event): void
    {
        $data = $event->getContactData();
        $entity = $event->getContact();
        $this->dataTransfer->fillEntity($data, $entity);
    }

    #[AsEventListener(event: ContactUpdateEvent::NAME, priority: 100)]
    public function onUpdateSaveEntity(ContactUpdateEvent $event): void
    {
        $entity = $event->getContact();
        $isFlush = $event->isFlush();
        $this->repository->saveContact($entity, $isFlush);
    }
}