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

        $loadedCampCategory = $campCategoryRepository->find($id);
        $this->assertNotNull($loadedCampCategory);
        $this->assertSame($campCategory->getId(), $loadedCampCategory->getId());

        $campCategoryRepository->removeCampCategory($campCategory, true);
        $loadedCampCategory = $campCategoryRepository->find($id);
        $this->assertNull($loadedCampCategory);
    }

    public function testCreate(): void
    {
        $repository = $this->getCampCategoryRepository();

        $campCategory = $repository->createCampCategory('Camp', 'camp');

        $this->assertSame('Camp', $campCategory->getName());
        $this->assertSame('camp', $campCategory->getUrlName());
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

        $this->assertCount(3, $categoryUrlNames);
        $this->assertContains('category-1', $categoryUrlNames);
        $this->assertContains('category-2', $categoryUrlNames);
        $this->assertContains('category-3', $categoryUrlNames);
    }

    public function testFindRoots(): void
    {
        $repository = $this->getCampCategoryRepository();

        $campCategories = $repository->findRoots();
        $categoryUrlNames = $this->getCampCategoryUrlNames($campCategories);

        $this->assertCount(2, $categoryUrlNames);
        $this->assertContains('category-1', $categoryUrlNames);
        $this->assertContains('category-3', $categoryUrlNames);
    }

    public function testFindPossibleParents(): void
    {
        $repository = $this->getCampCategoryRepository();

        $uid = new UuidV4('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $campCategory = $repository->findOneById($uid);
        $possibleParents = $repository->findPossibleParents($campCategory);
        $categoryUrlNames = $this->getCampCategoryUrlNames($possibleParents);

        $this->assertCount(1, $categoryUrlNames);
        $this->assertContains('category-3', $categoryUrlNames);
    }

    public function testFindByUrlName(): void
    {
        $repository = $this->getCampCategoryRepository();

        $campCategories = $repository->findByUrlName('category-1');
        $categoryUrlNames = $this->getCampCategoryUrlNames($campCategories);

        $this->assertCount(1, $categoryUrlNames);
        $this->assertContains('category-1', $categoryUrlNames);
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