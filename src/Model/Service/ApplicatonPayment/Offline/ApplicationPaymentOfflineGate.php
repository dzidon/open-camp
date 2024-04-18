<?php

namespace App\Model\Service\ApplicatonPayment\Offline;

use App\Model\Entity\Application;
use App\Model\Entity\ApplicationPayment;
use App\Model\Enum\Entity\ApplicationPaymentTypeEnum;

/**
 * @inheritDoc
 */
class ApplicationPaymentOfflineGate implements ApplicationPaymentOfflineGateInterface
{
    const STATE_PAID = 'PAID';
    const STATE_CANCELLED = 'CANCELLED';
    const STATE_REFUNDED = 'REFUNDED';
    const STATE_PENDING = 'PENDING';

    /**
     * @inheritDoc
     */
    public function getStates(): array
    {
        return [self::STATE_PAID, self::STATE_CANCELLED, self::STATE_REFUNDED, self::STATE_PENDING];
    }

    /**
     * @inheritDoc
     */
    public function createOfflinePayment(ApplicationPaymentTypeEnum $type, Application $application): ?ApplicationPayment
    {
        $amount = match ($type)
        {
            ApplicationPaymentTypeEnum::DEPOSIT => $application->getFullDeposit(),
            ApplicationPaymentTypeEnum::REST    => $application->getFullRest(),
            ApplicationPaymentTypeEnum::FULL    => $application->getFullPrice(),
        };

        $states = $this->getStates();
        $statesPaid = [self::STATE_PAID];
        $statesCancelled = [self::STATE_CANCELLED];
        $statesRefunded = [self::STATE_REFUNDED];
        $statesPending = [self::STATE_PENDING];
        $validStateChanges = [
            self::STATE_PAID      => $states,
            self::STATE_CANCELLED => $states,
            self::STATE_REFUNDED  => $states,
            self::STATE_PENDING   => $states,
        ];

        return new ApplicationPayment(
            $amount,
            $type,
            self::STATE_PENDING,
            false,
            $application,
            $states,
            $statesPaid,
            $statesCancelled,
            $statesRefunded,
            $statesPending,
            $validStateChanges,
        );
    }
}