<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Library\Data\User\ApplicationStepTwoUpdateData;
use App\Library\Data\User\ApplicationStepOneData;
use App\Model\Entity\Application;
use App\Model\Entity\CampDate;
use App\Model\Entity\User;
use App\Model\Event\User\Application\ApplicationStepOneCreateEvent;
use App\Model\Event\User\Application\ApplicationStepOneUpdateEvent;
use App\Model\Event\User\Application\ApplicationStepTwoUpdateEvent;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Model\Repository\CampCategoryRepositoryInterface;
use App\Model\Repository\CampDateRepositoryInterface;
use App\Model\Service\Application\ApplicationStepOneDataFactoryInterface;
use App\Model\Service\ApplicationCamper\ApplicationCamperDataFactoryInterface;
use App\Model\Service\ApplicationPurchasableItemInstance\ApplicationPurchasableItemInstanceDataFactoryInterface;
use App\Model\Service\Contact\ContactDataFactoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\User\ApplicationStepTwoType;
use App\Service\Form\Type\User\ApplicationStepOneType;
use App\Service\Menu\Breadcrumbs\User\ApplicationBreadcrumbsInterface;
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
    private CampCategoryRepositoryInterface $campCategoryRepository;
    private ApplicationBreadcrumbsInterface $breadcrumbs;

    public function __construct(CampDateRepositoryInterface     $campDateRepository,
                                ApplicationRepositoryInterface  $applicationRepository,
                                CampCategoryRepositoryInterface $campCategoryRepository,
                                ApplicationBreadcrumbsInterface $breadcrumbs)
    {
        $this->campDateRepository = $campDateRepository;
        $this->applicationRepository = $applicationRepository;
        $this->campCategoryRepository = $campCategoryRepository;
        $this->breadcrumbs = $breadcrumbs;
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

            return $this->redirectToRoute('user_application_step_two', [
                'applicationId' => $application->getId(),
            ]);
        }

        // load all camp categories so that the camp category path does not trigger additional queries
        $this->campCategoryRepository->findAll();
        $camp = $campDate->getCamp();

        return $this->render('user/application/step_one.html.twig', [
            'camp_name'                       => $camp->getName(),
            'camp_date_start_at'              => $campDate->getStartAt(),
            'camp_date_end_at'                => $campDate->getEndAt(),
            'camp_date_deposit'               => $campDate->getDeposit(),
            'camp_date_deposit_until'         => $campDate->getDepositUntil(),
            'camp_date_price_without_deposit' => $campDate->getPriceWithoutDeposit(),
            'camp_date_full_price'            => $campDate->getFullPrice(),
            'camp_date_leader_names'          => $campDate->getLeaderNames(),
            'camp_date_description'           => $campDate->getDescription(),
            'tax'                             => $this->getParameter('app.tax'),
            'form_application_step_one'       => $form->createView(),
            'breadcrumbs'                     => $this->breadcrumbs->buildForStepOneCreate($campDate),
            'application_back_url'            => $this->generateUrl('user_camp_detail', [
                'urlName' => $camp->getUrlName(),
            ]),
        ]);
    }

    #[Route('/application/{applicationId}/step-one', name: 'user_application_step_one_update')]
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
            $application->getCurrency(),
            $application->getTax()
        );
        $dataTransfer->fillData($applicationStepOneData, $application);

        $form = $this->createForm(ApplicationStepOneType::class, $applicationStepOneData, [
            'application_camper_default_data'  => $applicationCamperData,
            'contact_default_data'             => $contactData,
        ]);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.application_step_one.button',]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new ApplicationStepOneUpdateEvent($applicationStepOneData, $application);
            $eventDispatcher->dispatch($event, $event::NAME);

            return $this->redirectToRoute('user_application_step_two', [
                'applicationId' => $application->getId()
            ]);
        }

        // load all camp categories so that the camp category path does not trigger additional queries
        $this->campCategoryRepository->findAll();
        $campDate = $application->getCampDate();
        $camp = $campDate?->getCamp();
        $backRoute = $camp === null ? 'user_camp_catalog' : 'user_camp_detail';
        $backUrlParameters = $camp === null ? [] : ['urlName' => $camp->getUrlName()];

        return $this->render('user/application/step_one.html.twig', [
            'camp_name'                       => $application->getCampName(),
            'camp_date_start_at'              => $application->getCampDateStartAt(),
            'camp_date_end_at'                => $application->getCampDateEndAt(),
            'camp_date_deposit'               => $application->getDeposit(),
            'camp_date_deposit_until'         => $application->getDepositUntil(),
            'camp_date_price_without_deposit' => $application->getPriceWithoutDeposit(),
            'camp_date_full_price'            => $application->getFullPrice(),
            'camp_date_leader_names'          => $campDate?->getLeaderNames(),
            'camp_date_description'           => $campDate?->getDescription(),
            'tax'                             => $application->getTax(),
            'form_application_step_one'       => $form->createView(),
            'breadcrumbs'                     => $this->breadcrumbs->buildForStepOneUpdate($application),
            'application_back_url'            => $this->generateUrl($backRoute, $backUrlParameters),
        ]);
    }

    #[Route('/application/{applicationId}/step-two', name: 'user_application_step_two')]
    public function stepTwo(ApplicationPurchasableItemInstanceDataFactoryInterface $applicationPurchasableItemInstanceDataFactory,
                            DataTransferRegistryInterface                          $dataTransfer,
                            EventDispatcherInterface                               $eventDispatcher,
                            Request                                                $request,
                            UuidV4                                                 $applicationId): Response
    {
        $application = $this->findApplicationOrThrow404($applicationId);
        $applicationPurchasableItemsData = new ApplicationStepTwoUpdateData();
        $dataTransfer->fillData($applicationPurchasableItemsData, $application);

        $form = $this->createForm(ApplicationStepTwoType::class, $applicationPurchasableItemsData, [
            'instance_defaults_data' => $applicationPurchasableItemInstanceDataFactory->createDataFromApplication($application),
        ]);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.application_step_two.button',]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new ApplicationStepTwoUpdateEvent($applicationPurchasableItemsData, $application);
            $eventDispatcher->dispatch($event, $event::NAME);

            return $this->redirectToRoute('user_application_step_two', [
                'applicationId' => $application->getId()
            ]);
        }

        return $this->render('user/application/step_two.html.twig', [
            'form_application_step_two'     => $form->createView(),
            'application_purchasable_items' => $application->getApplicationPurchasableItems(),
            'breadcrumbs'                   => $this->breadcrumbs->buildForStepTwo($application),
            'application_back_url'          => $this->generateUrl('user_application_step_one_update', [
                'applicationId' => $application->getId()->toRfc4122(),
            ]),
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