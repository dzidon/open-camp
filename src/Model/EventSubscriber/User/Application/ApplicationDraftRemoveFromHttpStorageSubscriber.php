<?php

namespace App\Model\EventSubscriber\User\Application;

use App\Model\Event\User\Application\ApplicationDraftRemoveFromHttpStorageEvent;
use App\Model\Service\Application\ApplicationDraftHttpStorageInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationDraftRemoveFromHttpStorageSubscriber
{
    private ApplicationDraftHttpStorageInterface $applicationDraftHttpStorage;

    public function __construct(ApplicationDraftHttpStorageInterface $applicationDraftHttpStorage)
    {
        $this->applicationDraftHttpStorage = $applicationDraftHttpStorage;
    }

    #[AsEventListener(event: ApplicationDraftRemoveFromHttpStorageEvent::NAME)]
    public function onCreateInstantiateEntity(ApplicationDraftRemoveFromHttpStorageEvent $event): void
    {
        $application = $event->getApplication();
        $response = $event->getResponse();
        $this->applicationDraftHttpStorage->removeApplicationDraft($application, $response);
    }
}