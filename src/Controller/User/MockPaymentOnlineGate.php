<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Model\Repository\ApplicationPaymentRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MockPaymentOnlineGate extends AbstractController
{
    private ApplicationPaymentRepositoryInterface $applicationPaymentRepository;

    public function __construct(ApplicationPaymentRepositoryInterface $applicationPaymentRepository)
    {
        $this->applicationPaymentRepository = $applicationPaymentRepository;
    }

    #[Route('/payment-simulate/{externalId}', name: 'user_application_payment_simulate')]
    public function paymentSimulation(string $externalId): Response
    {
        $applicationPayment = $this->applicationPaymentRepository->findOneByExternalId($externalId);

        if ($applicationPayment === null || !$applicationPayment->isOnline() || !$applicationPayment->isPending())
        {
            throw $this->createNotFoundException();
        }

        return $this->render('user/payment/online_gate_mock/index.html.twig', [
            'application_payment' => $applicationPayment,
        ]);
    }
}