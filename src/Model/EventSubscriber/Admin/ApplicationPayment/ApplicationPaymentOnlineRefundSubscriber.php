<?php

namespace App\Model\EventSubscriber\Admin\ApplicationPayment;

use App\Model\Event\Admin\ApplicationPayment\ApplicationPaymentOnlineRefundEvent;
use App\Model\Repository\ApplicationPaymentRepositoryInterface;
use App\Model\Service\ApplicatonPayment\Online\ApplicationPaymentOnlineGateInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class ApplicationPaymentOnlineRefundSubscriber
{
    private ApplicationPaymentRepositoryInterface $applicationPaymentRepository;

    private ApplicationPaymentOnlineGateInterface $applicationPaymentOnlineGate;

    public function __construct(ApplicationPaymentRepositoryInterface $applicationPaymentRepository,
                                ApplicationPaymentOnlineGateInterface $applicationPaymentOnlineGate)
    {
        $this->applicationPaymentRepository = $applicationPaymentRepository;
        $this->applicationPaymentOnlineGate = $applicationPaymentOnlineGate;
    }

    #[AsEventListener(event: ApplicationPaymentOnlineRefundEvent::NAME, priority: 200)]
    public function onRefundUsePaymentGate(ApplicationPaymentOnlineRefundEvent $event): void
    {
        $entity = $event->getApplicationPayment();
        $this->applicationPaymentOnlineGate->refundPayment($entity);
    }

    #[AsEventListener(event: ApplicationPaymentOnlineRefundEvent::NAME, priority: 100)]
    public function onRefundSaveEntity(ApplicationPaymentOnlineRefundEvent $event): void
    {
        $entity = $event->getApplicationPayment();
        $flush = $event->isFlush();
        $this->applicationPaymentRepository->saveApplicationPayment($entity, $flush);
    }
}