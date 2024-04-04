<?php

namespace App\Model\Service\ApplicatonPayment\Online;

use App\Model\Entity\Application;
use App\Model\Entity\ApplicationPayment;
use App\Model\Entity\ApplicationPaymentStateConfig;
use App\Model\Enum\Entity\ApplicationPaymentTypeEnum;
use App\Model\Repository\ApplicationPaymentRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;

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

    public function getStates(): array
    {
        return [self::STATE_PAID, self::STATE_CANCELLED, self::STATE_REFUNDED, self::STATE_PENDING];
    }

    public function getPaidStates(): array
    {
        return [self::STATE_PAID];
    }

    public function getCancelledStates(): array
    {
        return [self::STATE_CANCELLED];
    }

    public function getRefundedStates(): array
    {
        return [self::STATE_REFUNDED];
    }

    public function getPendingStates(): array
    {
        return [self::STATE_PENDING];
    }

    public function getValidStateChanges(): array
    {
        return [
            self::STATE_PENDING => [self::STATE_PAID, self::STATE_CANCELLED],
            self::STATE_PAID    => [self::STATE_REFUNDED],
        ];
    }

    public function createOnlinePayment(ApplicationPaymentTypeEnum    $type,
                                        Application                   $application,
                                        ApplicationPaymentStateConfig $stateConfig): ?ApplicationPayment
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

        return new ApplicationPayment(
            $amount,
            $type,
            self::STATE_PENDING,
            true,
            $stateConfig,
            $application,
            $externalIdString,
            $externalUrl,
        );
    }

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

    public function getNewStateFromExternalRequest(Request $request): ?string
    {
        $newState = $request->query->get('newState');

        return $newState !== null ? (string) $newState : null;
    }

    public function refundPayment(ApplicationPayment $applicationPayment): void
    {
        $applicationPayment->setState(self::STATE_REFUNDED);
    }
}