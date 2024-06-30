<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Library\Data\User\ApplicationStepOneData;
use App\Library\Data\User\ApplicationStepThreeData;
use App\Library\Data\User\ApplicationStepTwoData;
use App\Model\Entity\Application;
use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use App\Model\Entity\User;
use App\Model\Event\User\Application\ApplicationAttachmentsUploadLaterEvent;
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
use App\Service\Data\Factory\Application\ApplicationStepOneDataFactoryInterface;
use App\Service\Data\Factory\ApplicationAttachment\ApplicationAttachmentsUploadLaterDataFactoryInterface;
use App\Service\Data\Factory\ApplicationCamper\ApplicationCamperDataFactoryInterface;
use App\Service\Data\Factory\ApplicationPurchasableItemInstance\ApplicationPurchasableItemInstanceDataFactoryInterface;
use App\Service\Data\Factory\Contact\ContactDataFactoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\User\ApplicationAttachmentsUploadLaterType;
use App\Service\Form\Type\User\ApplicationStepOneType;
use App\Service\Form\Type\User\ApplicationStepThreeType;
use App\Service\Form\Type\User\ApplicationStepTwoType;
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

    public function __construct(CampDateRepositoryInterface     $campDateRepository,
                                ApplicationRepositoryInterface  $applicationRepository,
                                CampCategoryRepositoryInterface $campCategoryRepository,
                                RouteNamerInterface             $routeNamer,
                                EventDispatcherInterface        $eventDispatcher)
    {
        $this->campDateRepository = $campDateRepository;
        $this->applicationRepository = $applicationRepository;
        $this->campCategoryRepository = $campCategoryRepository;
        $this->routeNamer = $routeNamer;
        $this->eventDispatcher = $eventDispatcher;
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

        $isEmailMandatory = $this->getParameter('app.contact_email_mandatory');
        $isPhoneNumberMandatory = $this->getParameter('app.contact_phone_number_mandatory');
        $contactDataCallable = $contactDataFactory->getCreateContactDataCallable($isEmailMandatory, $isPhoneNumberMandatory);
        $applicationCamperDataCallable = $applicationCamperDataFactory->getCallableFromCampDateForUserModule($campDate);
        $applicationStepOneData = $applicationStepOneDataFactory->createApplicationStepOneData(
            $campDate,
            $user,
            $applicationCamperDataCallable(),
            $contactDataCallable()
        );

        $form = $this->createForm(ApplicationStepOneType::class, $applicationStepOneData, [
            'application_camper_empty_data' => $applicationCamperDataCallable,
            'contact_empty_data'            => $contactDataCallable,
            'loadable_contacts'             => $user === null ? [] : $contactRepository->findByUser($user),
            'loadable_campers'              => $user === null ? [] : $camperRepository->findByUser($user),
        ]);
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
        $campCategory = $camp->getCampCategory();
        $this->setRouteNameToApplicationModule();

        return $this->render('user/application/step_one.html.twig', [
            'camp_date'                       => $campDate,
            'camp_name'                       => $camp->getName(),
            'camp_date_start_at'              => $campDate->getStartAt(),
            'camp_date_end_at'                => $campDate->getEndAt(),
            'camp_date_deposit'               => $campDate->getDeposit(),
            'camp_date_price_without_deposit' => $campDate->getPriceWithoutDeposit(),
            'camp_date_full_price'            => $campDate->getFullPrice(),
            'camp_date_description'           => $campDate->getDescription(),
            'tax'                             => $this->getParameter('app.tax'),
            'form_application_step_one'       => $form->createView(),
            'breadcrumbs'                     => $this->createBreadcrumbs([
                'camp'          => $camp,
                'camp_date'     => $campDate,
                'camp_category' => $campCategory,
            ]),
            'application_back_url' => $this->generateUrl('user_camp_detail', [
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
        $this->assertApplicationDraftAvailability($application);

        $campDate = $application->getCampDate();
        $isEmailMandatory = $application->isEmailMandatory();
        $isPhoneNumberMandatory = $application->isPhoneNumberMandatory();
        $contactDataCallable = $contactDataFactory->getCreateContactDataCallable($isEmailMandatory, $isPhoneNumberMandatory);
        $applicationCamperDataCallable = $applicationCamperDataFactory->getCallableFromApplicationForUserModule($application);
        $applicationStepOneData = new ApplicationStepOneData(
            $application->isEuBusinessDataEnabled(),
            $application->isNationalIdentifierEnabled(),
            $application->getCurrency(),
            $application->getTax(),
            $application->getCampDate(),
        );
        $dataTransfer->fillData($applicationStepOneData, $application);

        $form = $this->createForm(ApplicationStepOneType::class, $applicationStepOneData, [
            'application_camper_empty_data' => $applicationCamperDataCallable,
            'contact_empty_data'            => $contactDataCallable,
            'loadable_contacts'             => $user === null ? [] : $contactRepository->findByUser($user),
            'loadable_campers'              => $user === null ? [] : $camperRepository->findByUser($user),
        ]);
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
        $campCategory = $camp?->getCampCategory();

        $backRoute = $camp === null ? 'user_camp_catalog' : 'user_camp_detail';
        $backUrlParameters = $camp === null ? [] : ['urlName' => $camp->getUrlName()];
        $this->setRouteNameToApplicationModule();

        return $this->render('user/application/step_one.html.twig', [
            'application'                     => $application,
            'camp_name'                       => $application->getCampName(),
            'camp_date_start_at'              => $application->getCampDateStartAt(),
            'camp_date_end_at'                => $application->getCampDateEndAt(),
            'camp_date_deposit'               => $application->getDeposit(),
            'camp_date_price_without_deposit' => $application->getPriceWithoutDeposit(),
            'camp_date_full_price'            => $application->getPricePerCamper(),
            'camp_date_description'           => $campDate?->getDescription(),
            'tax'                             => $application->getTax(),
            'currency'                        => $application->getCurrency(),
            'form_application_step_one'       => $form->createView(),
            'application_back_url'            => $this->generateUrl($backRoute, $backUrlParameters),
            'breadcrumbs'                     => $this->createBreadcrumbs([
                'application'   => $application,
                'camp'          => $camp,
                'camp_category' => $campCategory,
            ]),
        ]);
    }

    #[Route('/application/{applicationId}/step-two', name: 'user_application_step_two')]
    public function stepTwo(ApplicationPurchasableItemInstanceDataFactoryInterface $dataFactory,
                            PaymentMethodRepositoryInterface                       $paymentMethodRepository,
                            DataTransferRegistryInterface                          $dataTransfer,
                            Request                                                $request,
                            UuidV4                                                 $applicationId): Response
    {
        $application = $this->findApplicationOrThrow404($applicationId);
        $this->assertApplicationDraftAvailability($application);

        $isBuyerBusiness = $application->isBuyerBusiness();
        $campDate = $application->getCampDate();
        $camp = $campDate?->getCamp();
        $campCategory = $camp?->getCampCategory();
        $applicationId = $application->getId();
        $itemInstancesEmptyData = $dataFactory->getDataCallableArrayForUserModule($application);
        $paymentMethodChoices = $paymentMethodRepository->findAll(true, $isBuyerBusiness);

        $applicationPurchasableItemsData = new ApplicationStepTwoData(
            $application->getCurrency(),
            $application->getDiscountSiblingsConfig(),
            count($application->getApplicationCampers()),
        );
        $dataTransfer->fillData($applicationPurchasableItemsData, $application);

        $form = $this->createForm(ApplicationStepTwoType::class, $applicationPurchasableItemsData, [
            'item_instances_empty_data' => $itemInstancesEmptyData,
            'choices_payment_methods'   => $paymentMethodChoices,
        ]);
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
        $this->setRouteNameToApplicationModule();

        return $this->render('user/application/step_two.html.twig', [
            'form_application_step_two' => $form->createView(),
            'application'               => $application,
            'breadcrumbs'               => $this->createBreadcrumbs([
                'application'   => $application,
                'camp'          => $camp,
                'camp_category' => $campCategory,
            ]),
            'application_back_url' => $this->generateUrl('user_application_step_one_update', [
                'applicationId' => $applicationId,
            ]),
        ]);
    }

    #[Route('/application/{applicationId}/step-three', name: 'user_application_step_three')]
    public function stepThree(CampDateRepositoryInterface $campDateRepository,
                              UuidV4                      $applicationId,
                              Request                     $request): Response
    {
        $application = $this->findApplicationOrThrow404($applicationId);
        $this->assertApplicationDraftAvailability($application);

        $campDate = $application->getCampDate();
        $camp = $campDate?->getCamp();
        $campCategory = $camp?->getCampCategory();
        $applicationId = $application->getId();

        $applicationStepThreeUpdateData = new ApplicationStepThreeData($this->getUser());
        $form = $this->createForm(ApplicationStepThreeType::class, $applicationStepThreeUpdateData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $numberOfNewApplicationCampers = count($application->getApplicationCampers());
            $capacityExceededBy = $campDateRepository->willNumberOfNewCampersExceedCampDateCapacity($campDate, $numberOfNewApplicationCampers);

            if (!$application->canBeCompleted())
            {
                $this->addTransFlash('failure', 'application.cannot_be_completed');
            }
            else if ($capacityExceededBy > 0)
            {
                $this->addTransFlash('failure', 'application_campers_count', [
                    'capacity_exceeded_by' => $capacityExceededBy,
                ], 'validators');
            }
            else
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
        }

        // load all camp categories so that the camp category path does not trigger additional queries
        $this->campCategoryRepository->findAll();
        $this->setRouteNameToApplicationModule();

        $applicationBackUrl = $this->generateUrl('user_application_step_two', [
            'applicationId' => $applicationId,
        ]);

        return $this->render('user/application/step_three.html.twig', [
            'application'                 => $application,
            'form_application_step_three' => $form->createView(),
            'application_back_url'        => $applicationBackUrl,
            'breadcrumbs'                 => $this->createBreadcrumbs([
                'application'   => $application,
                'camp'          => $camp,
                'camp_category' => $campCategory,
            ]),
        ]);
    }

    #[Route('/application/{applicationId}/completed', name: 'user_application_completed')]
    public function viewCompleted(ApplicationAttachmentsUploadLaterDataFactoryInterface $dataFactory,
                                  UuidV4                                                $applicationId,
                                  Request                                               $request): Response
    {
        $application = $this->findApplicationOrThrow404($applicationId);
        $this->assertApplicationCompletedAvailability($application);
        $form = null;

        if ($application->isAwaitingUploadOfAttachmentsRequiredLater())
        {
            $data = $dataFactory->createApplicationAttachmentsUploadLaterData($application);
            $form = $this->createForm(ApplicationAttachmentsUploadLaterType::class, $data);
            $form->add('submit', SubmitType::class, [
                'label'    => 'form.user.application_attachments_upload_later.button',
                'row_attr' => ['class' => 'mb-0'],
            ]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                $event = new ApplicationAttachmentsUploadLaterEvent($data, $application);
                $this->eventDispatcher->dispatch($event, $event::NAME);
                $this->addTransFlash('success', 'crud.action_performed.application_attachment.upload_later');

                return $this->redirectToRoute('user_application_completed', [
                    'applicationId' => $application->getId(),
                ]);
            }
        }

        return $this->render('user/application/completed.html.twig', [
            'application'                   => $application,
            'form_attachments_upload_later' => $form?->createView(),
            'breadcrumbs'                   => $this->createBreadcrumbs([
                'application'   => $application,
                'camp_category' => null,
            ]),
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
            if ($this->userCanViewHiddenCamps())
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
            if ($this->userCanViewHiddenCamps())
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

    private function userCanViewHiddenCamps(): bool
    {
        return $this->isGranted('camp_read') || $this->isGranted('camp_create') || $this->isGranted('camp_update');
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

    private function setRouteNameToApplicationModule(): void
    {
        $routeName = $this->trans('entity.application.singular');
        $this->routeNamer->setCurrentRouteName($routeName);
    }
}