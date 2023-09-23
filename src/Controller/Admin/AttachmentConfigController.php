<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\AttachmentConfigData;
use App\Library\Data\Admin\AttachmentConfigSearchData;
use App\Model\Entity\AttachmentConfig;
use App\Model\Repository\AttachmentConfigRepositoryInterface;
use App\Model\Repository\FileExtensionRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Admin\AttachmentConfigSearchType;
use App\Service\Form\Type\Admin\AttachmentConfigType;
use App\Service\Form\Type\Common\HiddenTrueType;
use App\Service\Menu\Breadcrumbs\Admin\AttachmentConfigBreadcrumbsInterface;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\UuidV4;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
class AttachmentConfigController extends AbstractController
{
    private AttachmentConfigRepositoryInterface $attachmentConfigRepository;
    private AttachmentConfigBreadcrumbsInterface $attachmentConfigBreadcrumbs;

    public function __construct(AttachmentConfigRepositoryInterface  $attachmentConfigRepository,
                                AttachmentConfigBreadcrumbsInterface $attachmentConfigBreadcrumbs)
    {
        $this->attachmentConfigRepository = $attachmentConfigRepository;
        $this->attachmentConfigBreadcrumbs = $attachmentConfigBreadcrumbs;
    }

    #[IsGranted(new Expression('is_granted("attachment_config_create") or is_granted("attachment_config_read") or 
                                         is_granted("attachment_config_update") or is_granted("attachment_config_delete")'))]
    #[Route('/admin/attachment-configs', name: 'admin_attachment_config_list')]
    public function list(FileExtensionRepositoryInterface $fileExtensionRepository,
                         MenuTypeFactoryRegistryInterface $menuFactory,
                         FormFactoryInterface             $formFactory,
                         Request                          $request): Response
    {
        $page = (int) $request->query->get('page', 1);
        $searchData = new AttachmentConfigSearchData();
        $form = $formFactory->createNamed('', AttachmentConfigSearchType::class, $searchData, [
            'choices_file_extensions' => $fileExtensionRepository->findForAttachmentConfigs(),
        ]);
        $form->handleRequest($request);

        $isSearchInvalid = $form->isSubmitted() && !$form->isValid();
        if ($isSearchInvalid)
        {
            $searchData = new AttachmentConfigSearchData();
        }

        $paginator = $this->attachmentConfigRepository->getAdminPaginator($searchData, $page, 20);
        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', ['paginator' => $paginator]);

        return $this->render('admin/attachment_config/list.html.twig', [
            'form_search'       => $form->createView(),
            'pagination_menu'   => $paginationMenu,
            'paginator'         => $paginator,
            'is_search_invalid' => $isSearchInvalid,
            'breadcrumbs'       => $this->attachmentConfigBreadcrumbs->buildList(),
        ]);
    }

    #[IsGranted('attachment_config_create')]
    #[Route('/admin/attachment-config/create', name: 'admin_attachment_config_create')]
    public function create(DataTransferRegistryInterface $dataTransfer, Request $request): Response
    {
        $attachmentConfigData = new AttachmentConfigData();

        $form = $this->createForm(AttachmentConfigType::class, $attachmentConfigData);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.attachment_config.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $attachmentConfig = new AttachmentConfig($attachmentConfigData->getName(), $attachmentConfigData->getMaxSize());
            $dataTransfer->fillEntity($attachmentConfigData, $attachmentConfig);
            $this->attachmentConfigRepository->saveAttachmentConfig($attachmentConfig, true);
            $this->addTransFlash('success', 'crud.action_performed.attachment_config.create');

            return $this->redirectToRoute('admin_attachment_config_list');
        }

        return $this->render('admin/attachment_config/update.html.twig', [
            'form_attachment_config' => $form->createView(),
            'breadcrumbs'            => $this->attachmentConfigBreadcrumbs->buildCreate(),
        ]);
    }

    #[IsGranted('attachment_config_read')]
    #[Route('/admin/attachment-config/{id}/read', name: 'admin_attachment_config_read')]
    public function read(UuidV4 $id): Response
    {
        $attachmentConfig = $this->findAttachmentConfigOrThrow404($id);

        return $this->render('admin/attachment_config/read.html.twig', [
            'attachment_config' => $attachmentConfig,
            'breadcrumbs'       => $this->attachmentConfigBreadcrumbs->buildRead($attachmentConfig),
        ]);
    }

    #[IsGranted('attachment_config_update')]
    #[Route('/admin/attachment-config/{id}/update', name: 'admin_attachment_config_update')]
    public function update(DataTransferRegistryInterface $dataTransfer, Request $request, UuidV4 $id): Response
    {
        $attachmentConfig = $this->findAttachmentConfigOrThrow404($id);

        $attachmentConfigData = new AttachmentConfigData();
        $dataTransfer->fillData($attachmentConfigData, $attachmentConfig);

        $form = $this->createForm(AttachmentConfigType::class, $attachmentConfigData);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.attachment_config.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $dataTransfer->fillEntity($attachmentConfigData, $attachmentConfig);
            $this->attachmentConfigRepository->saveAttachmentConfig($attachmentConfig, true);
            $this->addTransFlash('success', 'crud.action_performed.attachment_config.update');

            return $this->redirectToRoute('admin_attachment_config_list');
        }

        return $this->render('admin/attachment_config/update.html.twig', [
            'attachment_config'      => $attachmentConfig,
            'form_attachment_config' => $form->createView(),
            'breadcrumbs'            => $this->attachmentConfigBreadcrumbs->buildUpdate($attachmentConfig),
        ]);
    }

    #[IsGranted('attachment_config_delete')]
    #[Route('/admin/attachment-config/{id}/delete', name: 'admin_attachment_config_delete')]
    public function delete(Request $request, UuidV4 $id): Response
    {
        $attachmentConfig = $this->findAttachmentConfigOrThrow404($id);

        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'form.admin.attachment_config_delete.button',
            'attr'  => ['class' => 'btn-danger'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->attachmentConfigRepository->removeAttachmentConfig($attachmentConfig, true);
            $this->addTransFlash('success', 'crud.action_performed.attachment_config.delete');

            return $this->redirectToRoute('admin_attachment_config_list');
        }

        return $this->render('admin/attachment_config/delete.html.twig', [
            'attachment_config' => $attachmentConfig,
            'form_delete'       => $form->createView(),
            'breadcrumbs'       => $this->attachmentConfigBreadcrumbs->buildDelete($attachmentConfig),
        ]);
    }

    private function findAttachmentConfigOrThrow404(UuidV4 $id): AttachmentConfig
    {
        $attachmentConfig = $this->attachmentConfigRepository->findOneById($id);
        if ($attachmentConfig === null)
        {
            throw $this->createNotFoundException();
        }

        return $attachmentConfig;
    }
}