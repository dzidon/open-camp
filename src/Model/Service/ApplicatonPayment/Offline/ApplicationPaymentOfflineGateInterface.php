<?php

namespace App\Model\Service\ApplicatonPayment\Offline;

use App\Model\Entity\Application;
use App\Model\Entity\ApplicationPayment;

/**
 * Creates offline payments for manual review.
 */
interface ApplicationPaymentOfflineGateInterface
{
    /**
     * Creates an offline deposit payment.
     *
     * @param Application $application
     * @return ApplicationPayment
     */
    public function createOfflineDepositPayment(Application $application): ApplicationPayment;

    /**
     * Creates an offline rest payment.
     *
     * @param Application $application
     * @return ApplicationPayment
     */
    public function createOfflineRestPayment(Application $application): ApplicationPayment;
}