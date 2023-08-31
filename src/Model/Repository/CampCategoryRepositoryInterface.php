<?php

namespace App\Model\Repository;

use App\Model\Entity\CampCategory;
use Symfony\Component\Uid\UuidV4;

/**
 * Camp category CRUD.
 */
interface CampCategoryRepositoryInterface
{
    /**
     * Saves a camp category.
     *
     * @param CampCategory $campCategory
     * @param bool $flush
     * @return void
     */
    public function saveCampCategory(CampCategory $campCategory, bool $flush): void;

    /**
     * Removes a camp category.
     *
     * @param CampCategory $campCategory
     * @param bool $flush
     * @return void
     */
    public function removeCampCategory(CampCategory $campCategory, bool $flush): void;

    /**
     * Creates a camp category.
     *
     * @param string $name
     * @param string $urlName
     * @return CampCategory
     */
    public function createCampCategory(string $name, string $urlName): CampCategory;

    /**
     * Finds one camp category by id.
     *
     * @param UuidV4 $id
     * @return CampCategory|null
     */
    public function findOneById(UuidV4 $id): ?CampCategory;

    /**
     * Finds all camp categories.
     *
     * @return CampCategory[]
     */
    public function findAll(): array;

    /**
     * Finds camp categories that have no parent.
     *
     * @return CampCategory[]
     */
    public function findRoots(): array;

    /**
     * Finds camp categories that can be set as parents of the given camp category.
     *
     * @param CampCategory $category
     * @return CampCategory[]
     */
    public function findPossibleParents(CampCategory $category): array;

    /**
     * Finds one camp category by its absolute url name path.
     *
     * @param string $path
     * @return CampCategory|null
     */
    public function findOneByPath(string $path): ?CampCategory;

    /**
     * Returns an array of camp categories with the given url name.
     *
     * @param string $urlName
     * @return CampCategory[]
     */
    public function findByUrlName(string $urlName): array;
}