<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Library\Data\User\ApplicationProfileSearchData;
use App\Library\Http\Response\PdfResponse;
use App\Model\Entity\Application;
use App\Model\Entity\User;
use App\Model\Event\User\Application\ApplicationAttachmentsUploadLaterEvent;
use App\Model\Repository\ApplicationRepositoryInterface;
use App\Model\Service\Application\ApplicationInvoiceFilesystemInterface;
use App\Model\Service\ApplicationAttachment\ApplicationAttachmentsUploadLaterDataFactoryInterface;
use App\Service\Form\Type\User\ApplicationAttachmentsUploadLaterType;
use App\Service\Form\Type\User\ApplicationProfileSearchType;
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
#[Route('/profile')]
class ProfileApplicationController extends AbstractController
{
    private ApplicationRepositoryInterface $applicationRepository;

    public function __construct(ApplicationRepositoryInterface $applicationRepository)
    {
        $this->applicationRepository = $applicationRepository;
    }

    #[Route('/applications', name: 'user_profile_application_list')]
    public function list(MenuTypeFactoryRegistryInterface $menuFactory,
                         FormFactoryInterface             $formFactory,
                         Request                          $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $page = (int) $request->query->get('page', 1);
        $searchData = new ApplicationProfileSearchData();
        $form = $formFactory->createNamed('', ApplicationProfileSearchType::class, $searchData);
        $form->handleRequest($request);

        $isSearchInvalid = $form->isSubmitted() && !$form->isValid();
        if ($isSearchInvalid)
        {
            $searchData = new ApplicationProfileSearchData();
        }

        $paginator = $this->applicationRepository->getUserPaginator($searchData, $user, $page, 10);
        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', ['paginator' => $paginator]);

        return $this->render('user/profile/application/list.html.twig', [
            'form_search'       => $form->createView(),
            'pagination_menu'   => $paginationMenu,
            'paginator'         => $paginator,
            'is_search_invalid' => $isSearchInvalid,
            'breadcrumbs'       => $this->createBreadcrumbs(),
        ]);
    }

    #[Route('/application/{id}/read', name: 'user_profile_application_read')]
    public function read(ApplicationAttachmentsUploadLaterDataFactoryInterface $dataFactory,
                         ApplicationInvoiceFilesystemInterface                 $applicationInvoiceFilesystem,
                         EventDispatcherInterface                              $eventDispatcher,
                         Request                                               $request,
                         UuidV4                                                $id): Response
    {
        $application = $this->findCompletedApplicationOrThrow404($id);
        $this->denyAccessUnlessGranted('application_read', $application);
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
                $eventDispatcher->dispatch($event, $event::NAME);
                $this->addTransFlash('success', 'crud.action_performed.application_attachment.upload_later');

                return $this->redirectToRoute('user_profile_application_read', [
                    'id' => $application->getId(),
                ]);
            }
        }

        $invoiceFound = $applicationInvoiceFilesystem->getInvoiceContents($application);

        return $this->render('user/profile/application/read.html.twig', [
            'application'                   => $application,
            'invoice_found'                 => $invoiceFound,
            'form_attachments_upload_later' => $form,
            'breadcrumbs'                   => $this->createBreadcrumbs([
                'application' => $application,
            ]),
        ]);
    }

    #[Route('/application/{id}/invoice', name: 'user_profile_application_invoice')]
    public function invoice(ApplicationInvoiceFilesystemInterface $applicationInvoiceFilesystem, UuidV4 $id): PdfResponse
    {
        $application = $this->findCompletedApplicationOrThrow404($id);
        $this->denyAccessUnlessGranted('application_read', $application);

        $invoiceContents = $applicationInvoiceFilesystem->getInvoiceContents($application);

        if ($invoiceContents === null)
        {
            throw $this->createNotFoundException();
        }

        return new PdfResponse('', $invoiceContents);
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