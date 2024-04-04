<?php

namespace App\Model\Service\ApplicatonPayment\Offline;

/**
 * Interface for offline payment gates.
 */
interface ApplicationPaymentOfflineGateInterface
{
    /**
     * @return string[]
     */
    public function getStates(): array;

    /**
     * @return string
     */
    public function getInitialState(): string;

    /**
     * @return string[]
     */
    public function getPaidStates(): array;

    /**
     * @return string[]
     */
    public function getCancelledStates(): array;

    /**
     * @return string[]
     */
    public function getRefundedStates(): array;

    /**
     * @return string[]
     */
    public function getPendingStates(): array;

    /**
     * @return array
     */
    public function getValidStateChanges(): array;
}