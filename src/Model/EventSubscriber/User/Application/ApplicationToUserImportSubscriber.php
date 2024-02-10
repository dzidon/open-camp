<?php

namespace App\Model\EventSubscriber\User\Application;

use App\Model\Event\User\Application\ApplicationToUserImportEvent;
use App\Model\Event\User\Application\ApplicationToUserImportSkipEvent;
use App\Model\Repository\UserRepositoryInterface;
use App\Model\Service\Application\ApplicationToUserImporterInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;

class ApplicationToUserImportSubscriber
{
    private ApplicationToUserImporterInterface $applicationToUserImporter;

    private UserRepositoryInterface $userRepository;

    private RequestStack $requestStack;

    private string $lastCompletedApplicationIdSessionKey;

    public function __construct(ApplicationToUserImporterInterface $applicationToUserImporter,
                                UserRepositoryInterface            $userRepository,
                                RequestStack                       $requestStack,
                                string                             $lastCompletedApplicationIdSessionKey)
    {
        $this->applicationToUserImporter = $applicationToUserImporter;
        $this->userRepository = $userRepository;
        $this->requestStack = $requestStack;
        $this->lastCompletedApplicationIdSessionKey = $lastCompletedApplicationIdSessionKey;
    }

    #[AsEventListener(event: ApplicationToUserImportEvent::NAME, priority: 300)]
    public function onImportUpdateUser(ApplicationToUserImportEvent $event): void
    {
        $data = $event->getApplicationImportToUserData();
        $this->applicationToUserImporter->importApplicationDataToUser($data);
    }

    #[AsEventListener(event: ApplicationToUserImportEvent::NAME, priority: 200)]
    public function onImportSaveUser(ApplicationToUserImportEvent $event): void
    {
        $data = $event->getApplicationImportToUserData();
        $user = $data->getUser();
        $isFlush = $event->isFlush();
        $this->userRepository->saveUser($user, $isFlush);
    }

    #[AsEventListener(event: ApplicationToUserImportSkipEvent::NAME)]
    #[AsEventListener(event: ApplicationToUserImportEvent::NAME, priority: 100)]
    public function onImportClearSessionVariable(): void
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        $session = $currentRequest->getSession();
        $session->remove($this->lastCompletedApplicationIdSessionKey);
    }
}