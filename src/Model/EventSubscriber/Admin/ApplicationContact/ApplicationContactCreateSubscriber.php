<?php

namespace App\Model\EventSubscriber\Admin\ApplicationContact;

use App\Model\Entity\ApplicationContact;
use App\Model\Event\Admin\ApplicationContact\ApplicationContactCreateEvent;
use App\Model\Repository\ApplicationContactRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationContactCreateSubscriber
{
    private DataTransferRegistryInterface $dataTransfer;

    private ApplicationContactRepositoryInterface $repository;

    public function __construct(DataTransferRegistryInterface $dataTransfer, ApplicationContactRepositoryInterface $repository)
    {
        $this->dataTransfer = $dataTransfer;
        $this->repository = $repository;
    }

    #[AsEventListener(event: ApplicationContactCreateEvent::NAME, priority: 200)]
    public function onCreateFillEntity(ApplicationContactCreateEvent $event): void
    {
        $data = $event->getContactData();
        $application = $event->getApplication();
        $priority = $application->getLowestApplicationContactPriority() - 1;
        $entity = new ApplicationContact(
            $data->getNameFirst(),
            $data->getNameLast(),
            $application,
            $priority,
            $data->getRole(),
            $data->getRoleOther()
        );

        $this->dataTransfer->fillEntity($data, $entity);
        $event->setApplicationContact($entity);
    }

    #[AsEventListener(event: ApplicationContactCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(ApplicationContactCreateEvent $event): void
    {
        $entity = $event->getApplicationContact();
        $isFlush = $event->isFlush();
        $this->repository->saveApplicationContact($entity, $isFlush);
    }
}