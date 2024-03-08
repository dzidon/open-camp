<?php

namespace App\Model\Service\ApplicatonPayment\Offline;

use App\Model\Entity\Application;
use App\Model\Entity\ApplicationPayment;
use App\Model\Enum\Entity\ApplicationPaymentTypeEnum;
use App\Model\Repository\ApplicationPaymentStateConfigRepositoryInterface;

/**
 * @inheritDoc
 */
class ApplicationPaymentOfflineGate implements ApplicationPaymentOfflineGateInterface
{
    const STATE_PAID = 'PAID';
    const STATE_CANCELLED = 'CANCELLED';
    const STATE_REFUNDED = 'REFUNDED';
    const STATE_PENDING = 'PENDING';

    const STATES = [self::STATE_PAID, self::STATE_CANCELLED, self::STATE_REFUNDED, self::STATE_PENDING];
    const PAID_STATES = [self::STATE_PAID];
    const CANCELLED_STATES = [self::STATE_CANCELLED];
    const REFUNDED_STATES = [self::STATE_REFUNDED];
    const PENDING_STATES = [self::STATE_PENDING];
    const VALID_STATE_CHANGES = [
        self::STATE_PAID      => [self::STATE_CANCELLED, self::STATE_REFUNDED, self::STATE_PENDING],
        self::STATE_CANCELLED => [self::STATE_PAID, self::STATE_REFUNDED, self::STATE_PENDING],
        self::STATE_REFUNDED  => [self::STATE_PAID, self::STATE_CANCELLED, self::STATE_PENDING],
        self::STATE_PENDING   => [self::STATE_PAID, self::STATE_CANCELLED, self::STATE_REFUNDED],
    ];

    private ApplicationPaymentStateConfigRepositoryInterface $applicationPaymentStateConfigRepository;

    public function __construct(ApplicationPaymentStateConfigRepositoryInterface $applicationPaymentStateConfigRepository)
    {
        $this->applicationPaymentStateConfigRepository = $applicationPaymentStateConfigRepository;
    }

    /**
     * @inheritDoc
     */
    public function createOfflineDepositPayment(Application $application): ApplicationPayment
    {
        $depositAmount = $application->getFullDeposit();
        $stateConfig = $this->applicationPaymentStateConfigRepository->findOneByConfigurationOrCreateNew(
            self::STATES,
            self::PAID_STATES,
            self::CANCELLED_STATES,
            self::REFUNDED_STATES,
            self::PENDING_STATES,
            self::VALID_STATE_CHANGES,
        );

        return new ApplicationPayment(
            $depositAmount,
            ApplicationPaymentTypeEnum::DEPOSIT,
            self::STATE_PENDING,
            false,
            $stateConfig,
            $application,
        );
    }

    /**
     * @inheritDoc
     */
    public function createOfflineRestPayment(Application $application): ApplicationPayment
    {
        $restAmount = $application->getFullPriceWithoutDeposit();
        $stateConfig = $this->applicationPaymentStateConfigRepository->findOneByConfigurationOrCreateNew(
            self::STATES,
            self::PAID_STATES,
            self::CANCELLED_STATES,
            self::REFUNDED_STATES,
            self::PENDING_STATES,
            self::VALID_STATE_CHANGES,
        );

        return new ApplicationPayment(
            $restAmount,
            ApplicationPaymentTypeEnum::REST,
            self::STATE_PENDING,
            false,
            $stateConfig,
            $application,
        );
    }
}