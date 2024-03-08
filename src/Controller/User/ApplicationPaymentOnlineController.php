<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Model\Enum\Entity\ApplicationPaymentTypeEnum;
use App\Model\Event\User\ApplicationPayment\ApplicationPaymentOnlineCreateEvent;
use App\Model\Event\User\ApplicationPayment\ApplicationPaymentOnlineUpdateEvent;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Model\Service\ApplicatonPayment\Online\ApplicationPaymentOnlineGateInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\UuidV4;

class ApplicationPaymentOnlineController extends AbstractController
{
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    #[Route('/application/{applicationId}/online-payment/{type}', name: 'user_application_online_pay')]
    public function payOnline(ApplicationRepositoryInterface $applicationRepository,
                              UuidV4                         $applicationId,
                              ApplicationPaymentTypeEnum     $type): Response
    {
        $application = $applicationRepository->findOneById($applicationId);

        if ($application === null || !$application->isAwaitingPayment($type) || !$application->isPaymentMethodOnline())
        {
            throw $this->createNotFoundException();
        }

        $pendingOnlineApplicationPayment = match ($type)
        {
            ApplicationPaymentTypeEnum::DEPOSIT => $application->getPendingDepositApplicationPayment(),
            ApplicationPaymentTypeEnum::REST    => $application->getPendingRestApplicationPayment(),
            ApplicationPaymentTypeEnum::FULL    => $application->getPendingFullApplicationPayment(),
        };

        if ($pendingOnlineApplicationPayment !== null)
        {
            return $this->redirect($pendingOnlineApplicationPayment->getExternalUrl());
        }

        $event = new ApplicationPaymentOnlineCreateEvent($type, $application);
        $this->eventDispatcher->dispatch($event, $event::NAME);
        $newOnlineApplicationPayment = $event->getApplicationPayment();

        if ($newOnlineApplicationPayment === null)
        {
            $this->addTransFlash('failure', 'application.payment.unable_to_create');

            return $this->redirectToRoute('user_application_completed', [
                'applicationId' => $applicationId,
            ]);
        }

        return $this->redirect($newOnlineApplicationPayment->getExternalUrl());
    }

    #[Route('/application-payment-online-update', name: 'user_application_payment_online_update')]
    public function update(ApplicationPaymentOnlineGateInterface $applicationPaymentOnlineGate, Request $request): Response
    {
        $applicationPayment = $applicationPaymentOnlineGate->getApplicationPaymentFromExternalRequest($request);

        if ($applicationPayment === null || !$applicationPayment->isOnline())
        {
            throw $this->createNotFoundException();
        }

        $newState = $applicationPaymentOnlineGate->getNewStateFromExternalRequest($request);

        if ($newState === null)
        {
            return new Response('Could not retrieve new state', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (!$applicationPayment->canChangeToState($newState))
        {
            return new Response('Unable to change to this state', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $event = new ApplicationPaymentOnlineUpdateEvent($applicationPayment, $newState);
        $this->eventDispatcher->dispatch($event, $event::NAME);

        return new Response('State updated', Response::HTTP_OK);
    }

    #[Route('/application-payment-online-status', name: 'user_application_payment_online_status')]
    public function status(ApplicationPaymentOnlineGateInterface $applicationPaymentOnlineGate, Request $request): Response
    {
        $applicationPayment = $applicationPaymentOnlineGate->getApplicationPaymentFromExternalRequest($request);

        if ($applicationPayment === null || !$applicationPayment->isOnline())
        {
            throw $this->createNotFoundException();
        }

        $newState = $applicationPaymentOnlineGate->getNewStateFromExternalRequest($request);

        if ($newState !== null && $applicationPayment->canChangeToState($newState))
        {
            $event = new ApplicationPaymentOnlineUpdateEvent($applicationPayment, $newState);
            $this->eventDispatcher->dispatch($event, $event::NAME);
        }

        return $this->render('user/payment/status.html.twig', [
            'application_payment' => $applicationPayment,
            'breadcrumbs'         => $this->createBreadcrumbs([
                'get_parameters' => $request->query->all(),
                'application'    => $applicationPayment->getApplication(),
            ]),
        ]);
    }
}