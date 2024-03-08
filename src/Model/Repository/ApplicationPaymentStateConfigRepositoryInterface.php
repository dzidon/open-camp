<?php

namespace App\Model\Repository;

use App\Model\Entity\ApplicationPaymentStateConfig;

interface ApplicationPaymentStateConfigRepositoryInterface
{
    /**
     * Saves an application payment state config.
     *
     * @param ApplicationPaymentStateConfig $applicationPaymentStateConfig
     * @param bool $flush
     * @return void
     */
    public function saveApplicationPaymentStateConfig(ApplicationPaymentStateConfig $applicationPaymentStateConfig, bool $flush): void;

    /**
     * Removes an application payment state config.
     *
     * @param ApplicationPaymentStateConfig $applicationPaymentStateConfig
     * @param bool $flush
     * @return void
     */
    public function removeApplicationPaymentStateConfig(ApplicationPaymentStateConfig $applicationPaymentStateConfig, bool $flush): void;

    /**
     * Finds one by the given configuration or returns null.
     *
     * @param array $states
     * @param array $paidStates
     * @param array $cancelledStates
     * @param array $refundedStates
     * @param array $pendingStates
     * @param array $validStateChanges
     * @return ApplicationPaymentStateConfig
     */
    public function findOneByConfigurationOrCreateNew(array $states,
                                                      array $paidStates,
                                                      array $cancelledStates,
                                                      array $refundedStates,
                                                      array $pendingStates,
                                                      array $validStateChanges): ApplicationPaymentStateConfig;
}