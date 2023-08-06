<?php

namespace App\Tests\Model\Repository;

use App\Library\Data\Admin\CampSearchData;
use App\Library\Enum\Search\Data\Admin\CampSortEnum;
use App\Model\Entity\Camp;
use App\Model\Repository\CampCategoryRepositoryInterface;
use App\Model\Repository\CampRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class CampRepositoryTest extends KernelTestCase
{
    public function testSaveAndRemove(): void
    {
        $campRepository = $this->getCampRepository();

        $camp = new Camp('Camp', 'camp', 5, 10);
        $campRepository->saveCamp($camp, true);
        $id = $camp->getId();

        $loadedCamp = $campRepository->find($id);
        $this->assertNotNull($loadedCamp);
        $this->assertSame($camp->getId(), $loadedCamp->getId());

        $campRepository->removeCamp($camp, true);
        $loadedCamp = $campRepository->find($id);
        $this->assertNull($loadedCamp);
    }

    public function testCreate(): void
    {
        $campRepository = $this->getCampRepository();

        $camp = $campRepository->createCamp('Camp', 'camp', 5, 10);

        $this->assertSame('Camp', $camp->getName());
        $this->assertSame('camp', $camp->getUrlName());
        $this->assertSame(5, $camp->getAgeMin());
        $this->assertSame(10, $camp->getAgeMax());
    }

    public function testFindOneById(): void
    {
        $repository = $this->getCampRepository();

        $uid = new UuidV4('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $camp = $repository->findOneById($uid);

        $this->assertSame($uid->toRfc4122(), $camp->getId()->toRfc4122());
    }

    public function testFindOneByUrlName(): void
    {
        $repository = $this->getCampRepository();

        $loadedCamp = $repository->findOneByUrlName('nonexistent-camp');
        $this->assertNull($loadedCamp);

        $loadedCamp = $repository->findOneByUrlName('camp-1');
        $this->assertNotNull($loadedCamp);
        $this->assertSame('camp-1', $loadedCamp->getUrlName());
    }

    public function testGetAdminPaginator(): void
    {
        $repository = $this->getCampRepository();

        $data = new CampSearchData();
        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $urlNames = $this->getCampUrlNames($paginator->getCurrentPageItems());
        $this->assertSame(['camp-2', 'camp-1'], $urlNames);
    }

    public function testGetAdminPaginatorWithName(): void
    {
        $repository = $this->getCampRepository();

        $data = new CampSearchData();
        $data->setPhrase('amp 1');
        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(1, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $urlNames = $this->getCampUrlNames($paginator->getCurrentPageItems());
        $this->assertSame(['camp-1'], $urlNames);
    }

    public function testGetAdminPaginatorWithUrlName(): void
    {
        $repository = $this->getCampRepository();

        $data = new CampSearchData();
        $data->setPhrase('amp-1');
        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(1, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $urlNames = $this->getCampUrlNames($paginator->getCurrentPageItems());
        $this->assertSame(['camp-1'], $urlNames);
    }

    public function testGetAdminPaginatorSortByCreatedAtDesc(): void
    {
        $repository = $this->getCampRepository();

        $data = new CampSearchData();
        $data->setSortBy(CampSortEnum::CREATED_AT_DESC);
        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $urlNames = $this->getCampUrlNames($paginator->getCurrentPageItems());
        $this->assertSame(['camp-2', 'camp-1'], $urlNames);
    }

    public function testGetAdminPaginatorSortByCreatedAtAsc(): void
    {
        $repository = $this->getCampRepository();

        $data = new CampSearchData();
        $data->setSortBy(CampSortEnum::CREATED_AT_ASC);
        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $urlNames = $this->getCampUrlNames($paginator->getCurrentPageItems());
        $this->assertSame(['camp-1', 'camp-2'], $urlNames);
    }

    public function testGetAdminPaginatorSortByNameDesc(): void
    {
        $repository = $this->getCampRepository();

        $data = new CampSearchData();
        $data->setSortBy(CampSortEnum::NAME_DESC);
        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $urlNames = $this->getCampUrlNames($paginator->getCurrentPageItems());
        $this->assertSame(['camp-2', 'camp-1'], $urlNames);
    }

    public function testGetAdminPaginatorSortByNameAsc(): void
    {
        $repository = $this->getCampRepository();

        $data = new CampSearchData();
        $data->setSortBy(CampSortEnum::NAME_ASC);
        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $urlNames = $this->getCampUrlNames($paginator->getCurrentPageItems());
        $this->assertSame(['camp-1', 'camp-2'], $urlNames);
    }

    public function testGetAdminPaginatorSortByUrlNameDesc(): void
    {
        $repository = $this->getCampRepository();

        $data = new CampSearchData();
        $data->setSortBy(CampSortEnum::URL_NAME_DESC);
        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $urlNames = $this->getCampUrlNames($paginator->getCurrentPageItems());
        $this->assertSame(['camp-2', 'camp-1'], $urlNames);
    }

    public function testGetAdminPaginatorSortByUrlNameAsc(): void
    {
        $repository = $this->getCampRepository();

        $data = new CampSearchData();
        $data->setSortBy(CampSortEnum::URL_NAME_ASC);
        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $urlNames = $this->getCampUrlNames($paginator->getCurrentPageItems());
        $this->assertSame(['camp-1', 'camp-2'], $urlNames);
    }

    public function testGetAdminPaginatorWithAge(): void
    {
        $repository = $this->getCampRepository();

        $data = new CampSearchData();
        $data->setAge(8);
        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(1, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $urlNames = $this->getCampUrlNames($paginator->getCurrentPageItems());
        $this->assertSame(['camp-1'], $urlNames);
    }

    public function testGetAdminPaginatorWithCampCategory(): void
    {
        $campRepository = $this->getCampRepository();
        $campCategoryRepository = $this->getCampCategoryRepository();

        $id = UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $campCategory = $campCategoryRepository->findOneById($id);

        $data = new CampSearchData();
        $data->setCampCategory($campCategory);
        $paginator = $campRepository->getAdminPaginator($data, 1, 2);
        $this->assertSame(1, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $urlNames = $this->getCampUrlNames($paginator->getCurrentPageItems());
        $this->assertSame(['camp-1'], $urlNames);
    }

    private function getCampUrlNames(array $camps): array
    {
        $urlNames = [];

        /** @var Camp $camp */
        foreach ($camps as $camp)
        {
            $urlNames[] = $camp->getUrlName();
        }

        return $urlNames;
    }

    private function getCampCategoryRepository(): CampCategoryRepositoryInterface
    {
        $container = static::getContainer();

        /** @var CampCategoryRepositoryInterface $repository */
        $repository = $container->get(CampCategoryRepositoryInterface::class);

        return $repository;
    }

    private function getCampRepository(): CampRepository
    {
        $container = static::getContainer();

        /** @var CampRepository $repository */
        $repository = $container->get(CampRepository::class);

        return $repository;
    }
}