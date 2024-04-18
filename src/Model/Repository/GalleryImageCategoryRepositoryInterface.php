<?php

namespace App\Model\Repository;

use App\Model\Entity\GalleryImageCategory;
use Symfony\Component\Uid\UuidV4;

interface GalleryImageCategoryRepositoryInterface
{
    /**
     * Saves a gallery image category.
     *
     * @param GalleryImageCategory $galleryImageCategory
     * @param bool $flush
     * @return void
     */
    public function saveGalleryImageCategory(GalleryImageCategory $galleryImageCategory, bool $flush): void;

    /**
     * Removes a gallery image category.
     *
     * @param GalleryImageCategory $galleryImageCategory
     * @param bool $flush
     * @return void
     */
    public function removeGalleryImageCategory(GalleryImageCategory $galleryImageCategory, bool $flush): void;

    /**
     * Finds one gallery image category by id.
     *
     * @param UuidV4 $id
     * @return GalleryImageCategory|null
     */
    public function findOneById(UuidV4 $id): ?GalleryImageCategory;

    /**
     * Finds all gallery image categories.
     *
     * @return GalleryImageCategory[]
     */
    public function findAll(): array;

    /**
     * Finds gallery image categories that have no parent.
     *
     * @return GalleryImageCategory[]
     */
    public function findRoots(): array;

    /**
     * Finds gallery image categories that can be set as parents of the given gallery image category.
     *
     * @param GalleryImageCategory $category
     * @return GalleryImageCategory[]
     */
    public function findPossibleParents(GalleryImageCategory $category): array;

    /**
     * Finds one gallery image category by its absolute url name path.
     *
     * @param string $path
     * @return GalleryImageCategory|null
     */
    public function findOneByPath(string $path): ?GalleryImageCategory;

    /**
     * Returns an array of gallery image categories with the given url name.
     *
     * @param string $urlName
     * @return GalleryImageCategory[]
     */
    public function findByUrlName(string $urlName): array;

    /**
     * Returns true if there is a gallery image attached to the given gallery image category or one of its descendents.
     *
     * @param GalleryImageCategory $galleryImageCategory
     * @return bool
     */
    public function galleryImageCategoryHasGalleryImage(GalleryImageCategory $galleryImageCategory): bool;

    /**
     * Filters out gallery image categories that have no gallery images attached to them or their descendents.
     *
     * @param GalleryImageCategory[] $galleryImageCategories
     * @return array
     */
    public function filterOutGalleryImageCategoriesWithoutGalleryImages(array $galleryImageCategories): array;
}