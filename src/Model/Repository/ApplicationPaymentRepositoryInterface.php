<?php

namespace App\Model\Repository;

use App\Model\Entity\ApplicationPayment;

interface ApplicationPaymentRepositoryInterface
{
    /**
     * Saves an application payment.
     *
     * @param ApplicationPayment $applicationPayment
     * @param bool $flush
     * @return void
     */
    public function saveApplicationPayment(ApplicationPayment $applicationPayment, bool $flush): void;

    /**
     * Removes an application payment.
     *
     * @param ApplicationPayment $applicationPayment
     * @param bool $flush
     * @return void
     */
    public function removeApplicationPayment(ApplicationPayment $applicationPayment, bool $flush): void;

    /**
     * Finds one online payment by the given external id.
     *
     * @param string $externalId
     * @return ApplicationPayment|null
     */
    public function findOneByExternalId(string $externalId): ?ApplicationPayment;
}