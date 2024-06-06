<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\DownloadableFileCreateData;
use App\Library\Data\Admin\DownloadableFileSearchData;
use App\Library\Data\Admin\DownloadableFileUpdateData;
use App\Model\Entity\DownloadableFile;
use App\Model\Event\Admin\DownloadableFile\DownloadableFileCreateEvent;
use App\Model\Event\Admin\DownloadableFile\DownloadableFileDeleteEvent;
use App\Model\Event\Admin\DownloadableFile\DownloadableFileUpdateEvent;
use App\Model\Repository\DownloadableFileRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Admin\DownloadableFileCreateType;
use App\Service\Form\Type\Admin\DownloadableFileSearchType;
use App\Service\Form\Type\Admin\DownloadableFileUpdateType;
use App\Service\Form\Type\Common\HiddenTrueType;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\UuidV4;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
class DownloadableFileController extends AbstractController
{
    private DownloadableFileRepositoryInterface $downloadableFileRepository;

    public function __construct(DownloadableFileRepositoryInterface $downloadableFileRepository)
    {
        $this->downloadableFileRepository = $downloadableFileRepository;
    }

    #[IsGranted(new Expression('is_granted("downloadable_file", "any_admin_permission")'))]
    #[Route('/admin/downloadable-files', name: 'admin_downloadable_file_list')]
    public function list(MenuTypeFactoryRegistryInterface $menuFactory,
                         FormFactoryInterface             $formFactory,
                         Request                          $request): Response
    {
        $page = (int) $request->query->get('page', 1);
        $validExtensions = $this->downloadableFileRepository->findUsedExtensions();
        $searchData = new DownloadableFileSearchData($validExtensions);
        $form = $formFactory->createNamed('', DownloadableFileSearchType::class, $searchData);
        $form->handleRequest($request);
        $isSearchInvalid = $form->isSubmitted() && !$form->isValid();

        if ($isSearchInvalid)
        {
            $searchData = new DownloadableFileSearchData($validExtensions);
        }

        $paginator = $this->downloadableFileRepository->getAdminPaginator($searchData, $page, 20);

        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', [
            'paginator' => $paginator,
        ]);

        return $this->render('admin/downloadable_file/list.html.twig', [
            'form_search'       => $form->createView(),
            'pagination_menu'   => $paginationMenu,
            'paginator'         => $paginator,
            'is_search_invalid' => $isSearchInvalid,
            'breadcrumbs'       => $this->createBreadcrumbs(),
        ]);
    }

    #[IsGranted('downloadable_file_create')]
    #[Route('/admin/downloadable-file/create', name: 'admin_downloadable_file_create')]
    public function create(EventDispatcherInterface $eventDispatcher, Request $request): Response
    {
        $data = new DownloadableFileCreateData();
        $form = $this->createForm(DownloadableFileCreateType::class, $data);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.downloadable_file_create.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new DownloadableFileCreateEvent($data);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.downloadable_file.create');

            return $this->redirectToRoute('admin_downloadable_file_list');
        }

        return $this->render('admin/downloadable_file/update.html.twig', [
            'form_downloadable_file' => $form->createView(),
            'breadcrumbs'            => $this->createBreadcrumbs(),
        ]);
    }

    #[IsGranted('downloadable_file_read')]
    #[Route('/admin/downloadable-file/{id}/read', name: 'admin_downloadable_file_read')]
    public function read(UuidV4 $id): Response
    {
        $downloadableFile = $this->findDownloadableFileOrThrow404($id);

        return $this->render('admin/downloadable_file/read.html.twig', [
            'downloadable_file' => $downloadableFile,
            'breadcrumbs'       => $this->createBreadcrumbs([
                'downloadable_file' => $downloadableFile,
            ]),
        ]);
    }

    #[IsGranted('downloadable_file_update')]
    #[Route('/admin/downloadable-file/{id}/update', name: 'admin_downloadable_file_update')]
    public function update(EventDispatcherInterface      $eventDispatcher,
                           DataTransferRegistryInterface $dataTransfer,
                           Request                       $request,
                           UuidV4                        $id): Response
    {
        $downloadableFile = $this->findDownloadableFileOrThrow404($id);
        $data = new DownloadableFileUpdateData();
        $dataTransfer->fillData($data, $downloadableFile);

        $form = $this->createForm(DownloadableFileUpdateType::class, $data);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.downloadable_file_update.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new DownloadableFileUpdateEvent($data, $downloadableFile);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.downloadable_file.update');

            return $this->redirectToRoute('admin_downloadable_file_list');
        }

        return $this->render('admin/downloadable_file/update.html.twig', [
            'downloadable_file'      => $downloadableFile,
            'form_downloadable_file' => $form->createView(),
            'breadcrumbs'            => $this->createBreadcrumbs([
                'downloadable_file' => $downloadableFile,
            ]),
        ]);
    }

    #[IsGranted('downloadable_file_delete')]
    #[Route('/admin/downloadable-file/{id}/delete', name: 'admin_downloadable_file_delete')]
    public function delete(EventDispatcherInterface $eventDispatcher, Request $request, UuidV4 $id): Response
    {
        $downloadableFile = $this->findDownloadableFileOrThrow404($id);
        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'form.admin.downloadable_file_delete.button',
            'attr'  => ['class' => 'btn-danger'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new DownloadableFileDeleteEvent($downloadableFile);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.downloadable_file.delete');

            return $this->redirectToRoute('admin_downloadable_file_list');
        }

        return $this->render('admin/downloadable_file/delete.html.twig', [
            'downloadable_file' => $downloadableFile,
            'form_delete'       => $form->createView(),
            'breadcrumbs'       => $this->createBreadcrumbs([
                'downloadable_file' => $downloadableFile,
            ]),
        ]);
    }

    private function findDownloadableFileOrThrow404(UuidV4 $id): DownloadableFile
    {
        $downloadableFile = $this->downloadableFileRepository->findOneById($id);

        if ($downloadableFile === null)
        {
            throw $this->createNotFoundException();
        }

        return $downloadableFile;
    }
}