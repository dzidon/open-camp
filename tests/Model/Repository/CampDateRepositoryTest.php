<?php

namespace App\Tests\Model\Repository;

use App\Library\Data\Admin\CampDateSearchData;
use App\Library\Enum\Search\Data\Admin\CampDateSortEnum;
use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use App\Model\Repository\CampDateRepository;
use App\Model\Repository\CampRepositoryInterface;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class CampDateRepositoryTest extends KernelTestCase
{
    public function testSaveAndRemove(): void
    {
        $campDateRepository = $this->getCampDateRepository();
        $campRepository = $this->getCampRepository();

        $camp = new Camp('Camp', 'camp', 5, 10, 321);
        $campDate = new CampDate(new DateTimeImmutable('2000-07-01'), new DateTimeImmutable('2000-07-07'), 1000, 100.0, 10, $camp);
        $campRepository->saveCamp($camp, false);
        $campDateRepository->saveCampDate($campDate, true);
        $id = $campDate->getId();

        $loadedCampDate = $campDateRepository->findOneById($id);
        $this->assertNotNull($loadedCampDate);
        $this->assertSame($id, $loadedCampDate->getId());

        $campDateRepository->removeCampDate($campDate, true);
        $loadedCampDate = $campDateRepository->findOneById($id);
        $this->assertNull($loadedCampDate);
    }

    public function testFindOneById(): void
    {
        $campDateRepository = $this->getCampDateRepository();

        $uid = new UuidV4('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $campCategory = $campDateRepository->findOneById($uid);

        $this->assertSame($uid->toRfc4122(), $campCategory->getId()->toRfc4122());
    }

    public function testFindUpcomingByCamp(): void
    {
        $campDateRepository = $this->getCampDateRepository();
        $campRepository = $this->getCampRepository();
        $camp = $campRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $result = $campDateRepository->findUpcomingByCamp($camp);
        $campDates = $result->getCampDates();
        $this->assertSame(['550e8400-e29b-41d4-a716-446655440000'], $this->getCampDateIdStrings($campDates));
    }

    public function testFindThoseThatCollideWithIntervalCampOverlapLeft(): void
    {
        $campDateRepository = $this->getCampDateRepository();
        $campRepository = $this->getCampRepository();
        $camp = $campRepository->findOneById(UuidV4::fromString('a08f6f48-3a52-40db-b031-5eb3a468c57a'));

        $campDates = $campDateRepository->findThoseThatCollideWithInterval($camp, new DateTimeImmutable('4000-01-03'), new DateTimeImmutable('4000-01-07'));
        $this->assertSame(['c097941e-52c4-405a-9823-49b7b71ead6e'], $this->getCampDateIdStrings($campDates));
    }

    public function testFindThoseThatCollideWithIntervalDifferentCampOverlapLeft(): void
    {
        $campDateRepository = $this->getCampDateRepository();
        $campRepository = $this->getCampRepository();
        $camp = $campRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $campDates = $campDateRepository->findThoseThatCollideWithInterval($camp, new DateTimeImmutable('4000-01-03'), new DateTimeImmutable('4000-01-07'));
        $this->assertEmpty($this->getCampDateIdStrings($campDates));
    }

    public function testFindThoseThatCollideWithIntervalNullCampOverlapLeft(): void
    {
        $campDateRepository = $this->getCampDateRepository();

        $campDates = $campDateRepository->findThoseThatCollideWithInterval(null, new DateTimeImmutable('4000-01-03'), new DateTimeImmutable('4000-01-07'));
        $this->assertSame(['c097941e-52c4-405a-9823-49b7b71ead6e'], $this->getCampDateIdStrings($campDates));
    }

    public function testFindThoseThatCollideWithIntervalNullCampOverlapRight(): void
    {
        $campDateRepository = $this->getCampDateRepository();

        $campDates = $campDateRepository->findThoseThatCollideWithInterval(null, new DateTimeImmutable('4000-01-07'), new DateTimeImmutable('4000-01-15'));
        $this->assertSame(['c097941e-52c4-405a-9823-49b7b71ead6e'], $this->getCampDateIdStrings($campDates));
    }

    public function testFindThoseThatCollideWithIntervalNullCampInnerOverlapFirst(): void
    {
        $campDateRepository = $this->getCampDateRepository();

        $campDates = $campDateRepository->findThoseThatCollideWithInterval(null, new DateTimeImmutable('4000-01-04'), new DateTimeImmutable('4000-01-11'));
        $this->assertSame(['c097941e-52c4-405a-9823-49b7b71ead6e'], $this->getCampDateIdStrings($campDates));
    }

    public function testFindThoseThatCollideWithIntervalNullCampInnerOverlapSecond(): void
    {
        $campDateRepository = $this->getCampDateRepository();

        $campDates = $campDateRepository->findThoseThatCollideWithInterval(null, new DateTimeImmutable('4000-01-06'), new DateTimeImmutable('4000-01-09'));
        $this->assertSame(['c097941e-52c4-405a-9823-49b7b71ead6e'], $this->getCampDateIdStrings($campDates));
    }

    public function testFindThoseThatCollideWithIntervalNullCampStartEndOverlapLeft(): void
    {
        $campDateRepository = $this->getCampDateRepository();

        $campDates = $campDateRepository->findThoseThatCollideWithInterval(null, new DateTimeImmutable('4000-01-03'), new DateTimeImmutable('4000-01-05'));
        $this->assertSame(['c097941e-52c4-405a-9823-49b7b71ead6e'], $this->getCampDateIdStrings($campDates));
    }

    public function testFindThoseThatCollideWithIntervalNullCampStartEndOverlapRight(): void
    {
        $campDateRepository = $this->getCampDateRepository();

        $campDates = $campDateRepository->findThoseThatCollideWithInterval(null, new DateTimeImmutable('4000-01-10'), new DateTimeImmutable('4000-01-13'));
        $this->assertSame(['c097941e-52c4-405a-9823-49b7b71ead6e'], $this->getCampDateIdStrings($campDates));
    }

    public function testFindThoseThatCollideWithIntervalNullCampExactOverlap(): void
    {
        $campDateRepository = $this->getCampDateRepository();

        $campDates = $campDateRepository->findThoseThatCollideWithInterval(null, new DateTimeImmutable('4000-01-05'), new DateTimeImmutable('4000-01-10'));
        $this->assertSame(['c097941e-52c4-405a-9823-49b7b71ead6e'], $this->getCampDateIdStrings($campDates));
    }

    public function testFindThoseThatCollideWithIntervalNullCampNoOverlap(): void
    {
        $campDateRepository = $this->getCampDateRepository();

        $campDates = $campDateRepository->findThoseThatCollideWithInterval(null, new DateTimeImmutable('4000-01-15'), new DateTimeImmutable('4000-01-20'));
        $this->assertEmpty($this->getCampDateIdStrings($campDates));
    }

    public function testGetAdminPaginator(): void
    {
        $campDateRepository = $this->getCampDateRepository();
        $campRepository = $this->getCampRepository();

        /** @var Camp $camp */
        $camp = $campRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new CampDateSearchData();
        $paginator = $campDateRepository->getAdminPaginator($data, $camp, 1, 2);
        $this->assertSame(1, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $idStrings = $this->getCampDateIdStrings($paginator->getCurrentPageItems());
        $this->assertSame(['550e8400-e29b-41d4-a716-446655440000'], $idStrings);
    }

    public function testGetAdminPaginatorWithCurrentAndUpcoming(): void
    {
        $campDateRepository = $this->getCampDateRepository();
        $campRepository = $this->getCampRepository();

        /** @var Camp $camp */
        $camp = $campRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new CampDateSearchData();
        $data->setIsHistorical(false);
        $paginator = $campDateRepository->getAdminPaginator($data, $camp, 1, 2);
        $this->assertSame(1, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $idStrings = $this->getCampDateIdStrings($paginator->getCurrentPageItems());
        $this->assertSame(['550e8400-e29b-41d4-a716-446655440000'], $idStrings);
    }

    public function testGetAdminPaginatorWithHistorical(): void
    {
        $campDateRepository = $this->getCampDateRepository();
        $campRepository = $this->getCampRepository();

        /** @var Camp $camp */
        $camp = $campRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new CampDateSearchData();
        $data->setIsHistorical(true);
        $paginator = $campDateRepository->getAdminPaginator($data, $camp, 1, 2);
        $this->assertSame(1, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $idStrings = $this->getCampDateIdStrings($paginator->getCurrentPageItems());
        $this->assertSame(['e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'], $idStrings);
    }

    public function testGetAdminPaginatorWithHistoricalNull(): void
    {
        $campDateRepository = $this->getCampDateRepository();
        $campRepository = $this->getCampRepository();

        /** @var Camp $camp */
        $camp = $campRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new CampDateSearchData();
        $data->setIsHistorical(null);
        $paginator = $campDateRepository->getAdminPaginator($data, $camp, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $idStrings = $this->getCampDateIdStrings($paginator->getCurrentPageItems());
        $this->assertSame(['e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b', '550e8400-e29b-41d4-a716-446655440000'], $idStrings);
    }

    public function testGetAdminPaginatorWithFrom(): void
    {
        $campDateRepository = $this->getCampDateRepository();
        $campRepository = $this->getCampRepository();

        /** @var Camp $camp */
        $camp = $campRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new CampDateSearchData();
        $data->setFrom(new DateTimeImmutable('3000-07-08'));
        $data->setIsHistorical(null);
        $paginator = $campDateRepository->getAdminPaginator($data, $camp, 1, 2);
        $this->assertSame(1, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $idStrings = $this->getCampDateIdStrings($paginator->getCurrentPageItems());
        $this->assertSame(['550e8400-e29b-41d4-a716-446655440000'], $idStrings);
    }

    public function testGetAdminPaginatorWithTo(): void
    {
        $campDateRepository = $this->getCampDateRepository();
        $campRepository = $this->getCampRepository();

        /** @var Camp $camp */
        $camp = $campRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new CampDateSearchData();
        $data->setTo(new DateTimeImmutable('2000-07-07'));
        $data->setIsHistorical(null);
        $paginator = $campDateRepository->getAdminPaginator($data, $camp, 1, 2);
        $this->assertSame(1, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $idStrings = $this->getCampDateIdStrings($paginator->getCurrentPageItems());
        $this->assertSame(['e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'], $idStrings);
    }

    public function testGetAdminPaginatorSortByStartAtAsc(): void
    {
        $campDateRepository = $this->getCampDateRepository();
        $campRepository = $this->getCampRepository();

        /** @var Camp $camp */
        $camp = $campRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new CampDateSearchData();
        $data->setSortBy(CampDateSortEnum::START_AT_ASC);
        $data->setIsHistorical(null);
        $paginator = $campDateRepository->getAdminPaginator($data, $camp, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $idStrings = $this->getCampDateIdStrings($paginator->getCurrentPageItems());
        $this->assertSame(['e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b', '550e8400-e29b-41d4-a716-446655440000'], $idStrings);
    }

    public function testGetAdminPaginatorSortByStartAtDesc(): void
    {
        $campDateRepository = $this->getCampDateRepository();
        $campRepository = $this->getCampRepository();

        /** @var Camp $camp */
        $camp = $campRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new CampDateSearchData();
        $data->setSortBy(CampDateSortEnum::START_AT_DESC);
        $data->setIsHistorical(null);
        $paginator = $campDateRepository->getAdminPaginator($data, $camp, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $idStrings = $this->getCampDateIdStrings($paginator->getCurrentPageItems());
        $this->assertSame(['550e8400-e29b-41d4-a716-446655440000', 'e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'], $idStrings);
    }

    public function testGetAdminPaginatorSortByDepositAsc(): void
    {
        $campDateRepository = $this->getCampDateRepository();
        $campRepository = $this->getCampRepository();

        /** @var Camp $camp */
        $camp = $campRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new CampDateSearchData();
        $data->setSortBy(CampDateSortEnum::DEPOSIT_ASC);
        $data->setIsHistorical(null);
        $paginator = $campDateRepository->getAdminPaginator($data, $camp, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $idStrings = $this->getCampDateIdStrings($paginator->getCurrentPageItems());
        $this->assertSame(['e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b', '550e8400-e29b-41d4-a716-446655440000'], $idStrings);
    }

    public function testGetAdminPaginatorSortByDepositDesc(): void
    {
        $campDateRepository = $this->getCampDateRepository();
        $campRepository = $this->getCampRepository();

        /** @var Camp $camp */
        $camp = $campRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new CampDateSearchData();
        $data->setSortBy(CampDateSortEnum::DEPOSIT_DESC);
        $data->setIsHistorical(null);
        $paginator = $campDateRepository->getAdminPaginator($data, $camp, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $idStrings = $this->getCampDateIdStrings($paginator->getCurrentPageItems());
        $this->assertSame(['550e8400-e29b-41d4-a716-446655440000', 'e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'], $idStrings);
    }

    public function testGetAdminPaginatorSortByPriceWithoutDepositAsc(): void
    {
        $campDateRepository = $this->getCampDateRepository();
        $campRepository = $this->getCampRepository();

        /** @var Camp $camp */
        $camp = $campRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new CampDateSearchData();
        $data->setSortBy(CampDateSortEnum::PRICE_WITHOUT_DEPOSIT_ASC);
        $data->setIsHistorical(null);
        $paginator = $campDateRepository->getAdminPaginator($data, $camp, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $idStrings = $this->getCampDateIdStrings($paginator->getCurrentPageItems());
        $this->assertSame(['e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b', '550e8400-e29b-41d4-a716-446655440000'], $idStrings);
    }

    public function testGetAdminPaginatorSortByPriceWithoutDepositDesc(): void
    {
        $campDateRepository = $this->getCampDateRepository();
        $campRepository = $this->getCampRepository();

        /** @var Camp $camp */
        $camp = $campRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new CampDateSearchData();
        $data->setSortBy(CampDateSortEnum::PRICE_WITHOUT_DEPOSIT_DESC);
        $data->setIsHistorical(null);
        $paginator = $campDateRepository->getAdminPaginator($data, $camp, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $idStrings = $this->getCampDateIdStrings($paginator->getCurrentPageItems());
        $this->assertSame(['550e8400-e29b-41d4-a716-446655440000', 'e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'], $idStrings);
    }

    public function testGetAdminPaginatorSortByCapacityAsc(): void
    {
        $campDateRepository = $this->getCampDateRepository();
        $campRepository = $this->getCampRepository();

        /** @var Camp $camp */
        $camp = $campRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new CampDateSearchData();
        $data->setSortBy(CampDateSortEnum::CAPACITY_ASC);
        $data->setIsHistorical(null);
        $paginator = $campDateRepository->getAdminPaginator($data, $camp, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $idStrings = $this->getCampDateIdStrings($paginator->getCurrentPageItems());
        $this->assertSame(['e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b', '550e8400-e29b-41d4-a716-446655440000'], $idStrings);
    }

    public function testGetAdminPaginatorSortByCapacityDesc(): void
    {
        $campDateRepository = $this->getCampDateRepository();
        $campRepository = $this->getCampRepository();

        /** @var Camp $camp */
        $camp = $campRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new CampDateSearchData();
        $data->setSortBy(CampDateSortEnum::CAPACITY_DESC);
        $data->setIsHistorical(null);
        $paginator = $campDateRepository->getAdminPaginator($data, $camp, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $idStrings = $this->getCampDateIdStrings($paginator->getCurrentPageItems());
        $this->assertSame(['550e8400-e29b-41d4-a716-446655440000', 'e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'], $idStrings);
    }

    private function getCampDateIdStrings(array $campDates): array
    {
        $idStrings = [];

        /** @var CampDate $campDate */
        foreach ($campDates as $campDate)
        {
            $idStrings[] = $campDate
                ->getId()
                ->toRfc4122()
            ;
        }

        return $idStrings;
    }

    private function getCampRepository(): CampRepositoryInterface
    {
        $container = static::getContainer();

        /** @var CampRepositoryInterface $repository */
        $repository = $container->get(CampRepositoryInterface::class);

        return $repository;
    }

    private function getCampDateRepository(): CampDateRepository
    {
        $container = static::getContainer();

        /** @var CampDateRepository $repository */
        $repository = $container->get(CampDateRepository::class);

        return $repository;
    }
}