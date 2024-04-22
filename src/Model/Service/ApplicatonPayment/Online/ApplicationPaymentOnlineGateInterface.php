<?php

namespace App\Model\Service\ApplicatonPayment\Online;

use App\Model\Entity\Application;
use App\Model\Entity\ApplicationPayment;
use App\Model\Enum\Entity\ApplicationPaymentTypeEnum;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface for online payment gates.
 */
interface ApplicationPaymentOnlineGateInterface
{
    /**
     * Tells the online payment gate to create an online payment for the given application.
     *
     * @param ApplicationPaymentTypeEnum $type
     * @param Application $application
     * @return ApplicationPayment|null
     */
    public function createOnlinePayment(ApplicationPaymentTypeEnum $type, Application $application): ?ApplicationPayment;

    /**
     * Used when a payment gate notifies us about payment state change.
     *
     * @param Request $request
     * @return ApplicationPayment|null
     */
    public function getApplicationPaymentFromExternalRequest(Request $request): ?ApplicationPayment;

    /**
     * Used when a payment gate notifies us about payment state change.
     *
     * @param Request $request
     * @return string|null
     */
    public function getStateFromExternalRequest(Request $request): ?string;

    /**
     * Tells the online payment gate to refund the given payment.
     *
     * @param ApplicationPayment $applicationPayment
     * @return void
     */
    public function refundPayment(ApplicationPayment $applicationPayment): void;
}