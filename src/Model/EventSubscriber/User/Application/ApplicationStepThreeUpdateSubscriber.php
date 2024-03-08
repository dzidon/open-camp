<?php

namespace App\Model\EventSubscriber\User\Application;

use App\Model\Entity\User;
use App\Model\Event\User\Application\ApplicationStepThreeUpdateEvent;
use App\Model\Event\User\ApplicationPayment\ApplicationPaymentOfflineDepositCreateEvent;
use App\Model\Event\User\ApplicationPayment\ApplicationPaymentOfflineRestCreateEvent;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Model\Service\Application\ApplicationCompleterInterface;
use App\Model\Service\Application\ApplicationInvoiceFilesystemInterface;
use App\Service\Mailer\ApplicationCompletedMailerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ApplicationStepThreeUpdateSubscriber
{
    private ApplicationRepositoryInterface $applicationRepository;

    private ApplicationCompleterInterface $applicationCompleter;

    private ApplicationInvoiceFilesystemInterface $applicationInvoiceFilesystem;

    private ApplicationCompletedMailerInterface $applicationCompletedMailer;

    private EventDispatcherInterface $eventDispatcher;

    private RequestStack $requestStack;

    private Security $security;

    private string $lastCompletedApplicationIdSessionKey;

    public function __construct(ApplicationRepositoryInterface        $applicationRepository,
                                ApplicationCompleterInterface         $applicationCompleter,
                                ApplicationInvoiceFilesystemInterface $applicationInvoiceFilesystem,
                                ApplicationCompletedMailerInterface   $applicationCompletedMailer,
                                EventDispatcherInterface              $eventDispatcher,
                                RequestStack                          $requestStack,
                                Security                              $security,
                                string                                $lastCompletedApplicationIdSessionKey)
    {
        $this->applicationRepository = $applicationRepository;
        $this->applicationCompleter = $applicationCompleter;
        $this->applicationInvoiceFilesystem = $applicationInvoiceFilesystem;
        $this->applicationCompletedMailer = $applicationCompletedMailer;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
        $this->security = $security;
        $this->lastCompletedApplicationIdSessionKey = $lastCompletedApplicationIdSessionKey;
    }

    #[AsEventListener(event: ApplicationStepThreeUpdateEvent::NAME, priority: 600)]
    public function onCompleteUpdateApplication(ApplicationStepThreeUpdateEvent $event): void
    {
        /** @var User|null $user */
        $user = $this->security->getUser();
        $application = $event->getApplication();
        $this->applicationCompleter->completeApplication($application, $user);
    }

    #[AsEventListener(event: ApplicationStepThreeUpdateEvent::NAME, priority: 500)]
    public function onCompleteCreateOfflinePayments(ApplicationStepThreeUpdateEvent $event): void
    {
        $application = $event->getApplication();

        if (!$application->isPaymentMethodOffline())
        {
            return;
        }

        if ($application->canCreateNewDepositPayment())
        {
            $event = new ApplicationPaymentOfflineDepositCreateEvent($application);
            $event->setIsFlush(false);
            $this->eventDispatcher->dispatch($event, $event::NAME);
        }

        if ($application->canCreateNewRestPayment())
        {
            $event = new ApplicationPaymentOfflineRestCreateEvent($application);
            $event->setIsFlush(false);
            $this->eventDispatcher->dispatch($event, $event::NAME);
        }
    }

    #[AsEventListener(event: ApplicationStepThreeUpdateEvent::NAME, priority: 400)]
    public function onCompleteSaveApplication(ApplicationStepThreeUpdateEvent $event): void
    {
        $application = $event->getApplication();
        $isFlush = $event->isFlush();
        $this->applicationRepository->saveApplication($application, $isFlush);
    }

    #[AsEventListener(event: ApplicationStepThreeUpdateEvent::NAME, priority: 300)]
    public function onCompleteCreateInvoice(ApplicationStepThreeUpdateEvent $event): void
    {
        $application = $event->getApplication();
        $this->applicationInvoiceFilesystem->createInvoiceFile($application);
    }

    #[AsEventListener(event: ApplicationStepThreeUpdateEvent::NAME, priority: 200)]
    public function onCompleteSendEmail(ApplicationStepThreeUpdateEvent $event): void
    {
        $application = $event->getApplication();
        $this->applicationCompletedMailer->sendEmail($application);
    }

    #[AsEventListener(event: ApplicationStepThreeUpdateEvent::NAME, priority: 100)]
    public function onCompleteSetSessionVariable(ApplicationStepThreeUpdateEvent $event): void
    {
        $application = $event->getApplication();
        $applicationIdString = $application
            ->getId()
            ->toRfc4122()
        ;

        $currentRequest = $this->requestStack->getCurrentRequest();
        $session = $currentRequest->getSession();
        $session->set($this->lastCompletedApplicationIdSessionKey, $applicationIdString);
    }
}