<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Library\Data\User\ApplicationStepThreeUpdateData;
use App\Library\Data\User\ApplicationStepTwoUpdateData;
use App\Library\Data\User\ApplicationStepOneData;
use App\Model\Entity\Application;
use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use App\Model\Entity\User;
use App\Model\Event\User\Application\ApplicationDraftRemoveFromHttpStorageEvent;
use App\Model\Event\User\Application\ApplicationDraftStoreInHttpStorageEvent;
use App\Model\Event\User\Application\ApplicationStepOneCreateEvent;
use App\Model\Event\User\Application\ApplicationStepOneUpdateEvent;
use App\Model\Event\User\Application\ApplicationStepThreeUpdateEvent;
use App\Model\Event\User\Application\ApplicationStepTwoUpdateEvent;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Model\Repository\CampCategoryRepositoryInterface;
use App\Model\Repository\CampDateRepositoryInterface;
use App\Model\Repository\CamperRepositoryInterface;
use App\Model\Repository\ContactRepositoryInterface;
use App\Model\Repository\PaymentMethodRepositoryInterface;
use App\Model\Service\Application\ApplicationStepOneDataFactoryInterface;
use App\Model\Service\ApplicationCamper\ApplicationCamperDataFactoryInterface;
use App\Model\Service\ApplicationPurchasableItemInstance\ApplicationPurchasableItemInstanceDataFactoryInterface;
use App\Model\Service\Contact\ContactDataFactoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\User\ApplicationStepThreeType;
use App\Service\Form\Type\User\ApplicationStepTwoType;
use App\Service\Form\Type\User\ApplicationStepOneType;
use App\Service\Menu\Breadcrumbs\User\ApplicationBreadcrumbsInterface;
use App\Service\Routing\RouteNamerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\UuidV4;

class ApplicationController extends AbstractController
{
    private CampDateRepositoryInterface $campDateRepository;
    private ApplicationRepositoryInterface $applicationRepository;
    private CampCategoryRepositoryInterface $campCategoryRepository;
    private RouteNamerInterface $routeNamer;
    private EventDispatcherInterface $eventDispatcher;
    private ApplicationBreadcrumbsInterface $breadcrumbs;

    public function __construct(CampDateRepositoryInterface     $campDateRepository,
                                ApplicationRepositoryInterface  $applicationRepository,
                                CampCategoryRepositoryInterface $campCategoryRepository,
                                RouteNamerInterface             $routeNamer,
                                EventDispatcherInterface        $eventDispatcher,
                                ApplicationBreadcrumbsInterface $breadcrumbs)
    {
        $this->campDateRepository = $campDateRepository;
        $this->applicationRepository = $applicationRepository;
        $this->campCategoryRepository = $campCategoryRepository;
        $this->routeNamer = $routeNamer;
        $this->eventDispatcher = $eventDispatcher;
        $this->breadcrumbs = $breadcrumbs;
    }

    #[Route('/application-create/{campDateId}', name: 'user_application_step_one_create')]
    public function stepOneCreate(ContactRepositoryInterface             $contactRepository,
                                  CamperRepositoryInterface              $camperRepository,
                                  ApplicationStepOneDataFactoryInterface $applicationStepOneDataFactory,
                                  ApplicationCamperDataFactoryInterface  $applicationCamperDataFactory,
                                  ContactDataFactoryInterface            $contactDataFactory,
                                  Request                                $request,
                                  UuidV4                                 $campDateId): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();
        $campDate = $this->findCampDateOrThrow404($campDateId);
        $this->assertCampDateAvailability($campDate);

        $contactData = $contactDataFactory->createContactData();
        $applicationCamperData = $applicationCamperDataFactory->createApplicationCamperDataFromCampDate($campDate);
        $applicationStepOneData = $applicationStepOneDataFactory->createApplicationStepOneData($campDate, $applicationCamperData, $contactData);

        $form = $this->createForm(ApplicationStepOneType::class, $applicationStepOneData, [
            'application_camper_default_data'  => $applicationCamperData,
            'contact_default_data'             => $contactData,
            'loadable_contacts'                => $user === null ? [] : $contactRepository->findByUser($user),
            'loadable_campers'                 => $user === null ? [] : $camperRepository->findByUser($user),
        ]);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.application_step_one.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            /** @var null|User $user */
            $user = $this->getUser();
            $event = new ApplicationStepOneCreateEvent($applicationStepOneData, $campDate, $user);
            $this->eventDispatcher->dispatch($event, $event::NAME);
            $application = $event->getApplication();

            $response = $this->redirectToRoute('user_application_step_two', [
                'applicationId' => $application->getId(),
            ]);

            $event = new ApplicationDraftStoreInHttpStorageEvent($application, $response);
            $this->eventDispatcher->dispatch($event, $event::NAME);

            return $response;
        }

        // load all camp categories so that the camp category path does not trigger additional queries
        $this->campCategoryRepository->findAll();
        $camp = $campDate->getCamp();
        $this->setRouteName();

        return $this->render('user/application/step_one.html.twig', [
            'camp_date'                       => $campDate,
            'camp_name'                       => $camp->getName(),
            'camp_date_start_at'              => $campDate->getStartAt(),
            'camp_date_end_at'                => $campDate->getEndAt(),
            'camp_date_deposit'               => $campDate->getDeposit(),
            'camp_date_deposit_until'         => $campDate->getDepositUntil(),
            'camp_date_price_without_deposit' => $campDate->getPriceWithoutDeposit(),
            'camp_date_full_price'            => $campDate->getFullPrice(),
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
    public function stepOneUpdate(ContactRepositoryInterface            $contactRepository,
                                  CamperRepositoryInterface             $camperRepository,
                                  ApplicationCamperDataFactoryInterface $applicationCamperDataFactory,
                                  ContactDataFactoryInterface           $contactDataFactory,
                                  DataTransferRegistryInterface         $dataTransfer,
                                  Request                               $request,
                                  UuidV4                                $applicationId): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();
        $application = $this->findApplicationOrThrow404($applicationId);
        $campDate = $application->getCampDate();
        $this->assertApplicationDraftAvailability($application);

        $contactData = $contactDataFactory->createContactDataFromApplication($application);
        $applicationCamperData = $applicationCamperDataFactory->createApplicationCamperDataFromApplication($application);
        $applicationStepOneData = new ApplicationStepOneData(
            $application->isEuBusinessDataEnabled(),
            $application->isNationalIdentifierEnabled(),
            $application->getCurrency(),
            $application->getTax(),
            $application->getCampDate()
        );
        $dataTransfer->fillData($applicationStepOneData, $application);

        $form = $this->createForm(ApplicationStepOneType::class, $applicationStepOneData, [
            'application_camper_default_data' => $applicationCamperData,
            'contact_default_data'            => $contactData,
            'loadable_contacts'               => $user === null ? [] : $contactRepository->findByUser($user),
            'loadable_campers'                => $user === null ? [] : $camperRepository->findByUser($user),
        ]);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.application_step_one.button',]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new ApplicationStepOneUpdateEvent($applicationStepOneData, $application);
            $this->eventDispatcher->dispatch($event, $event::NAME);

            return $this->redirectToRoute('user_application_step_two', [
                'applicationId' => $application->getId(),
            ]);
        }

        // load all camp categories so that the camp category path does not trigger additional queries
        $this->campCategoryRepository->findAll();
        $camp = $campDate?->getCamp();
        $backRoute = $camp === null ? 'user_camp_catalog' : 'user_camp_detail';
        $backUrlParameters = $camp === null ? [] : ['urlName' => $camp->getUrlName()];
        $this->setRouteName();

        return $this->render('user/application/step_one.html.twig', [
            'application'                     => $application,
            'camp_name'                       => $application->getCampName(),
            'camp_date_start_at'              => $application->getCampDateStartAt(),
            'camp_date_end_at'                => $application->getCampDateEndAt(),
            'camp_date_deposit'               => $application->getDeposit(),
            'camp_date_deposit_until'         => $application->getDepositUntil(),
            'camp_date_price_without_deposit' => $application->getPriceWithoutDeposit(),
            'camp_date_full_price'            => $application->getPricePerCamper(),
            'camp_date_description'           => $campDate?->getDescription(),
            'tax'                             => $application->getTax(),
            'currency'                        => $application->getCurrency(),
            'form_application_step_one'       => $form->createView(),
            'breadcrumbs'                     => $this->breadcrumbs->buildForStepOneUpdate($application),
            'application_back_url'            => $this->generateUrl($backRoute, $backUrlParameters),
        ]);
    }

    #[Route('/application/{applicationId}/step-two', name: 'user_application_step_two')]
    public function stepTwo(ApplicationPurchasableItemInstanceDataFactoryInterface $applicationPurchasableItemInstanceDataFactory,
                            PaymentMethodRepositoryInterface                       $paymentMethodRepository,
                            DataTransferRegistryInterface                          $dataTransfer,
                            Request                                                $request,
                            UuidV4                                                 $applicationId): Response
    {
        $application = $this->findApplicationOrThrow404($applicationId);
        $this->assertApplicationDraftAvailability($application);
        $applicationId = $application->getId();

        $numberOfApplicationCampers = count($application->getApplicationCampers());
        $applicationPurchasableItemsData = new ApplicationStepTwoUpdateData(
            $application->getDiscountSiblingsConfig(),
            $application->getCurrency(),
            $numberOfApplicationCampers
        );
        $dataTransfer->fillData($applicationPurchasableItemsData, $application);

        $form = $this->createForm(ApplicationStepTwoType::class, $applicationPurchasableItemsData, [
            'instance_defaults_data'  => $applicationPurchasableItemInstanceDataFactory->createDataFromApplication($application),
            'choices_payment_methods' => $paymentMethodRepository->findAll(),
        ]);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.application_step_two.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new ApplicationStepTwoUpdateEvent($applicationPurchasableItemsData, $application);
            $this->eventDispatcher->dispatch($event, $event::NAME);

            return $this->redirectToRoute('user_application_step_three', [
                'applicationId' => $applicationId,
            ]);
        }

        // load all camp categories so that the camp category path does not trigger additional queries
        $this->campCategoryRepository->findAll();
        $this->setRouteName();

        return $this->render('user/application/step_two.html.twig', [
            'form_application_step_two' => $form->createView(),
            'application'               => $application,
            'breadcrumbs'               => $this->breadcrumbs->buildForStepTwo($application),
            'application_back_url'      => $this->generateUrl('user_application_step_one_update', [
                'applicationId' => $applicationId->toRfc4122(),
            ]),
        ]);
    }

    #[Route('/application/{applicationId}/step-three', name: 'user_application_step_three')]
    public function stepThree(UuidV4 $applicationId, Request $request): Response
    {
        $application = $this->findApplicationOrThrow404($applicationId);
        $this->assertApplicationDraftAvailability($application);
        $applicationId = $application->getId();

        $applicationStepThreeUpdateData = new ApplicationStepThreeUpdateData($this->getUser());
        $form = $this->createForm(ApplicationStepThreeType::class, $applicationStepThreeUpdateData);
        $form->add('submit', SubmitType::class, ['label' => 'form.user.application_step_three.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if ($application->canBeCompleted())
            {
                $response = $this->redirectToRoute('user_application_completed', [
                    'applicationId' => $applicationId,
                ]);

                $event = new ApplicationStepThreeUpdateEvent($applicationStepThreeUpdateData, $application);
                $this->eventDispatcher->dispatch($event, $event::NAME);

                $event = new ApplicationDraftRemoveFromHttpStorageEvent($application, $response);
                $this->eventDispatcher->dispatch($event, $event::NAME);

                $this->addTransFlash('success', 'application.completed');

                return $response;
            }
            else
            {
                $this->addTransFlash('failure', 'application.cannot_be_completed');
            }
        }

        // load all camp categories so that the camp category path does not trigger additional queries
        $this->campCategoryRepository->findAll();
        $this->setRouteName();

        $applicationBackUrl = $this->generateUrl('user_application_step_two', [
            'applicationId' => $applicationId->toRfc4122(),
        ]);

        return $this->render('user/application/step_three.html.twig', [
            'application'                 => $application,
            'form_application_step_three' => $form->createView(),
            'application_back_url'        => $applicationBackUrl,
            'breadcrumbs'                 => $this->breadcrumbs->buildForStepThree($application),
        ]);
    }

    #[Route('/application/{applicationId}/completed', name: 'user_application_completed')]
    public function viewCompleted(UuidV4 $applicationId): Response
    {
        $application = $this->findApplicationOrThrow404($applicationId);
        $this->assertApplicationCompletedAvailability($application);

        return $this->render('user/application/completed.html.twig', [
            'application' => $application,
            'breadcrumbs' => $this->breadcrumbs->buildForCompleted($application),
        ]);
    }

    private function assertCampDateAvailability(CampDate $campDate): void
    {
        $camp = $campDate->getCamp();
        $redirectException = $this->createCampDetailRedirectException($camp);
        $isOpen = $this->campDateRepository->isCampDateOpenForApplications($campDate);

        if (!$isOpen)
        {
            $this->addTransFlash('failure', 'camp_catalog.camp_date_no_longer_available');
            throw $redirectException;
        }

        if ($camp->isHidden() || $campDate->isHidden())
        {
            if ($this->isGranted('camp_create') || $this->isGranted('camp_update'))
            {
                $this->addTransFlash('warning', 'camp_catalog.hidden_camp_date_shown_for_admin');
            }
            else
            {
                $this->addTransFlash('failure', 'camp_catalog.camp_date_no_longer_available');
                throw $redirectException;
            }
        }
    }

    private function assertApplicationDraftAvailability(Application $application): void
    {
        $campDate = $application->getCampDate();

        if ($campDate === null)
        {
            $this->addTransFlash('failure', 'application.no_longer_editable');
            throw $this->createRedirectToRouteException('user_camp_catalog');
        }

        $camp = $campDate->getCamp();
        $redirectException = $this->createCampDetailRedirectException($camp);
        $editableDraftsResult = $this->applicationRepository->getApplicationsEditableDraftsResult([$application]);
        $isOpen = $editableDraftsResult->isApplicationEditableDraft($application);

        if (!$isOpen)
        {
            $this->addTransFlash('failure', 'application.no_longer_editable');
            throw $redirectException;
        }

        if ($camp->isHidden() || $campDate->isHidden())
        {
            if ($this->isGranted('camp_create') || $this->isGranted('camp_update'))
            {
                $this->addTransFlash('warning', 'camp_catalog.hidden_camp_date_shown_for_admin');
            }
            else
            {
                $this->addTransFlash('failure', 'application.no_longer_editable');
                throw $redirectException;
            }
        }
    }

    private function assertApplicationCompletedAvailability(Application $application): void
    {
        if ($application->isDraft())
        {
            throw $this->createNotFoundException();
        }
    }

    private function createCampDetailRedirectException(Camp $camp): HttpException
    {
        return $this->createRedirectToRouteException('user_camp_detail', [
            'urlName' => $camp->getUrlName(),
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

    private function setRouteName(): void
    {
        $routeName = $this->trans('entity.application.singular');
        $this->routeNamer->setCurrentRouteName($routeName);
    }
}