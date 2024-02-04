<?php

namespace App\Model\EventSubscriber\User\Application;

use App\Model\Event\User\Application\ApplicationDraftStoreInHttpStorageEvent;
use App\Model\Service\Application\ApplicationDraftHttpStorageInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationDraftStoreInHttpStorageSubscriber
{
    private ApplicationDraftHttpStorageInterface $applicationDraftHttpStorage;

    public function __construct(ApplicationDraftHttpStorageInterface $applicationDraftHttpStorage)
    {
        $this->applicationDraftHttpStorage = $applicationDraftHttpStorage;
    }

    #[AsEventListener(event: ApplicationDraftStoreInHttpStorageEvent::NAME)]
    public function onCreateInstantiateEntity(ApplicationDraftStoreInHttpStorageEvent $event): void
    {
        $application = $event->getApplication();
        $response = $event->getResponse();
        $this->applicationDraftHttpStorage->storeApplicationDraft($application, $response);
    }
}