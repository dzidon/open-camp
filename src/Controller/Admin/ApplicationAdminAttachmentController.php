<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\ApplicationAdminAttachmentCreateData;
use App\Library\Data\Admin\ApplicationAdminAttachmentSearchData;
use App\Library\Data\Admin\ApplicationAdminAttachmentUpdateData;
use App\Library\Http\Response\FileDownloadResponse;
use App\Model\Entity\Application;
use App\Model\Entity\ApplicationAdminAttachment;
use App\Model\Event\Admin\ApplicationAdminAttachment\ApplicationAdminAttachmentCreateEvent;
use App\Model\Event\Admin\ApplicationAdminAttachment\ApplicationAdminAttachmentDeleteEvent;
use App\Model\Event\Admin\ApplicationAdminAttachment\ApplicationAdminAttachmentUpdateEvent;
use App\Model\Repository\ApplicationAdminAttachmentRepositoryInterface;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Model\Service\ApplicationAdminAttachment\ApplicationAdminAttachmentFilesystemInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Admin\ApplicationAdminAttachmentCreateType;
use App\Service\Form\Type\Admin\ApplicationAdminAttachmentSearchType;
use App\Service\Form\Type\Admin\ApplicationAdminAttachmentUpdateType;
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
class ApplicationAdminAttachmentController extends AbstractController
{
    private ApplicationAdminAttachmentRepositoryInterface $applicationAdminAttachmentRepository;

    private ApplicationRepositoryInterface $applicationRepository;

    public function __construct(ApplicationAdminAttachmentRepositoryInterface $applicationAdminAttachmentRepository,
                                ApplicationRepositoryInterface                $applicationRepository)
    {
        $this->applicationAdminAttachmentRepository = $applicationAdminAttachmentRepository;
        $this->applicationRepository = $applicationRepository;
    }

    #[Route('/admin/application/{id}/admin-attachments', name: 'admin_application_admin_attachment_list')]
    public function list(MenuTypeFactoryRegistryInterface $menuFactory,
                         FormFactoryInterface             $formFactory,
                         Request                          $request,
                         UuidV4                           $id): Response
    {
        $application = $this->findApplicationOrThrow404($id);
        $this->assertIsGrantedUpdateAccess($application);

        $page = (int) $request->query->get('page', 1);
        $validExtensions = $application->getApplicationAdminAttachmentExtensions();
        $searchData = new ApplicationAdminAttachmentSearchData($validExtensions);
        $form = $formFactory->createNamed('', ApplicationAdminAttachmentSearchType::class, $searchData);
        $form->handleRequest($request);

        $isSearchInvalid = $form->isSubmitted() && !$form->isValid();
        if ($isSearchInvalid)
        {
            $searchData = new ApplicationAdminAttachmentSearchData($validExtensions);
        }

        $paginator = $this->applicationAdminAttachmentRepository->getAdminPaginator($searchData, $application, $page, 20);
        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', [
            'paginator' => $paginator,
        ]);

        $campDate = $application->getCampDate();
        $camp = $campDate?->getCamp();

        return $this->render('admin/application/admin_attachment/list.html.twig', [
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

    #[Route('/admin/application/{id}/create-admin-attachment', name: 'admin_application_admin_attachment_create')]
    public function create(EventDispatcherInterface $eventDispatcher, Request $request, UuidV4 $id): Response
    {
        $application = $this->findApplicationOrThrow404($id);
        $this->assertIsGrantedUpdateAccess($application);

        $data = new ApplicationAdminAttachmentCreateData();
        $form = $this->createForm(ApplicationAdminAttachmentCreateType::class, $data);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.application_admin_attachment_create.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new ApplicationAdminAttachmentCreateEvent($data, $application);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.application_admin_attachment.create');

            return $this->redirectToRoute('admin_application_admin_attachment_list', [
                'id' => $application->getId(),
            ]);
        }

        $campDate = $application->getCampDate();
        $camp = $campDate?->getCamp();

        return $this->render('admin/application/admin_attachment/update.html.twig', [
            'application'                       => $application,
            'form_application_admin_attachment' => $form->createView(),
            'breadcrumbs'                       => $this->createBreadcrumbs([
                'application' => $application,
                'camp_date'   => $campDate,
                'camp'        => $camp,
            ]),
        ]);
    }

    #[Route('/admin/application-admin-attachment/{id}/read', name: 'admin_application_admin_attachment_read')]
    public function read(ApplicationAdminAttachmentFilesystemInterface $applicationAdminAttachmentFilesystem,
                         UuidV4                                        $id): FileDownloadResponse
    {
        $applicationAdminAttachment = $this->applicationAdminAttachmentRepository->findOneById($id);

        if ($applicationAdminAttachment === null)
        {
            throw $this->createNotFoundException();
        }

        $application = $applicationAdminAttachment->getApplication();
        $this->assertIsGrantedReadAccess($application);
        $fileContents = $applicationAdminAttachmentFilesystem->getFileContents($applicationAdminAttachment);

        if ($fileContents === null)
        {
            throw $this->createNotFoundException();
        }

        $fileName = $applicationAdminAttachment->getLabel();
        $fileExtension = $applicationAdminAttachment->getExtension();

        return new FileDownloadResponse($fileName, $fileExtension, $fileContents);
    }

    #[Route('/admin/application-admin-attachment/{id}/update', name: 'admin_application_admin_attachment_update')]
    public function update(EventDispatcherInterface      $eventDispatcher,
                           DataTransferRegistryInterface $dataTransfer,
                           Request                       $request,
                           UuidV4                        $id): Response
    {
        $applicationAdminAttachment = $this->findApplicationAdminAttachmentOrThrow404($id);
        $application = $applicationAdminAttachment->getApplication();
        $this->assertIsGrantedUpdateAccess($application);

        $data = new ApplicationAdminAttachmentUpdateData();
        $dataTransfer->fillData($data, $applicationAdminAttachment);

        $form = $this->createForm(ApplicationAdminAttachmentUpdateType::class, $data);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.application_admin_attachment_update.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new ApplicationAdminAttachmentUpdateEvent($data, $applicationAdminAttachment);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.application_admin_attachment.update');

            return $this->redirectToRoute('admin_application_admin_attachment_list', [
                'id' => $application->getId(),
            ]);
        }

        $campDate = $application->getCampDate();
        $camp = $campDate?->getCamp();

        return $this->render('admin/application/admin_attachment/update.html.twig', [
            'application'                       => $application,
            'application_admin_attachment'      => $applicationAdminAttachment,
            'form_application_admin_attachment' => $form->createView(),
            'breadcrumbs'                       => $this->createBreadcrumbs([
                'application_admin_attachment' => $applicationAdminAttachment,
                'application'                  => $application,
                'camp_date'                    => $campDate,
                'camp'                         => $camp,
            ]),
        ]);
    }

    #[Route('/admin/application-admin-attachment/{id}/delete', name: 'admin_application_admin_attachment_delete')]
    public function delete(EventDispatcherInterface $eventDispatcher, Request $request, UuidV4 $id): Response
    {
        $applicationAdminAttachment = $this->findApplicationAdminAttachmentOrThrow404($id);
        $application = $applicationAdminAttachment->getApplication();
        $this->assertIsGrantedUpdateAccess($application);

        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'form.admin.application_admin_attachment_delete.button',
            'attr'  => ['class' => 'btn-danger'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new ApplicationAdminAttachmentDeleteEvent($applicationAdminAttachment);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.application_admin_attachment.delete');

            return $this->redirectToRoute('admin_application_admin_attachment_list', [
                'id' => $application->getId(),
            ]);
        }

        $campDate = $application->getCampDate();
        $camp = $campDate?->getCamp();

        return $this->render('admin/application/admin_attachment/delete.html.twig', [
            'application'                  => $application,
            'application_admin_attachment' => $applicationAdminAttachment,
            'form_delete'                  => $form->createView(),
            'breadcrumbs'                  => $this->createBreadcrumbs([
                'application_admin_attachment' => $applicationAdminAttachment,
                'application'                  => $application,
                'camp_date'                    => $campDate,
                'camp'                         => $camp,
            ]),
        ]);
    }

    private function assertIsGrantedUpdateAccess(Application $application): void
    {
        if (!$this->isGranted('application_update') && !$this->isGranted('guide_access_update', $application))
        {
            throw $this->createAccessDeniedException();
        }
    }

    private function assertIsGrantedReadAccess(Application $application): void
    {
        if (!$this->isGranted('application_read') && !$this->isGranted('guide_access_read', $application))
        {
            throw $this->createAccessDeniedException();
        }
    }

    private function findApplicationAdminAttachmentOrThrow404(UuidV4 $id): ApplicationAdminAttachment
    {
        $applicationAdminAttachment = $this->applicationAdminAttachmentRepository->findOneById($id);

        if ($applicationAdminAttachment === null)
        {
            throw $this->createNotFoundException();
        }

        return $applicationAdminAttachment;
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