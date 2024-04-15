<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\ApplicationPaymentData;
use App\Library\Data\Admin\ApplicationPaymentSearchData;
use App\Model\Entity\Application;
use App\Model\Entity\ApplicationPayment;
use App\Model\Event\Admin\ApplicationPayment\ApplicationPaymentOfflineCreateEvent;
use App\Model\Event\Admin\ApplicationPayment\ApplicationPaymentOfflineDeleteEvent;
use App\Model\Event\Admin\ApplicationPayment\ApplicationPaymentOfflineUpdateEvent;
use App\Model\Event\Admin\ApplicationPayment\ApplicationPaymentOnlineRefundEvent;
use App\Model\Repository\ApplicationPaymentRepositoryInterface;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Model\Service\ApplicatonPayment\Offline\ApplicationPaymentOfflineGateInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Admin\ApplicationPaymentSearchType;
use App\Service\Form\Type\Admin\ApplicationPaymentType;
use App\Service\Form\Type\Common\HiddenTrueType;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\UuidV4;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
class ApplicationPaymentController extends AbstractController
{
    private ApplicationPaymentRepositoryInterface $applicationPaymentRepository;

    private ApplicationRepositoryInterface $applicationRepository;

    public function __construct(ApplicationPaymentRepositoryInterface $applicationPaymentRepository,
                                ApplicationRepositoryInterface        $applicationRepository)
    {
        $this->applicationPaymentRepository = $applicationPaymentRepository;
        $this->applicationRepository = $applicationRepository;
    }

    #[Route('/admin/application/{id}/payments', name: 'admin_application_payment_list')]
    public function list(MenuTypeFactoryRegistryInterface $menuFactory,
                         FormFactoryInterface             $formFactory,
                         Request                          $request,
                         UuidV4                           $id): Response
    {
        $application = $this->findApplicationOrThrow404($id);

        if (!$this->isGranted('application_payment', 'any_admin_permission') &&
            !$this->isGranted('guide_access_payments', $application))
        {
            throw $this->createAccessDeniedException();
        }

        $page = (int) $request->query->get('page', 1);
        $searchData = new ApplicationPaymentSearchData();
        $form = $formFactory->createNamed('', ApplicationPaymentSearchType::class, $searchData);
        $form->handleRequest($request);

        $isSearchInvalid = $form->isSubmitted() && !$form->isValid();
        if ($isSearchInvalid)
        {
            $searchData = new ApplicationPaymentSearchData();
        }

        $paginator = $this->applicationPaymentRepository->getAdminPaginator($searchData, $application, $page, 20);
        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', [
            'paginator' => $paginator,
        ]);

        $campDate = $application->getCampDate();
        $camp = $campDate?->getCamp();

        return $this->render('admin/application/payment/list.html.twig', [
            'application'       => $application,
            'form_search'       => $form->createView(),
            'pagination_menu'   => $paginationMenu,
            'paginator'         => $paginator,
            'is_search_invalid' => $isSearchInvalid,
            'breadcrumbs'       => $this->createBreadcrumbs([
                'application' => $application,
                'camp_date'   => $campDate,
                'camp'        => $camp,
            ]),
        ]);
    }

    #[Route('/admin/application/{id}/create-payment', name: 'admin_application_payment_create')]
    public function create(EventDispatcherInterface               $eventDispatcher,
                           ApplicationPaymentOfflineGateInterface $offlinePaymentGate,
                           Request                                $request,
                           UuidV4                                 $id): Response
    {
        $application = $this->findApplicationOrThrow404($id);

        if (!$this->isGranted('application_payment_create') && !$this->isGranted('guide_access_payments', $application))
        {
            throw $this->createAccessDeniedException();
        }

        $states = $offlinePaymentGate->getStates();
        $applicationPaymentData = new ApplicationPaymentData($states);
        $form = $this->createForm(ApplicationPaymentType::class, $applicationPaymentData, [
            'application' => $application,
        ]);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.application_payment.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new ApplicationPaymentOfflineCreateEvent($applicationPaymentData, $application);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.application_payment.create');

            return $this->redirectToRoute('admin_application_payment_list', [
                'id' => $application->getId(),
            ]);
        }

        $campDate = $application->getCampDate();
        $camp = $campDate?->getCamp();

        return $this->render('admin/application/payment/update.html.twig', [
            'application'              => $application,
            'form_application_payment' => $form->createView(),
            'breadcrumbs'              => $this->createBreadcrumbs([
                'application' => $application,
                'camp_date'   => $campDate,
                'camp'        => $camp,
            ]),
        ]);
    }

    #[Route('/admin/application-payment/{id}/read', name: 'admin_application_payment_read')]
    public function read(UuidV4 $id): Response
    {
        $applicationPayment = $this->findApplicationPaymentOrThrow404($id);
        $application = $applicationPayment->getApplication();

        if (!$this->isGranted('application_payment_read') && !$this->isGranted('guide_access_payments', $application))
        {
            throw $this->createAccessDeniedException();
        }

        $campDate = $application->getCampDate();
        $camp = $campDate?->getCamp();

        return $this->render('admin/application/payment/read.html.twig', [
            'application'         => $application,
            'application_payment' => $applicationPayment,
            'breadcrumbs'         => $this->createBreadcrumbs([
                'application_payment' => $applicationPayment,
                'application'         => $application,
                'camp_date'           => $campDate,
                'camp'                => $camp,
            ]),
        ]);
    }

    #[Route('/admin/application-payment/{id}/update', name: 'admin_application_payment_update')]
    public function update(EventDispatcherInterface      $eventDispatcher,
                           DataTransferRegistryInterface $dataTransfer,
                           Request                       $request,
                           UuidV4                        $id): Response
    {
        $applicationPayment = $this->findApplicationPaymentOrThrow404($id);
        $application = $applicationPayment->getApplication();

        if ($applicationPayment->isOnline())
        {
            throw $this->createNotFoundException();
        }

        if (!$this->isGranted('application_payment_update') && !$this->isGranted('guide_access_payments', $application))
        {
            throw $this->createAccessDeniedException();
        }

        $applicationPaymentData = new ApplicationPaymentData($applicationPayment->getCurrentValidStateChanges());
        $dataTransfer->fillData($applicationPaymentData, $applicationPayment);

        $form = $this->createForm(ApplicationPaymentType::class, $applicationPaymentData, [
            'application' => $application,
        ]);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.application_payment.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new ApplicationPaymentOfflineUpdateEvent($applicationPaymentData, $applicationPayment);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.application_payment.update');

            return $this->redirectToRoute('admin_application_payment_list', [
                'id' => $application->getId(),
            ]);
        }

        $campDate = $application->getCampDate();
        $camp = $campDate?->getCamp();

        return $this->render('admin/application/payment/update.html.twig', [
            'application'              => $application,
            'application_payment'      => $applicationPayment,
            'form_application_payment' => $form->createView(),
            'breadcrumbs'              => $this->createBreadcrumbs([
                'application_payment' => $applicationPayment,
                'application'         => $application,
                'camp_date'           => $campDate,
                'camp'                => $camp,
            ]),
        ]);
    }

    #[Route('/admin/application-payment/{id}/delete', name: 'admin_application_payment_delete')]
    public function delete(EventDispatcherInterface $eventDispatcher, Request $request, UuidV4 $id): Response
    {
        $applicationPayment = $this->findApplicationPaymentOrThrow404($id);
        $application = $applicationPayment->getApplication();

        if ($applicationPayment->isOnline())
        {
            throw $this->createNotFoundException();
        }

        if (!$this->isGranted('application_payment_delete') && !$this->isGranted('guide_access_payments', $application))
        {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'form.admin.application_payment_delete.button',
            'attr'  => ['class' => 'btn-danger'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new ApplicationPaymentOfflineDeleteEvent($applicationPayment);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.application_payment.delete');

            return $this->redirectToRoute('admin_application_payment_list', [
                'id' => $application->getId(),
            ]);
        }

        $campDate = $application->getCampDate();
        $camp = $campDate?->getCamp();

        return $this->render('admin/application/payment/delete.html.twig', [
            'application'         => $application,
            'application_payment' => $applicationPayment,
            'form_delete'         => $form->createView(),
            'breadcrumbs'         => $this->createBreadcrumbs([
                'application_payment' => $applicationPayment,
                'application'         => $application,
                'camp_date'           => $campDate,
                'camp'                => $camp,
            ]),
        ]);
    }

    #[Route('/admin/application-payment/{id}/refund', name: 'admin_application_payment_refund')]
    public function refund(EventDispatcherInterface $eventDispatcher, Request $request, UuidV4 $id): Response
    {
        $applicationPayment = $this->findApplicationPaymentOrThrow404($id);
        $application = $applicationPayment->getApplication();

        if (!$applicationPayment->isOnline() || !$applicationPayment->isPaid())
        {
            throw $this->createNotFoundException();
        }

        if (!$this->isGranted('application_payment_refund') && !$this->isGranted('guide_access_payments', $application))
        {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'form.admin.application_payment_refund.button',
            'attr'  => ['class' => 'btn-warning'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new ApplicationPaymentOnlineRefundEvent($applicationPayment);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.application_payment.refund');

            return $this->redirectToRoute('admin_application_payment_list', [
                'id' => $application->getId(),
            ]);
        }

        $campDate = $application->getCampDate();
        $camp = $campDate?->getCamp();

        return $this->render('admin/application/payment/refund.html.twig', [
            'application'         => $application,
            'application_payment' => $applicationPayment,
            'form_refund'         => $form->createView(),
            'breadcrumbs'         => $this->createBreadcrumbs([
                'application_payment' => $applicationPayment,
                'application'         => $application,
                'camp_date'           => $campDate,
                'camp'                => $camp,
            ]),
        ]);
    }

    private function findApplicationPaymentOrThrow404(UuidV4 $id): ApplicationPayment
    {
        $applicationPayment = $this->applicationPaymentRepository->findOneById($id);

        if ($applicationPayment === null)
        {
            throw $this->createNotFoundException();
        }

        return $applicationPayment;
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