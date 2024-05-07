<?php

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Library\Data\Admin\GalleryImageCategoryData;
use App\Model\Entity\GalleryImageCategory;
use App\Model\Event\Admin\GalleryImageCategory\GalleryImageCategoryCreateEvent;
use App\Model\Event\Admin\GalleryImageCategory\GalleryImageCategoryDeleteEvent;
use App\Model\Event\Admin\GalleryImageCategory\GalleryImageCategoryUpdateEvent;
use App\Model\Repository\GalleryImageCategoryRepositoryInterface;
use App\Service\Data\Registry\DataTransferRegistryInterface;
use App\Service\Form\Type\Admin\GalleryImageCategoryType;
use App\Service\Form\Type\Common\HiddenTrueType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\UuidV4;

#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
class GalleryImageCategoryController extends AbstractController
{
    private GalleryImageCategoryRepositoryInterface $galleryImageCategoryRepository;

    public function __construct(GalleryImageCategoryRepositoryInterface $galleryImageCategoryRepository)
    {
        $this->galleryImageCategoryRepository = $galleryImageCategoryRepository;
    }

    #[IsGranted(new Expression('is_granted("gallery_image_category", "any_admin_permission")'))]
    #[Route('/admin/gallery-image-categories', name: 'admin_gallery_image_category_list')]
    public function list(): Response
    {
        $rootCategories = $this->galleryImageCategoryRepository->findRoots(true);

        return $this->render('admin/gallery_image_category/list.html.twig', [
            'root_categories' => $rootCategories,
            'breadcrumbs'     => $this->createBreadcrumbs(),
        ]);
    }

    #[IsGranted('gallery_image_category_create')]
    #[Route('/admin/gallery-image-category/create', name: 'admin_gallery_image_category_create')]
    public function create(EventDispatcherInterface $eventDispatcher, Request $request): Response
    {
        $galleryImageCategoryData = new GalleryImageCategoryData();
        $parentChoices = $this->galleryImageCategoryRepository->findAll();

        $form = $this->createForm(GalleryImageCategoryType::class, $galleryImageCategoryData, [
            'choices_gallery_image_categories' => $parentChoices
        ]);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.gallery_image_category.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new GalleryImageCategoryCreateEvent($galleryImageCategoryData);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.gallery_image_category.create');

            return $this->redirectToRoute('admin_gallery_image_category_list');
        }

        return $this->render('admin/gallery_image_category/update.html.twig', [
            'form_gallery_image_category' => $form->createView(),
            'breadcrumbs'                 => $this->createBreadcrumbs(),
        ]);
    }

    #[IsGranted('gallery_image_category_read')]
    #[Route('/admin/gallery-image-category/{id}/read', name: 'admin_gallery_image_category_read')]
    public function read(UuidV4 $id): Response
    {
        $galleryImageCategory = $this->findGalleryImageCategoryOrThrow404($id);

        return $this->render('admin/gallery_image_category/read.html.twig', [
            'gallery_image_category' => $galleryImageCategory,
            'breadcrumbs'            => $this->createBreadcrumbs([
                'gallery_image_category' => $galleryImageCategory,
            ]),
        ]);
    }

    #[IsGranted('gallery_image_category_update')]
    #[Route('/admin/gallery-image-category/{id}/update', name: 'admin_gallery_image_category_update')]
    public function update(EventDispatcherInterface $eventDispatcher, DataTransferRegistryInterface $dataTransfer, Request $request, UuidV4 $id): Response
    {
        $galleryImageCategory = $this->findGalleryImageCategoryOrThrow404($id);
        $parentChoices = $this->galleryImageCategoryRepository->findPossibleParents($galleryImageCategory);

        $galleryImageCategoryData = new GalleryImageCategoryData($galleryImageCategory);
        $dataTransfer->fillData($galleryImageCategoryData, $galleryImageCategory);

        $form = $this->createForm(GalleryImageCategoryType::class, $galleryImageCategoryData, [
            'choices_gallery_image_categories' => $parentChoices
        ]);
        $form->add('submit', SubmitType::class, ['label' => 'form.admin.gallery_image_category.button']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new GalleryImageCategoryUpdateEvent($galleryImageCategoryData, $galleryImageCategory);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.gallery_image_category.update');

            return $this->redirectToRoute('admin_gallery_image_category_list');
        }

        return $this->render('admin/gallery_image_category/update.html.twig', [
            'gallery_image_category'      => $galleryImageCategory,
            'form_gallery_image_category' => $form->createView(),
            'breadcrumbs'                 => $this->createBreadcrumbs([
                'gallery_image_category' => $galleryImageCategory,
            ]),
        ]);
    }

    #[IsGranted('gallery_image_category_delete')]
    #[Route('/admin/gallery-image-category/{id}/delete', name: 'admin_gallery_image_category_delete')]
    public function delete(EventDispatcherInterface $eventDispatcher, Request $request, UuidV4 $id): Response
    {
        $galleryImageCategory = $this->findGalleryImageCategoryOrThrow404($id);

        $form = $this->createForm(HiddenTrueType::class);
        $form->add('submit', SubmitType::class, [
            'label' => 'form.admin.gallery_image_category_delete.button',
            'attr'  => ['class' => 'btn-danger'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $event = new GalleryImageCategoryDeleteEvent($galleryImageCategory);
            $eventDispatcher->dispatch($event, $event::NAME);
            $this->addTransFlash('success', 'crud.action_performed.gallery_image_category.delete');

            return $this->redirectToRoute('admin_gallery_image_category_list');
        }

        return $this->render('admin/gallery_image_category/delete.html.twig', [
            'gallery_image_category' => $galleryImageCategory,
            'form_delete' => $form->createView(),
            'breadcrumbs' => $this->createBreadcrumbs([
                'gallery_image_category' => $galleryImageCategory,
            ]),
        ]);
    }

    private function findGalleryImageCategoryOrThrow404(UuidV4 $id): GalleryImageCategory
    {
        $galleryImageCategory = $this->galleryImageCategoryRepository->findOneById($id);
        
        if ($galleryImageCategory === null)
        {
            throw $this->createNotFoundException();
        }

        return $galleryImageCategory;
    }
}