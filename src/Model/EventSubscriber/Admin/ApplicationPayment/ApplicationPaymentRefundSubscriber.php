<?php

namespace App\Model\EventSubscriber\Admin\ApplicationPayment;

use App\Model\Event\Admin\ApplicationPayment\ApplicationPaymentRefundEvent;
use App\Model\Repository\ApplicationPaymentRepositoryInterface;
use App\Model\Service\ApplicatonPayment\Online\ApplicationPaymentOnlineGateInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationPaymentRefundSubscriber
{
    private ApplicationPaymentRepositoryInterface $applicationPaymentRepository;

    private ApplicationPaymentOnlineGateInterface $applicationPaymentOnlineGate;

    public function __construct(ApplicationPaymentRepositoryInterface $applicationPaymentRepository,
                                ApplicationPaymentOnlineGateInterface $applicationPaymentOnlineGate)
    {
        $this->applicationPaymentRepository = $applicationPaymentRepository;
        $this->applicationPaymentOnlineGate = $applicationPaymentOnlineGate;
    }

    #[AsEventListener(event: ApplicationPaymentRefundEvent::NAME, priority: 200)]
    public function onRefundUsePaymentGate(ApplicationPaymentRefundEvent $event): void
    {
        $entity = $event->getApplicationPayment();
        $this->applicationPaymentOnlineGate->refundPayment($entity);
    }

    #[AsEventListener(event: ApplicationPaymentRefundEvent::NAME, priority: 100)]
    public function onRefundSaveEntity(ApplicationPaymentRefundEvent $event): void
    {
        $entity = $event->getApplicationPayment();
        $flush = $event->isFlush();
        $this->applicationPaymentRepository->saveApplicationPayment($entity, $flush);
    }
}