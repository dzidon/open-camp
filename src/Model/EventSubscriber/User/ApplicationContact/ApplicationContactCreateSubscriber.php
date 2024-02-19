<?php

namespace App\Model\EventSubscriber\User\ApplicationContact;

use App\Model\Entity\ApplicationContact;
use App\Model\Event\User\ApplicationContact\ApplicationContactCreateEvent;
use App\Model\Repository\ApplicationContactRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationContactCreateSubscriber
{
    private ApplicationContactRepositoryInterface $applicationContactRepository;

    private DataTransferRegistryInterface $dataTransfer;

    public function __construct(ApplicationContactRepositoryInterface $applicationContactRepository, DataTransferRegistryInterface $dataTransfer)
    {
        $this->applicationContactRepository = $applicationContactRepository;
        $this->dataTransfer = $dataTransfer;
    }

    #[AsEventListener(event: ApplicationContactCreateEvent::NAME, priority: 200)]
    public function onCreateFillEntity(ApplicationContactCreateEvent $event): void
    {
        $data = $event->getContactData();
        $priority = $event->getPriority();
        $application = $event->getApplication();
        $applicationContact = new ApplicationContact(
            $data->getNameFirst(),
            $data->getNameLast(),
            $application,
            $priority,
            $data->getRole(),
            $data->getRoleOther()
        );

        $this->dataTransfer->fillEntity($data, $applicationContact);
        $event->setApplicationContact($applicationContact);
    }

    #[AsEventListener(event: ApplicationContactCreateEvent::NAME, priority: 100)]
    public function onCreateSaveEntity(ApplicationContactCreateEvent $event): void
    {
        $applicationContact = $event->getApplicationContact();
        $isFlush = $event->isFlush();
        $this->applicationContactRepository->saveApplicationContact($applicationContact, $isFlush);
    }
}