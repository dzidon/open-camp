<?php

namespace App\Tests\Model\Repository;

use App\Model\Entity\CampCategory;
use App\Model\Repository\CampCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class CampCategoryRepositoryTest extends KernelTestCase
{
    public function testSaveAndRemove(): void
    {
        $campCategoryRepository = $this->getCampCategoryRepository();

        $campCategory = new CampCategory('Category', 'category');
        $campCategoryRepository->saveCampCategory($campCategory, true);
        $id = $campCategory->getId();

        $loadedCampCategory = $campCategoryRepository->findOneById($id);
        $this->assertNotNull($loadedCampCategory);
        $this->assertSame($id, $loadedCampCategory->getId());

        $campCategoryRepository->removeCampCategory($campCategory, true);
        $loadedCampCategory = $campCategoryRepository->findOneById($id);
        $this->assertNull($loadedCampCategory);
    }

    public function testFindOneById(): void
    {
        $repository = $this->getCampCategoryRepository();

        $uid = new UuidV4('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $campCategory = $repository->findOneById($uid);

        $this->assertSame($uid->toRfc4122(), $campCategory->getId()->toRfc4122());
    }

    public function testFindAll(): void
    {
        $repository = $this->getCampCategoryRepository();

        $campCategories = $repository->findAll();
        $categoryUrlNames = $this->getCampCategoryUrlNames($campCategories);

        $this->assertSame(['category-1', 'category-2', 'category-3', 'category-4'], $categoryUrlNames);
    }

    public function testFindRoots(): void
    {
        $repository = $this->getCampCategoryRepository();

        $campCategories = $repository->findRoots();
        $categoryUrlNames = $this->getCampCategoryUrlNames($campCategories);

        $this->assertSame(['category-1', 'category-3'], $categoryUrlNames);
    }

    public function testFindOneByPath(): void
    {
        $repository = $this->getCampCategoryRepository();

        $this->assertNull($repository->findOneByPath(''));
        $this->assertNull($repository->findOneByPath('abc'));
        $this->assertNull($repository->findOneByPath('category-2'));

        $campCategory = $repository->findOneByPath('category-1');
        $this->assertSame('category-1', $campCategory->getUrlName());

        $campCategory = $repository->findOneByPath('category-1/category-2');
        $this->assertSame('category-2', $campCategory->getUrlName());

        $campCategory = $repository->findOneByPath('/category-1/category-2/');
        $this->assertSame('category-2', $campCategory->getUrlName());
    }

    public function testFindPossibleParents(): void
    {
        $repository = $this->getCampCategoryRepository();

        $uid = new UuidV4('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $campCategory = $repository->findOneById($uid);
        $possibleParents = $repository->findPossibleParents($campCategory);
        $categoryUrlNames = $this->getCampCategoryUrlNames($possibleParents);

        $this->assertSame(['category-3', 'category-4'], $categoryUrlNames);
    }

    public function testFindByUrlName(): void
    {
        $repository = $this->getCampCategoryRepository();

        $campCategories = $repository->findByUrlName('category-1');
        $categoryUrlNames = $this->getCampCategoryUrlNames($campCategories);

        $this->assertSame(['category-1'], $categoryUrlNames);
    }

    public function testCampCategoryHasCamp(): void
    {
        $repository = $this->getCampCategoryRepository();

        $uid = new UuidV4('a08f6f48-3a52-40db-b031-5eb3a468c57a');
        $campCategory3 = $repository->findOneById($uid);
        $this->assertFalse($repository->campCategoryHasCamp($campCategory3, false));
        $this->assertTrue($repository->campCategoryHasCamp($campCategory3));

        $uid = new UuidV4('550e8400-e29b-41d4-a716-446655440000');
        $campCategory4 = $repository->findOneById($uid);
        $this->assertFalse($repository->campCategoryHasCamp($campCategory4, false));
        $this->assertTrue($repository->campCategoryHasCamp($campCategory4));
    }

    public function testFilterOutCampCategoriesWithoutCamps(): void
    {
        $repository = $this->getCampCategoryRepository();

        $uid = new UuidV4('a08f6f48-3a52-40db-b031-5eb3a468c57a');
        $campCategory3 = $repository->findOneById($uid);
        $filteredCampCategories = $repository->filterOutCampCategoriesWithoutCamps([$campCategory3], false);
        $categoryUrlNames = $this->getCampCategoryUrlNames($filteredCampCategories);
        $this->assertEmpty($categoryUrlNames);

        $filteredCampCategories = $repository->filterOutCampCategoriesWithoutCamps([$campCategory3]);
        $categoryUrlNames = $this->getCampCategoryUrlNames($filteredCampCategories);
        $this->assertSame(['category-3'], $categoryUrlNames);

        $uid = new UuidV4('550e8400-e29b-41d4-a716-446655440000');
        $campCategory4 = $repository->findOneById($uid);
        $filteredCampCategories = $repository->filterOutCampCategoriesWithoutCamps([$campCategory4], false);
        $categoryUrlNames = $this->getCampCategoryUrlNames($filteredCampCategories);
        $this->assertEmpty($categoryUrlNames);

        $filteredCampCategories = $repository->filterOutCampCategoriesWithoutCamps([$campCategory4]);
        $categoryUrlNames = $this->getCampCategoryUrlNames($filteredCampCategories);
        $this->assertSame(['category-4'], $categoryUrlNames);
    }

    private function getCampCategoryUrlNames(array $campCategories): array
    {
        $urlNames = [];

        /** @var CampCategory $campCategory */
        foreach ($campCategories as $campCategory)
        {
            $urlNames[] = $campCategory->getUrlName();
        }

        return $urlNames;
    }

    private function getCampCategoryRepository(): CampCategoryRepository
    {
        $container = static::getContainer();

        /** @var CampCategoryRepository $repository */
        $repository = $container->get(CampCategoryRepository::class);

        return $repository;
    }
}