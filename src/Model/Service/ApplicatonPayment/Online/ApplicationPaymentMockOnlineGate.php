<?php

namespace App\Model\Service\ApplicatonPayment\Online;

use App\Model\Entity\Application;
use App\Model\Entity\ApplicationPayment;
use App\Model\Enum\Entity\ApplicationPaymentTypeEnum;
use App\Model\Repository\ApplicationPaymentRepositoryInterface;
use App\Model\Repository\ApplicationPaymentStateConfigRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Uid\Uuid;

class ApplicationPaymentMockOnlineGate implements ApplicationPaymentOnlineGateInterface
{
    const STATE_PAID = 'PAID';
    const STATE_CANCELLED = 'CANCELLED';
    const STATE_REFUNDED = 'REFUNDED';
    const STATE_PENDING = 'PENDING';

    const STATES = [self::STATE_PAID, self::STATE_CANCELLED, self::STATE_REFUNDED, self::STATE_PENDING];
    const PAID_STATES = [self::STATE_PAID];
    const CANCELLED_STATES = [self::STATE_CANCELLED];
    const REFUNDED_STATES = [self::STATE_REFUNDED];
    const PENDING_STATES = [self::STATE_PENDING];
    const VALID_STATE_CHANGES = [
        self::STATE_PENDING => [self::STATE_PAID, self::STATE_CANCELLED],
        self::STATE_PAID    => [self::STATE_REFUNDED],
    ];

    private ApplicationPaymentRepositoryInterface $applicationPaymentRepository;

    private ApplicationPaymentStateConfigRepositoryInterface $applicationPaymentStateConfigRepository;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(ApplicationPaymentRepositoryInterface            $applicationPaymentRepository,
                                ApplicationPaymentStateConfigRepositoryInterface $applicationPaymentStateConfigRepository,
                                UrlGeneratorInterface                            $urlGenerator)
    {
        $this->applicationPaymentRepository = $applicationPaymentRepository;
        $this->applicationPaymentStateConfigRepository = $applicationPaymentStateConfigRepository;
        $this->urlGenerator = $urlGenerator;
    }

    public function createOnlinePayment(ApplicationPaymentTypeEnum $type, Application $application): ?ApplicationPayment
    {
        $amount = match ($type)
        {
            ApplicationPaymentTypeEnum::DEPOSIT => $application->getFullDeposit(),
            ApplicationPaymentTypeEnum::REST    => $application->getFullPriceWithoutDeposit(),
            ApplicationPaymentTypeEnum::FULL    => $application->getFullPrice(),
        };

        $stateConfig = $this->applicationPaymentStateConfigRepository->findOneByConfigurationOrCreateNew(
            self::STATES,
            self::PAID_STATES,
            self::CANCELLED_STATES,
            self::REFUNDED_STATES,
            self::PENDING_STATES,
            self::VALID_STATE_CHANGES,
        );

        $externalIdString = Uuid::v4()
            ->toRfc4122()
        ;

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