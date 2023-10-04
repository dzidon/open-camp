<?php

namespace App\Tests\Model\Repository;

use App\Library\Data\Admin\TripLocationSearchData;
use App\Library\Enum\Search\Data\Admin\TripLocationSortEnum;
use App\Model\Entity\TripLocation;
use App\Model\Entity\TripLocationPath;
use App\Model\Repository\TripLocationPathRepositoryInterface;
use App\Model\Repository\TripLocationRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class TripLocationRepositoryTest extends KernelTestCase
{
    public function testSaveAndRemove(): void
    {
        $tripLocationPathRepository = $this->getTripLocationPathRepository();
        $tripLocationRepository = $this->getTripLocationRepository();

        $tripLocationPath = new TripLocationPath('Path');
        $tripLocation = new TripLocation('Location', 1000.0, 100, $tripLocationPath);
        $tripLocationPathRepository->saveTripLocationPath($tripLocationPath, false);
        $tripLocationRepository->saveTripLocation($tripLocation, true);
        $id = $tripLocation->getId();

        $loadedTripLocation = $tripLocationRepository->findOneById($id);
        $this->assertNotNull($loadedTripLocation);
        $this->assertSame($id, $loadedTripLocation->getId());

        $tripLocationRepository->removeTripLocation($tripLocation, true);
        $loadedTripLocation = $tripLocationRepository->findOneById($id);
        $this->assertNull($loadedTripLocation);
    }

    public function testFindOneById(): void
    {
        $repository = $this->getTripLocationRepository();

        $uid = new UuidV4('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $tripLocationPath = $repository->findOneById($uid);

        $this->assertSame($uid->toRfc4122(), $tripLocationPath->getId()->toRfc4122());
    }

    public function testFindByName(): void
    {
        $repository = $this->getTripLocationRepository();

        $tripLocations = $repository->findByName('Location 1');
        $names = $this->getTripLocationNames($tripLocations);
        $this->assertSame(['Location 1'], $names);
    }

    public function testFindByTripLocationPath(): void
    {
        $tripLocationPathRepository = $this->getTripLocationPathRepository();
        $tripLocationRepository = $this->getTripLocationRepository();
        $tripLocationPath = $tripLocationPathRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $tripLocations = $tripLocationRepository->findByTripLocationPath($tripLocationPath);
        $names = $this->getTripLocationNames($tripLocations);
        $this->assertSame(['Location 1', 'Location 2'], $names);
    }

    public function testCanRemoveTripLocation(): void
    {
        $repository = $this->getTripLocationRepository();

        $tripLocation = $repository->findOneById(new UuidV4('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));
        $this->assertTrue($repository->canRemoveTripLocation($tripLocation));

        $tripLocation = $repository->findOneById(new UuidV4('550e8400-e29b-41d4-a716-446655440000'));
        $this->assertFalse($repository->canRemoveTripLocation($tripLocation));
    }

    public function testGetAdminPaginator(): void
    {
        $tripLocationPathRepository = $this->getTripLocationPathRepository();
        $tripLocationRepository = $this->getTripLocationRepository();
        $tripLocationPath = $tripLocationPathRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new TripLocationSearchData();
        $paginator = $tripLocationRepository->getAdminPaginator($data, $tripLocationPath, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getTripLocationNames($paginator->getCurrentPageItems());
        $this->assertSame(['Location 2', 'Location 1'], $names);
    }

    public function testGetAdminPaginatorWithName(): void
    {
        $tripLocationPathRepository = $this->getTripLocationPathRepository();
        $tripLocationRepository = $this->getTripLocationRepository();
        $tripLocationPath = $tripLocationPathRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new TripLocationSearchData();
        $data->setPhrase('ocation 2');

        $paginator = $tripLocationRepository->getAdminPaginator($data, $tripLocationPath, 1, 2);
        $this->assertSame(1, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getTripLocationNames($paginator->getCurrentPageItems());
        $this->assertSame(['Location 2'], $names);
    }

    public function testGetAdminPaginatorSortByCreatedAtDesc(): void
    {
        $tripLocationPathRepository = $this->getTripLocationPathRepository();
        $tripLocationRepository = $this->getTripLocationRepository();
        $tripLocationPath = $tripLocationPathRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new TripLocationSearchData();
        $data->setSortBy(TripLocationSortEnum::CREATED_AT_DESC);

        $paginator = $tripLocationRepository->getAdminPaginator($data, $tripLocationPath, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getTripLocationNames($paginator->getCurrentPageItems());
        $this->assertSame(['Location 2', 'Location 1'], $names);
    }

    public function testGetAdminPaginatorSortByCreatedAtAsc(): void
    {
        $tripLocationPathRepository = $this->getTripLocationPathRepository();
        $tripLocationRepository = $this->getTripLocationRepository();
        $tripLocationPath = $tripLocationPathRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new TripLocationSearchData();
        $data->setSortBy(TripLocationSortEnum::CREATED_AT_ASC);

        $paginator = $tripLocationRepository->getAdminPaginator($data, $tripLocationPath, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getTripLocationNames($paginator->getCurrentPageItems());
        $this->assertSame(['Location 1', 'Location 2'], $names);
    }

    public function testGetAdminPaginatorSortByNameDesc(): void
    {
        $tripLocationPathRepository = $this->getTripLocationPathRepository();
        $tripLocationRepository = $this->getTripLocationRepository();
        $tripLocationPath = $tripLocationPathRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new TripLocationSearchData();
        $data->setSortBy(TripLocationSortEnum::NAME_DESC);

        $paginator = $tripLocationRepository->getAdminPaginator($data, $tripLocationPath, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getTripLocationNames($paginator->getCurrentPageItems());
        $this->assertSame(['Location 2', 'Location 1'], $names);
    }

    public function testGetAdminPaginatorSortByNameAsc(): void
    {
        $tripLocationPathRepository = $this->getTripLocationPathRepository();
        $tripLocationRepository = $this->getTripLocationRepository();
        $tripLocationPath = $tripLocationPathRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new TripLocationSearchData();
        $data->setSortBy(TripLocationSortEnum::NAME_ASC);

        $paginator = $tripLocationRepository->getAdminPaginator($data, $tripLocationPath, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getTripLocationNames($paginator->getCurrentPageItems());
        $this->assertSame(['Location 1', 'Location 2'], $names);
    }

    public function testGetAdminPaginatorSortByPriceDesc(): void
    {
        $tripLocationPathRepository = $this->getTripLocationPathRepository();
        $tripLocationRepository = $this->getTripLocationRepository();
        $tripLocationPath = $tripLocationPathRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new TripLocationSearchData();
        $data->setSortBy(TripLocationSortEnum::PRICE_DESC);

        $paginator = $tripLocationRepository->getAdminPaginator($data, $tripLocationPath, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getTripLocationNames($paginator->getCurrentPageItems());
        $this->assertSame(['Location 2', 'Location 1'], $names);
    }

    public function testGetAdminPaginatorSortByPriceAsc(): void
    {
        $tripLocationPathRepository = $this->getTripLocationPathRepository();
        $tripLocationRepository = $this->getTripLocationRepository();
        $tripLocationPath = $tripLocationPathRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new TripLocationSearchData();
        $data->setSortBy(TripLocationSortEnum::PRICE_ASC);

        $paginator = $tripLocationRepository->getAdminPaginator($data, $tripLocationPath, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getTripLocationNames($paginator->getCurrentPageItems());
        $this->assertSame(['Location 1', 'Location 2'], $names);
    }

    public function testGetAdminPaginatorSortByPriorityDesc(): void
    {
        $tripLocationPathRepository = $this->getTripLocationPathRepository();
        $tripLocationRepository = $this->getTripLocationRepository();
        $tripLocationPath = $tripLocationPathRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new TripLocationSearchData();
        $data->setSortBy(TripLocationSortEnum::PRIORITY_DESC);

        $paginator = $tripLocationRepository->getAdminPaginator($data, $tripLocationPath, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getTripLocationNames($paginator->getCurrentPageItems());
        $this->assertSame(['Location 1', 'Location 2'], $names);
    }

    public function testGetAdminPaginatorSortByPriorityAsc(): void
    {
        $tripLocationPathRepository = $this->getTripLocationPathRepository();
        $tripLocationRepository = $this->getTripLocationRepository();
        $tripLocationPath = $tripLocationPathRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new TripLocationSearchData();
        $data->setSortBy(TripLocationSortEnum::PRIORITY_ASC);

        $paginator = $tripLocationRepository->getAdminPaginator($data, $tripLocationPath, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getTripLocationNames($paginator->getCurrentPageItems());
        $this->assertSame(['Location 2', 'Location 1'], $names);
    }

    private function getTripLocationNames(array $tripLocations): array
    {
        $names = [];

        /** @var TripLocation $tripLocation */
        foreach ($tripLocations as $tripLocation)
        {
            $names[] = $tripLocation->getName();
        }

        return $names;
    }

    private function getTripLocationPathRepository(): TripLocationPathRepositoryInterface
    {
        $container = static::getContainer();

        /** @var TripLocationPathRepositoryInterface $repository */
        $repository = $container->get(TripLocationPathRepositoryInterface::class);

        return $repository;
    }

    private function getTripLocationRepository(): TripLocationRepository
    {
        $container = static::getContainer();

        /** @var TripLocationRepository $repository */
        $repository = $container->get(TripLocationRepository::class);

        return $repository;
    }
}