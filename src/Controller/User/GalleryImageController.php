<?php

namespace App\Controller\User;

use App\Controller\AbstractController;
use App\Model\Entity\GalleryImage;
use App\Model\Entity\GalleryImageCategory;
use App\Model\Repository\GalleryImageCategoryRepositoryInterface;
use App\Model\Repository\GalleryImageRepositoryInterface;
use App\Service\Menu\Registry\MenuTypeFactoryRegistryInterface;
use App\Service\Routing\RouteNamerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\UuidV4;

class GalleryImageController extends AbstractController
{
    private GalleryImageRepositoryInterface $galleryImageRepository;

    private GalleryImageCategoryRepositoryInterface $galleryImageCategoryRepository;

    private RouteNamerInterface $routeNamer;

    public function __construct(GalleryImageRepositoryInterface         $galleryImageRepository,
                                GalleryImageCategoryRepositoryInterface $galleryImageCategoryRepository,
                                RouteNamerInterface                     $routeNamer)
    {
        $this->galleryImageRepository = $galleryImageRepository;
        $this->galleryImageCategoryRepository = $galleryImageCategoryRepository;
        $this->routeNamer = $routeNamer;
    }
    
    #[Route('/gallery/{path}', name: 'user_gallery_image_list', requirements: ['path' => '.+'])]
    public function list(MenuTypeFactoryRegistryInterface $menuFactory, Request $request, ?string $path = null): Response
    {
        $galleryImageCategory = null;

        if ($path === null || $path === '')
        {
            $galleryImageCategoryChildren = $this->galleryImageCategoryRepository->findRoots(true);
        }
        else
        {
            $galleryImageCategory = $this->findGalleryImageCategoryByPathOrThrow404($path);
            $galleryImageCategoryName = $galleryImageCategory->getName();
            $this->routeNamer->setCurrentRouteName($galleryImageCategoryName);
            $galleryImageCategoryChildren = $galleryImageCategory->getChildren();
        }

        $galleryImageCategoryChildren = $this->galleryImageCategoryRepository
            ->filterOutGalleryImageCategoriesWithoutGalleryImages($galleryImageCategoryChildren)
        ;

        $page = (int) $request->query->get('page', 1);
        $paginator = $this->galleryImageRepository->getUserPaginator($galleryImageCategory, $page, 24);

        if ($paginator->isCurrentPageOutOfBounds())
        {
            throw $this->createNotFoundException();
        }

        $paginationMenu = $menuFactory->buildMenuType('pagination', [
            'paginator' => $paginator,
        ]);

        return $this->render('user/gallery_image/list.html.twig', [
            'gallery_image_category'          => $galleryImageCategory,
            'gallery_image_category_children' => $galleryImageCategoryChildren,
            'paginator'                       => $paginator,
            'pagination_menu'                 => $paginationMenu,
            'breadcrumbs'                     => $this->createBreadcrumbs([
                'gallery_image_category' => $galleryImageCategory,
            ]),
        ]);
    }

    #[Route('/gallery-image/{galleryImageId}/{fromGalleryImageCategoryId}', name: 'user_gallery_image_read')]
    public function read(UuidV4 $galleryImageId, ?UuidV4 $fromGalleryImageCategoryId = null): Response
    {
        $galleryImage = $this->findGalleryImageOrThrow404($galleryImageId);
        $fromGalleryImageCategory = null;

        if ($fromGalleryImageCategoryId !== null)
        {
            $fromGalleryImageCategory = $this->findGalleryImageCategoryByIdOrThrow404($fromGalleryImageCategoryId);
        }

        // load all gallery image categories so that the gallery image category path does not trigger additional queries
        $this->galleryImageCategoryRepository->findAll();

        $galleryImageSurroundings = $this->galleryImageRepository->getGalleryImageSurroundings($galleryImage, $fromGalleryImageCategory);

        if ($galleryImageSurroundings === null)
        {
            throw $this->createNotFoundException();
        }

        return $this->render('user/gallery_image/read.html.twig', [
            'gallery_image'               => $galleryImage,
            'gallery_image_surroundings'  => $galleryImageSurroundings,
            'from_gallery_image_category' => $fromGalleryImageCategory,
            'breadcrumbs'                => $this->createBreadcrumbs([
                'gallery_image'          => $galleryImage,
                'gallery_image_category' => $fromGalleryImageCategory,
            ]),
        ]);
    }

    private function findGalleryImageCategoryByPathOrThrow404(string $path): GalleryImageCategory
    {
        $galleryImageCategory = $this->galleryImageCategoryRepository->findOneByPath($path);
        $hasGalleryImage = $this->galleryImageCategoryRepository->galleryImageCategoryHasGalleryImage($galleryImageCategory);

        if ($galleryImageCategory === null || !$hasGalleryImage)
        {
            throw $this->createNotFoundException();
        }

        return $galleryImageCategory;
    }

    private function findGalleryImageCategoryByIdOrThrow404(UuidV4 $id): GalleryImageCategory
    {
        $galleryImageCategory = $this->galleryImageCategoryRepository->findOneById($id);

        if ($galleryImageCategory === null)
        {
            throw $this->createNotFoundException();
        }

        return $galleryImageCategory;
    }

    private function findGalleryImageOrThrow404(UuidV4 $id): GalleryImage
    {
        $galleryImage = $this->galleryImageRepository->findOneById($id);

        if ($galleryImage === null || $galleryImage->isHiddenInGallery())
        {
            throw $this->createNotFoundException();
        }

        return $galleryImage;
    }
}