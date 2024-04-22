<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\GalleryImageData;
use App\Library\Data\Admin\GalleryImageSearchData;
use App\Library\Data\Admin\GalleryImagesUploadData;
use App\Model\Entity\GalleryImage;
use App\Model\Event\Admin\GalleryImage\GalleryImagesCreateEvent;
use App\Model\Event\Admin\GalleryImage\GalleryImageDeleteEvent;
use App\Model\Event\Admin\GalleryImage\GalleryImageUpdateEvent;
use App\Model\Repository\GalleryImageCategoryRepositoryInterface;
use App\Model\Repository\GalleryImageRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Admin\GalleryImageSearchType;
use App\Service\Form\Type\Admin\GalleryImagesUploadType;
use App\Service\Form\Type\Admin\GalleryImageType;
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
class GalleryImageController extends AbstractController
{
    private GalleryImageRepositoryInterface $galleryImageRepository;

    public function __construct(GalleryImageRepositoryInterface $galleryImageRepository)
    {
        $this->galleryImageRepository = $galleryImageRepository;
    }

    #[IsGranted(new Expression('is_granted("gallery_image", "any_admin_permission")'))]
    #[Route('/admin/gallery-images', name: 'admin_gallery_image_list')]
    public function list(MenuTypeFactoryRegistryInterface        $menuFactory,
                         FormFactoryInterface                    $formFactory,
                         GalleryImageCategoryRepositoryInterface $galleryImageCategoryRepository,
                         Request                                 $request): Response
    {
        $galleryImageCategories = $galleryImageCategoryRepository->findAll();

        $page = (int) $request->query->get('page', 1);
        $searchData = new GalleryImageSearchData();
        $form = $formFactory->createNamed('', GalleryImageSearchType::class, $searchData, [
            'choices_gallery_image_categories' => $galleryImageCategories,
        ]);
        $form->handleRequest($request);
        $isSearchInvalid = $form->isSubmitted() && !$form->isValid();

        if ($isSearchInvalid)
        {
            $searchData = new GalleryImageSearchData();
        }

        $paginator = $this->galleryImageRepository->getAdminPaginator($searchData, $page, 20);

        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', ['paginator' => $paginator]);

        return $this->render('admin/gallery_image/list.html.twig', [
            'form_search'       => $form->createView(),
            'pagination_menu'   => $paginationMenu,
            'paginator'         => $paginator,
            'is_search_invalid' => $isSearchInvalid,
            'breadcrumbs'       => $this->createBreadcrumbs(),
        ]);
    }

    #[IsGranted('gallery_image_create')]
    #[Route('/admin/gallery-images/upload', name: 'admin_gallery_image_create')]
    public function create(EventDispatcherInterface                $eventDispatcher,
                           GalleryImageCategoryRepositoryInterface $galleryImageCategoryRepository,
                           Request                                 $request): Response
    {
        $galleryImageCategories = $galleryImageCategoryRepository->findAll();
        $galleryImagesUploadData = new GalleryImagesUploadData($galleryImageCategories);
        $form = $this->createForm(GalleryImagesUploadType::class, $galleryImagesUploadData);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.gallery_images_upload.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new GalleryImagesCreateEvent($galleryImagesUploadData);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.gallery_image.upload');

            return $this->redirectToRoute('admin_gallery_image_list');
        }

        return $this->render('admin/gallery_image/update.html.twig', [
            'form_gallery_image' => $form->createView(),
            'breadcrumbs'        => $this->createBreadcrumbs(),
        ]);
    }

    #[IsGranted('gallery_image_read')]
    #[Route('/admin/gallery-image/{id}/read', name: 'admin_gallery_image_read')]
    public function read(GalleryImageCategoryRepositoryInterface $galleryImageCategoryRepository,
                         UuidV4                                  $id): Response
    {
        $galleryImage = $this->findGalleryImageOrThrow404($id);
        $galleryImageCategoryRepository->findAll();

        return $this->render('admin/gallery_image/read.html.twig', [
            'gallery_image' => $galleryImage,
            'breadcrumbs'   => $this->createBreadcrumbs([
                'gallery_image' => $galleryImage,
            ]),
        ]);
    }

    #[IsGranted('gallery_image_update')]
    #[Route('/admin/gallery-image/{id}/update', name: 'admin_gallery_image_update')]
    public function update(EventDispatcherInterface                $eventDispatcher,
                           DataTransferRegistryInterface           $dataTransfer,
                           GalleryImageCategoryRepositoryInterface $galleryImageCategoryRepository,
                           Request                                 $request,
                           UuidV4                                  $id): Response
    {
        $galleryImage = $this->findGalleryImageOrThrow404($id);
        $galleryImageCategories = $galleryImageCategoryRepository->findAll();
        $galleryImageData = new GalleryImageData($galleryImageCategories);
        $dataTransfer->fillData($galleryImageData, $galleryImage);

        $form = $this->createForm(GalleryImageType::class, $galleryImageData);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.gallery_image.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new GalleryImageUpdateEvent($galleryImageData, $galleryImage);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.gallery_image.update');

            return $this->redirectToRoute('admin_gallery_image_list');
        }

        return $this->render('admin/gallery_image/update.html.twig', [
            'gallery_image'      => $galleryImage,
            'form_gallery_image' => $form->createView(),
            'breadcrumbs'        => $this->createBreadcrumbs([
                'gallery_image' => $galleryImage,
            ]),
        ]);
    }

    #[IsGranted('gallery_image_delete')]
    #[Route('/admin/gallery-image/{id}/delete', name: 'admin_gallery_image_delete')]
    public function delete(EventDispatcherInterface $eventDispatcher, Request $request, UuidV4 $id): Response
    {
        $galleryImage = $this->findGalleryImageOrThrow404($id);

        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'form.admin.gallery_image_delete.button',
            'attr'  => ['class' => 'btn-danger'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new GalleryImageDeleteEvent($galleryImage);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.gallery_image.delete');

            return $this->redirectToRoute('admin_gallery_image_list');
        }

        return $this->render('admin/gallery_image/delete.html.twig', [
            'gallery_image' => $galleryImage,
            'form_delete'   => $form->createView(),
            'breadcrumbs'   => $this->createBreadcrumbs([
                'gallery_image' => $galleryImage,
            ]),
        ]);
    }

    private function findGalleryImageOrThrow404(UuidV4 $id): GalleryImage
    {
        $galleryImage = $this->galleryImageRepository->findOneById($id);

        if ($galleryImage === null)
        {
            throw $this->createNotFoundException();
        }

        return $galleryImage;
    }
}