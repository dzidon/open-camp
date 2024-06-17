<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\GalleryImageSearchData;
use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\GalleryImage;
use App\Model\Entity\GalleryImageCategory;
use Symfony\Component\Uid\UuidV4;

interface GalleryImageRepositoryInterface
{
    /**
     * Saves a gallery image.
     *
     * @param GalleryImage $galleryImage
     * @param bool $flush
     * @return void
     */
    public function saveGalleryImage(GalleryImage $galleryImage, bool $flush): void;

    /**
     * Removes a gallery image.
     *
     * @param GalleryImage $galleryImage
     * @param bool $flush
     * @return void
     */
    public function removeGalleryImage(GalleryImage $galleryImage, bool $flush): void;

    /**
     * Finds one gallery image by id.
     *
     * @param UuidV4 $id
     * @return GalleryImage|null
     */
    public function findOneById(UuidV4 $id): ?GalleryImage;

    /**
     * Finds all gallery images to be shown in carousel.
     *
     * @return GalleryImage[]
     */
    public function findForCarousel(): array;

    /**
     * Finds images by the given category.
     *
     * @param GalleryImageCategory $galleryImageCategory
     * @param bool $offspringCategories
     * @return GalleryImage[]
     */
    public function findByGalleryImageCategory(GalleryImageCategory $galleryImageCategory,
                                               bool                 $offspringCategories = false): array;

    /**
     * Returns true if there is at least one image shown in the gallery.
     *
     * @return bool
     */
    public function existsAtLeastOneImageInGallery(): bool;

    /**
     * Returns admin gallery search paginator.
     *
     * @param GalleryImageSearchData $galleryImageSearchData
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getAdminPaginator(GalleryImageSearchData $galleryImageSearchData,
                                      int                    $currentPage,
                                      int                    $pageSize): PaginatorInterface;

    /**
     * Returns user gallery search paginator.
     *
     * @param null|GalleryImageCategory $galleryImageCategory
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getUserPaginator(?GalleryImageCategory $galleryImageCategory,
                                     int                   $currentPage,
                                     int                   $pageSize): PaginatorInterface;
}