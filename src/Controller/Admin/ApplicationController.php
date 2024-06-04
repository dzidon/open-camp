<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\ApplicationData;
use App\Library\Data\Admin\ApplicationSearchData;
use App\Library\Http\Response\PdfResponse;
use App\Model\Entity\Application;
use App\Model\Entity\CampDate;
use App\Model\Entity\User;
use App\Model\Event\Admin\Application\ApplicationDeleteEvent;
use App\Model\Event\Admin\Application\ApplicationUpdateEvent;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Model\Repository\CampDateRepositoryInterface;
use App\Model\Service\Application\ApplicationInvoiceFilesystemInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Admin\ApplicationSearchType;
use App\Service\Form\Type\Admin\ApplicationType;
use App\Service\Form\Type\Common\HiddenTrueType;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\UuidV4;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
class ApplicationController extends AbstractController
{
    private ApplicationRepositoryInterface $applicationRepository;

    public function __construct(ApplicationRepositoryInterface $applicationRepository)
    {
        $this->applicationRepository = $applicationRepository;
    }

    #[Route('/admin/applications/{campDateId}', name: 'admin_application_list')]
    public function list(CampDateRepositoryInterface      $campDateRepository,
                         FormFactoryInterface             $formFactory,
                         MenuTypeFactoryRegistryInterface $menuFactory,
                         Request                          $request,
                         ?UuidV4                          $campDateId = null): Response
    {
        $searchByGuideOrCampDate = null;
        $breadcrumbsOptions = [];

        $isAdmin =
            $this->isGranted('application', 'any_admin_permission') ||
            $this->isGranted('application_payment', 'any_admin_permission')
        ;

        if ($campDateId === null)
        {
            $isGuide = $this->isGranted('guide_access_read');

            if (!$isGuide && !$isAdmin)
            {
                throw $this->createAccessDeniedException();
            }

            if ($isGuide && !$isAdmin)
            {
                /** @var User $searchByGuideOrCampDate */
                $searchByGuideOrCampDate = $this->getUser();
            }
        }
        else
        {
            $campDate = $campDateRepository->findOneById($campDateId);

            if ($campDate === null)
            {
                throw $this->createNotFoundException();
            }

            $isGuide = $this->isGranted('guide_access_read', $campDate);

            if (!$isAdmin && !$isGuide)
            {
                throw $this->createAccessDeniedException();
            }

            $searchByGuideOrCampDate = $campDate;
            $breadcrumbsOptions = [
                'camp_date' => $campDate,
                'camp'      => $campDate->getCamp(),
            ];
        }

        $page = (int) $request->query->get('page', 1);
        $searchData = new ApplicationSearchData();
        $form = $formFactory->createNamed('', ApplicationSearchType::class, $searchData);
        $form->handleRequest($request);
        $isSearchInvalid = $form->isSubmitted() && !$form->isValid();

        if ($isSearchInvalid)
        {
            $searchData = new ApplicationSearchData();
        }

        $paginator = $this->applicationRepository->getAdminPaginator($searchData, $searchByGuideOrCampDate, $page, 20);

        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', ['paginator' => $paginator]);
        $campDate = $searchByGuideOrCampDate instanceof CampDate ? $searchByGuideOrCampDate : null;

        return $this->render('admin/application/list.html.twig', [
            'form_search'       => $form->createView(),
            'paginator'         => $paginator,
            'pagination_menu'   => $paginationMenu,
            'is_search_invalid' => $isSearchInvalid,
            'camp_date'         => $campDate,
            'breadcrumbs'       => $this->createBreadcrumbs($breadcrumbsOptions),
        ]);
    }

    #[Route('/admin/application/{id}/invoice', name: 'admin_application_invoice')]
    public function invoice(ApplicationInvoiceFilesystemInterface $applicationInvoiceFilesystem, UuidV4 $id): PdfResponse
    {
        $application = $this->findCompletedApplicationOrThrow404($id);

        if (!$this->isGranted('application_read') && !$this->isGranted('guide_access_read', $application))
        {
            throw $this->createAccessDeniedException();
        }

        $applicationInvoiceFilesystem->createInvoiceFile($application);
        $invoiceContents = $applicationInvoiceFilesystem->getInvoiceContents($application);

        if ($invoiceContents === null)
        {
            throw $this->createNotFoundException();
        }

        return new PdfResponse('', $invoiceContents);
    }

    #[Route('/admin/application/{id}/read', name: 'admin_application_read')]
    public function read(ApplicationInvoiceFilesystemInterface $applicationInvoiceFilesystem, UuidV4 $id): Response
    {
        $application = $this->findCompletedApplicationOrThrow404($id);

        if (!$this->isGranted('application_read') && !$this->isGranted('guide_access_read', $application))
        {
            throw $this->createAccessDeniedException();
        }

        $campDate = $application->getCampDate();
        $camp = $campDate?->getCamp();
        $invoiceFound = $applicationInvoiceFilesystem->getInvoiceContents($application) !== null;

        return $this->render('admin/application/read.html.twig', [
            'application'   => $application,
            'invoice_found' => $invoiceFound,
            'breadcrumbs'   => $this->createBreadcrumbs([
                'application' => $application,
                'camp_date'   => $campDate,
                'camp'        => $camp,
            ]),
        ]);
    }

    #[Route('/admin/application/{id}/update', name: 'admin_application_update')]
    public function update(EventDispatcherInterface      $eventDispatcher,
                           DataTransferRegistryInterface $dataTransfer,
                           Request                       $request,
                           UuidV4                        $id): Response
    {
        $application = $this->findCompletedApplicationOrThrow404($id);
        $campDate = $application->getCampDate();
        $camp = $campDate?->getCamp();

        if (!$this->isGranted('application_update')                &&
            !$this->isGranted('application_state_update')          &&
            !$this->isGranted('guide_access_update', $application) &&
            !$this->isGranted('guide_access_state', $application))
        {
            throw $this->createAccessDeniedException();
        }

        $applicationData = new ApplicationData($application);
        $dataTransfer->fillData($applicationData, $application);
        $form = $this->createForm(ApplicationType::class, $applicationData);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.application.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new ApplicationUpdateEvent($applicationData, $application);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.application.update');
            $campDateId = $campDate?->getId();

            return $this->redirectToRoute('admin_application_list', [
                'campDateId' => $campDateId,
            ]);
        }

        return $this->render('admin/application/update.html.twig', [
            'application'      => $application,
            'form_application' => $form->createView(),
            'breadcrumbs'      => $this->createBreadcrumbs([
                'application' => $application,
                'camp_date'   => $campDate,
                'camp'        => $camp,
            ]),
        ]);
    }

    #[IsGranted('application_delete')]
    #[Route('/admin/application/{id}/delete', name: 'admin_application_delete')]
    public function delete(EventDispatcherInterface $eventDispatcher, Request $request, UuidV4 $id): Response
    {
        $application = $this->findCompletedApplicationOrThrow404($id);

        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'form.admin.application_delete.button',
            'attr'  => ['class' => 'btn-danger'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new ApplicationDeleteEvent($application);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.application.delete');
            $campDateId = $application->getCampDate()->getId();

            return $this->redirectToRoute('admin_application_list', [
                'campDateId' => $campDateId,
            ]);
        }

        $campDate = $application->getCampDate();
        $camp = $campDate?->getCamp();

        return $this->render('admin/application/delete.html.twig', [
            'application' => $application,
            'form_delete' => $form->createView(),
            'breadcrumbs' => $this->createBreadcrumbs([
                'application' => $application,
                'camp_date'   => $campDate,
                'camp'        => $camp,
            ]),
        ]);
    }

    private function findCompletedApplicationOrThrow404(UuidV4 $id): Application
    {
        $application = $this->applicationRepository->findOneById($id);

        if ($application === null || $application->isDraft())
        {
            throw $this->createNotFoundException();
        }

        return $application;
    }
}