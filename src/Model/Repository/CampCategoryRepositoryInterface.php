<?php

namespace App\Model\Repository;

use App\Model\Entity\CampCategory;
use Symfony\Component\Uid\UuidV4;

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

    /**
     * Returns true if there is a camp attached to the given camp category or one of its descendents.
     *
     * @param CampCategory $campCategory
     * @param bool $showHiddenCamps
     * @return bool
     */
    public function campCategoryHasCamp(CampCategory $campCategory, bool $showHiddenCamps = true): bool;

    /**
     * Filters out camp categories that have no camps attached to them or their descendents.
     *
     * @param CampCategory[] $campCategories
     * @param bool $showHiddenCamps
     * @return array
     */
    public function filterOutCampCategoriesWithoutCamps(array $campCategories, bool $showHiddenCamps = false): array;
}