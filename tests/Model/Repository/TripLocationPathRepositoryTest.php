<?php

namespace App\Tests\Model\Repository;

use App\Library\Data\Admin\TripLocationPathSearchData;
use App\Library\Enum\Search\Data\Admin\TripLocationPathSortEnum;
use App\Model\Entity\TripLocationPath;
use App\Model\Repository\TripLocationPathRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class TripLocationPathRepositoryTest extends KernelTestCase
{
    public function testSaveAndRemove(): void
    {
        $repository = $this->getTripLocationPathRepository();

        $tripLocationPath = new TripLocationPath('Name');
        $repository->saveTripLocationPath($tripLocationPath, true);
        $id = $tripLocationPath->getId();

        $loadedTripLocationPath = $repository->find($id);
        $this->assertNotNull($loadedTripLocationPath);
        $this->assertSame($tripLocationPath->getId(), $loadedTripLocationPath->getId());

        $repository->removeTripLocationPath($tripLocationPath, true);
        $loadedTripLocationPath = $repository->find($id);
        $this->assertNull($loadedTripLocationPath);
    }

    public function testCreate(): void
    {
        $repository = $this->getTripLocationPathRepository();
        $role = $repository->createTripLocationPath('Path');

        $this->assertSame('Path', $role->getName());
    }

    public function testFindOneById(): void
    {
        $repository = $this->getTripLocationPathRepository();

        $uid = new UuidV4('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $tripLocationPath = $repository->findOneById($uid);

        $this->assertSame($uid->toRfc4122(), $tripLocationPath->getId()->toRfc4122());
    }

    public function testFindOneByName(): void
    {
        $repository = $this->getTripLocationPathRepository();

        $tripLocationPath = $repository->findOneByName('Path 1');
        $this->assertSame('Path 1', $tripLocationPath->getName());
    }

    public function testGetAdminPaginator(): void
    {
        $repository = $this->getTripLocationPathRepository();

        $data = new TripLocationPathSearchData();
        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getTripLocationPathNames($paginator->getCurrentPageItems());
        $this->assertSame(['Path 2', 'Path 1'], $names);
    }

    public function testGetAdminPaginatorWithName(): void
    {
        $repository = $this->getTripLocationPathRepository();

        $data = new TripLocationPathSearchData();
        $data->setPhrase('ath 2');

        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(1, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getTripLocationPathNames($paginator->getCurrentPageItems());
        $this->assertSame(['Path 2'], $names);
    }

    public function testGetAdminPaginatorSortByCreatedAtDesc(): void
    {
        $repository = $this->getTripLocationPathRepository();

        $data = new TripLocationPathSearchData();
        $data->setSortBy(TripLocationPathSortEnum::CREATED_AT_DESC);

        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getTripLocationPathNames($paginator->getCurrentPageItems());
        $this->assertSame(['Path 2', 'Path 1'], $names);
    }

    public function testGetAdminPaginatorSortByCreatedAtAsc(): void
    {
        $repository = $this->getTripLocationPathRepository();

        $data = new TripLocationPathSearchData();
        $data->setSortBy(TripLocationPathSortEnum::CREATED_AT_ASC);

        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getTripLocationPathNames($paginator->getCurrentPageItems());
        $this->assertSame(['Path 1', 'Path 2'], $names);
    }

    public function testGetAdminPaginatorSortByNameDesc(): void
    {
        $repository = $this->getTripLocationPathRepository();

        $data = new TripLocationPathSearchData();
        $data->setSortBy(TripLocationPathSortEnum::NAME_DESC);

        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getTripLocationPathNames($paginator->getCurrentPageItems());
        $this->assertSame(['Path 2', 'Path 1'], $names);
    }

    public function testGetAdminPaginatorSortByNameAsc(): void
    {
        $repository = $this->getTripLocationPathRepository();

        $data = new TripLocationPathSearchData();
        $data->setSortBy(TripLocationPathSortEnum::NAME_ASC);

        $paginator = $repository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getTripLocationPathNames($paginator->getCurrentPageItems());
        $this->assertSame(['Path 1', 'Path 2'], $names);
    }

    private function getTripLocationPathNames(array $tripLocationPaths): array
    {
        $names = [];

        /** @var TripLocationPath $tripLocationPath */
        foreach ($tripLocationPaths as $tripLocationPath)
        {
            $names[] = $tripLocationPath->getName();
        }

        return $names;
    }

    private function getTripLocationPathRepository(): TripLocationPathRepository
    {
        $container = static::getContainer();

        /** @var TripLocationPathRepository $repository */
        $repository = $container->get(TripLocationPathRepository::class);

        return $repository;
    }
}