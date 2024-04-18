<?php

namespace App\Model\Service\ApplicatonPayment\Offline;

use App\Model\Entity\Application;
use App\Model\Entity\ApplicationPayment;
use App\Model\Enum\Entity\ApplicationPaymentTypeEnum;

/**
 * Interface for offline payment gates.
 */
interface ApplicationPaymentOfflineGateInterface
{
    /**
     * Returns valid offline payment states.
     *
     * @return string[]
     */
    public function getStates(): array;

    /**
     * Creates an offline payment.
     *
     * @param ApplicationPaymentTypeEnum $type
     * @param Application $application
     * @return ApplicationPayment|null
     */
    public function createOfflinePayment(ApplicationPaymentTypeEnum $type, Application $application): ?ApplicationPayment;
}