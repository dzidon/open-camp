<?php

namespace App\Model\EventSubscriber\Admin\Application;

use App\Model\Event\Admin\Application\ApplicationDeleteEvent;
use App\Model\Event\Admin\ApplicationPayment\ApplicationPaymentOnlineRefundEvent;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Model\Service\Application\ApplicationInvoiceFilesystemInterface;
use App\Model\Service\ApplicationAdminAttachment\ApplicationAdminAttachmentFilesystemInterface;
use App\Model\Service\ApplicationAttachment\ApplicationAttachmentFilesystemInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ApplicationDeleteSubscriber
{
    private ApplicationRepositoryInterface $applicationRepository;

    private ApplicationAttachmentFilesystemInterface $applicationAttachmentFilesystem;

    private ApplicationAdminAttachmentFilesystemInterface $applicationAdminAttachmentFilesystem;

    private ApplicationInvoiceFilesystemInterface $applicationInvoiceFilesystem;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(ApplicationRepositoryInterface                $applicationRepository,
                                ApplicationAttachmentFilesystemInterface      $applicationAttachmentFilesystem,
                                ApplicationAdminAttachmentFilesystemInterface $applicationAdminAttachmentFilesystem,
                                ApplicationInvoiceFilesystemInterface         $applicationInvoiceFilesystem,
                                EventDispatcherInterface                      $eventDispatcher)
    {
        $this->applicationRepository = $applicationRepository;
        $this->applicationAttachmentFilesystem = $applicationAttachmentFilesystem;
        $this->applicationAdminAttachmentFilesystem = $applicationAdminAttachmentFilesystem;
        $this->applicationInvoiceFilesystem = $applicationInvoiceFilesystem;
        $this->eventDispatcher = $eventDispatcher;
    }

    #[AsEventListener(event: ApplicationDeleteEvent::NAME, priority: 500)]
    public function onDeleteRefundOnlinePayments(ApplicationDeleteEvent $event): void
    {
        $application = $event->getApplication();
        $applicationPayments = $application->getApplicationPayments();

        foreach ($applicationPayments as $applicationPayment)
        {
            if ($applicationPayment->isOnline() && $applicationPayment->isPaid())
            {
                $event = new ApplicationPaymentOnlineRefundEvent($applicationPayment);
                $event->setIsFlush(false);
                $this->eventDispatcher->dispatch($event, $event::NAME);
            }
        }
    }

    #[AsEventListener(event: ApplicationDeleteEvent::NAME, priority: 400)]
    public function onDeleteRemoveInvoice(ApplicationDeleteEvent $event): void
    {
        $application = $event->getApplication();
        $this->applicationInvoiceFilesystem->removeInvoiceFile($application);
    }

    #[AsEventListener(event: ApplicationDeleteEvent::NAME, priority: 300)]
    public function onDeleteRemoveUserAttachments(ApplicationDeleteEvent $event): void
    {
        $application = $event->getApplication();
        $applicationAttachments = $application->getApplicationAttachments();

        foreach ($applicationAttachments as $applicationAttachment)
        {
            $this->applicationAttachmentFilesystem->removeFile($applicationAttachment);
        }

        $applicationCampers = $application->getApplicationCampers();

        foreach ($applicationCampers as $applicationCamper)
        {
            $applicationCamperAttachments = $applicationCamper->getApplicationAttachments();

            foreach ($applicationCamperAttachments as $applicationCamperAttachment)
            {
                $this->applicationAttachmentFilesystem->removeFile($applicationCamperAttachment);
            }
        }
    }

    #[AsEventListener(event: ApplicationDeleteEvent::NAME, priority: 200)]
    public function onDeleteRemoveAdminAttachments(ApplicationDeleteEvent $event): void
    {
        $application = $event->getApplication();
        $applicationAdminAttachments = $application->getApplicationAdminAttachments();

        foreach ($applicationAdminAttachments as $applicationAdminAttachment)
        {
            $this->applicationAdminAttachmentFilesystem->removeFile($applicationAdminAttachment);
        }
    }

    #[AsEventListener(event: ApplicationDeleteEvent::NAME, priority: 100)]
    public function onDeleteRemoveEntity(ApplicationDeleteEvent $event): void
    {
        $entity = $event->getApplication();
        $flush = $event->isFlush();
        $this->applicationRepository->removeApplication($entity, $flush);
    }
}