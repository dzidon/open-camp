<?php

namespace App\Model\Service\ApplicatonPayment\Online;

use App\Model\Entity\Application;
use App\Model\Entity\ApplicationPayment;
use App\Model\Enum\Entity\ApplicationPaymentTypeEnum;
use App\Model\Repository\ApplicationPaymentRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;

/**
 * @inheritDoc
 */
class ApplicationPaymentMockOnlineGate implements ApplicationPaymentOnlineGateInterface
{
    const STATE_PAID = 'PAID';
    const STATE_CANCELLED = 'CANCELLED';
    const STATE_REFUNDED = 'REFUNDED';
    const STATE_PENDING = 'PENDING';

    private ApplicationPaymentRepositoryInterface $applicationPaymentRepository;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(ApplicationPaymentRepositoryInterface $applicationPaymentRepository,
                                UrlGeneratorInterface                 $urlGenerator)
    {
        $this->applicationPaymentRepository = $applicationPaymentRepository;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @inheritDoc
     */
    public function createOnlinePayment(ApplicationPaymentTypeEnum $type, Application $application): ?ApplicationPayment
    {
        $amount = match ($type)
        {
            ApplicationPaymentTypeEnum::DEPOSIT => $application->getFullDeposit(),
            ApplicationPaymentTypeEnum::REST    => $application->getFullRest(),
            ApplicationPaymentTypeEnum::FULL    => $application->getFullPrice(),
        };

        $externalIdString = (Uuid::v4())->toRfc4122();
        $externalUrl = $this->urlGenerator->generate('user_application_payment_simulate', [
            'externalId' => $externalIdString,
        ]);

        $states = [self::STATE_PAID, self::STATE_CANCELLED, self::STATE_REFUNDED, self::STATE_PENDING];
        $statesPaid = [self::STATE_PAID];
        $statesCancelled = [self::STATE_CANCELLED];
        $statesRefunded = [self::STATE_REFUNDED];
        $statesPending = [self::STATE_PENDING];
        $validStateChanges = [
            self::STATE_PENDING => [self::STATE_PAID, self::STATE_CANCELLED],
            self::STATE_PAID    => [self::STATE_REFUNDED],
        ];

        return new ApplicationPayment(
            $amount,
            $type,
            self::STATE_PENDING,
            true,
            $application,
            $states,
            $statesPaid,
            $statesCancelled,
            $statesRefunded,
            $statesPending,
            $validStateChanges,
            $externalIdString,
            $externalUrl,
        );
    }

    /**
     * @inheritDoc
     */
    public function getApplicationPaymentFromExternalRequest(Request $request): ?ApplicationPayment
    {
        $externalId = $request->query->get('externalId');
        $externalId = $externalId !== null ? (string) $externalId : null;

        if ($externalId === null)
        {
            return null;
        }

        return $this->applicationPaymentRepository->findOneByExternalId($externalId);
    }

    /**
     * @inheritDoc
     */
    public function getStateFromExternalRequest(Request $request): ?string
    {
        $newState = $request->query->get('newState');

        return $newState !== null ? (string) $newState : null;
    }

    /**
     * @inheritDoc
     */
    public function refundPayment(ApplicationPayment $applicationPayment): void
    {
        $applicationPayment->setState(self::STATE_REFUNDED);
    }
}