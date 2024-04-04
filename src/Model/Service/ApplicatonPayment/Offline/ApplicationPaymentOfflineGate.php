<?php

namespace App\Model\Service\ApplicatonPayment\Offline;

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
    public function getInitialState(): string
    {
        return self::STATE_PAID;
    }

    /**
     * @inheritDoc
     */
    public function getPaidStates(): array
    {
        return [self::STATE_PAID];
    }

    /**
     * @inheritDoc
     */
    public function getCancelledStates(): array
    {
        return [self::STATE_CANCELLED];
    }

    /**
     * @inheritDoc
     */
    public function getRefundedStates(): array
    {
        return [self::STATE_REFUNDED];
    }

    /**
     * @inheritDoc
     */
    public function getPendingStates(): array
    {
        return [self::STATE_PENDING];
    }

    /**
     * @inheritDoc
     */
    public function getValidStateChanges(): array
    {
        $states = $this->getStates();

        return [
            self::STATE_PAID      => $states,
            self::STATE_CANCELLED => $states,
            self::STATE_REFUNDED  => $states,
            self::STATE_PENDING   => $states,
        ];
    }
}