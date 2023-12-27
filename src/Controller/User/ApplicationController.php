<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Library\Data\User\ApplicationStepOneData;
use App\Model\Entity\Application;
use App\Model\Entity\CampDate;
use App\Model\Entity\User;
use App\Model\Event\User\Application\ApplicationStepOneCreateEvent;
use App\Model\Event\User\Application\ApplicationStepOneUpdateEvent;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Model\Repository\CampDateRepositoryInterface;
use App\Model\Service\Application\ApplicationStepOneDataFactoryInterface;
use App\Model\Service\ApplicationCamper\ApplicationCamperDataFactoryInterface;
use App\Model\Service\Contact\ContactDataFactoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\User\ApplicationStepOneType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\UuidV4;

class ApplicationController extends AbstractController
{
    private CampDateRepositoryInterface $campDateRepository;
    private ApplicationRepositoryInterface $applicationRepository;

    public function __construct(CampDateRepositoryInterface $campDateRepository, ApplicationRepositoryInterface $applicationRepository)
    {
        $this->campDateRepository = $campDateRepository;
        $this->applicationRepository = $applicationRepository;
    }

    #[Route('/application-create/{campDateId}', name: 'user_application_step_one_create')]
    public function stepOneCreate(ApplicationStepOneDataFactoryInterface $applicationStepOneDataFactory,
                                  ApplicationCamperDataFactoryInterface  $applicationCamperDataFactory,
                                  ContactDataFactoryInterface            $contactDataFactory,
                                  EventDispatcherInterface               $eventDispatcher,
                                  Request                                $request,
                                  UuidV4                                 $campDateId): Response
    {
        $campDate = $this->findCampDateOrThrow404($campDateId);
        $contactData = $contactDataFactory->createContactData();
        $applicationCamperData = $applicationCamperDataFactory->createApplicationCamperDataFromCampDate($campDate);
        $applicationStepOneData = $applicationStepOneDataFactory->createApplicationStepOneData($campDate, $applicationCamperData, $contactData);

        $form = $this->createForm(ApplicationStepOneType::class, $applicationStepOneData, [
            'application_camper_default_data'  => $applicationCamperData,
            'contact_default_data'             => $contactData,
        ]);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.application_step_one.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            /** @var null|User $user */
            $user = $this->getUser();
            $event = new ApplicationStepOneCreateEvent($applicationStepOneData, $campDate, $user);
            $eventDispatcher->dispatch($event, $event::NAME);
            $application = $event->getApplication();

            return $this->redirectToRoute('user_application_step_one_update', [
                'applicationId' => $application->getId()
            ]);
        }

        return $this->render('user/application/step_one.html.twig', [
            'form_application_step_one' => $form->createView(),
        ]);
    }

    #[Route('/application/{applicationId}', name: 'user_application_step_one_update')]
    public function stepOneUpdate(ApplicationCamperDataFactoryInterface  $applicationCamperDataFactory,
                                  ContactDataFactoryInterface            $contactDataFactory,
                                  EventDispatcherInterface               $eventDispatcher,
                                  DataTransferRegistryInterface          $dataTransfer,
                                  Request                                $request,
                                  UuidV4                                 $applicationId): Response
    {
        $application = $this->findApplicationOrThrow404($applicationId);
        $contactData = $contactDataFactory->createContactDataFromApplication($application);
        $applicationCamperData = $applicationCamperDataFactory->createApplicationCamperDataFromApplication($application);
        $applicationStepOneData = new ApplicationStepOneData(
            $application->isEuBusinessDataEnabled(),
            $application->isNationalIdentifierEnabled(),
            $application->getCurrency()
        );
        $dataTransfer->fillData($applicationStepOneData, $application);

        $form = $this->createForm(ApplicationStepOneType::class, $applicationStepOneData, [
            'application_camper_default_data'  => $applicationCamperData,
            'contact_default_data'             => $contactData,
        ]);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.application_step_one.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new ApplicationStepOneUpdateEvent($applicationStepOneData, $application);
            $eventDispatcher->dispatch($event, $event::NAME);

            return $this->redirectToRoute('user_application_step_one_update', [
                'applicationId' => $application->getId()
            ]);
        }

        return $this->render('user/application/step_one.html.twig', [
            'form_application_step_one' => $form->createView(),
        ]);
    }

    private function findCampDateOrThrow404(UuidV4 $id): CampDate
    {
        $campDate = $this->campDateRepository->findOneById($id);

        if ($campDate === null)
        {
            throw $this->createNotFoundException();
        }

        return $campDate;
    }

    private function findApplicationOrThrow404(UuidV4 $id): Application
    {
        $application = $this->applicationRepository->findOneById($id);

        if ($application === null)
        {
            throw $this->createNotFoundException();
        }

        return $application;
    }
}