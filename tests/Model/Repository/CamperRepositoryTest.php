<?php

namespace App\Tests\Model\Repository;

use App\Enum\GenderEnum;
use App\Enum\Search\Data\User\CamperSortEnum;
use App\Form\DataTransfer\Data\User\CamperSearchData;
use App\Model\Entity\Camper;
use App\Model\Entity\User;
use App\Model\Repository\CamperRepository;
use App\Model\Repository\UserRepositoryInterface;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

/**
 * Tests the Camper repository.
 */
class CamperRepositoryTest extends KernelTestCase
{
    public function testSaveAndRemove(): void
    {
        $camperRepository = $this->getCamperRepository();
        $userRepository = $this->getUserRepository();

        $user = new User('bob@bing.com');
        $camper = new Camper('Bob', 'Bobby', GenderEnum::MALE, new DateTimeImmutable('now'), $user);

        $userRepository->saveUser($user, false);
        $camperRepository->saveCamper($camper, true);
        $id = $camper->getId();

        $loadedCamper = $camperRepository->find($id);
        $this->assertNotNull($loadedCamper);
        $this->assertSame($camper->getId(), $loadedCamper->getId());

        $camperRepository->removeCamper($camper, true);
        $loadedCamper = $camperRepository->find($id);
        $this->assertNull($loadedCamper);
    }

    public function testCreate(): void
    {
        $repository = $this->getCamperRepository();
        $bornAtDate = new DateTimeImmutable('2000-01-01');
        $user = new User('bob@bing.com');
        $camper = $repository->createCamper('Bob', 'Bobby', GenderEnum::FEMALE, $bornAtDate, $user);

        $this->assertSame('Bob', $camper->getNameFirst());
        $this->assertSame('Bobby', $camper->getNameLast());
        $this->assertSame(GenderEnum::FEMALE, $camper->getGender());
        $this->assertSame($bornAtDate, $camper->getBornAt());
        $this->assertSame($user, $camper->getUser());
    }

    public function testFindOneById(): void
    {
        $repository = $this->getCamperRepository();

        $uid = new UuidV4('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $camper = $repository->findOneById($uid);

        $this->assertSame($uid->toRfc4122(), $camper->getId()->toRfc4122());
    }

    public function testFindByUser(): void
    {
        $camperRepository = $this->getCamperRepository();
        $userRepository = $this->getUserRepository();

        $user = $userRepository->findOneByEmail('david@gmail.com');
        $campers = $camperRepository->findByUser($user);
        $names = $this->getCamperNames($campers);

        $this->assertCount(2, $names);
        $this->assertContains('Camper 1', $names);
        $this->assertContains('Camper 2', $names);
    }

    public function testFindOwnedBySameUser(): void
    {
        $camperRepository = $this->getCamperRepository();
        $userRepository = $this->getUserRepository();

        $user = $userRepository->findOneByEmail('david@gmail.com');
        $camper = new Camper('Camper', '3', GenderEnum::MALE, new DateTimeImmutable(), $user);
        $camperRepository->saveCamper($camper, true);

        $campers = $camperRepository->findOwnedBySameUser($camper);
        $names = $this->getCamperNames($campers);

        $this->assertCount(2, $names);
        $this->assertContains('Camper 1', $names);
        $this->assertContains('Camper 2', $names);
    }

    public function testGetUserPaginator(): void
    {
        $camperRepository = $this->getCamperRepository();
        $userRepository = $this->getUserRepository();
        $user = $userRepository->findOneByEmail('david@gmail.com');

        $data = new CamperSearchData();
        $paginator = $camperRepository->getUserPaginator($data, $user, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getCamperNames($paginator->getCurrentPageItems());
        $this->assertSame(['Camper 2', 'Camper 1'], $names);
    }

    public function testGetUserPaginatorWithPhrase(): void
    {
        $camperRepository = $this->getCamperRepository();
        $userRepository = $this->getUserRepository();
        $user = $userRepository->findOneByEmail('david@gmail.com');

        $data = new CamperSearchData();
        $data->setPhrase('er 1');
        $paginator = $camperRepository->getUserPaginator($data, $user, 1, 2);
        $this->assertSame(1, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getCamperNames($paginator->getCurrentPageItems());
        $this->assertSame(['Camper 1'], $names);
    }

    public function testGetUserPaginatorSortByCreatedAtDesc(): void
    {
        $camperRepository = $this->getCamperRepository();
        $userRepository = $this->getUserRepository();
        $user = $userRepository->findOneByEmail('david@gmail.com');

        $data = new CamperSearchData();
        $data->setSortBy(CamperSortEnum::CREATED_AT_DESC);

        $paginator = $camperRepository->getUserPaginator($data, $user, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getCamperNames($paginator->getCurrentPageItems());
        $this->assertSame(['Camper 2', 'Camper 1'], $names);
    }

    public function testGetUserPaginatorSortByCreatedAtAsc(): void
    {
        $camperRepository = $this->getCamperRepository();
        $userRepository = $this->getUserRepository();
        $user = $userRepository->findOneByEmail('david@gmail.com');

        $data = new CamperSearchData();
        $data->setSortBy(CamperSortEnum::CREATED_AT_ASC);

        $paginator = $camperRepository->getUserPaginator($data, $user, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getCamperNames($paginator->getCurrentPageItems());
        $this->assertSame(['Camper 1', 'Camper 2'], $names);
    }

    public function testGetUserPaginatorSortByNameDesc(): void
    {
        $camperRepository = $this->getCamperRepository();
        $userRepository = $this->getUserRepository();
        $user = $userRepository->findOneByEmail('david@gmail.com');

        $data = new CamperSearchData();
        $data->setSortBy(CamperSortEnum::NAME_LAST_DESC);

        $paginator = $camperRepository->getUserPaginator($data, $user, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getCamperNames($paginator->getCurrentPageItems());
        $this->assertSame(['Camper 2', 'Camper 1'], $names);
    }

    public function testGetUserPaginatorSortByNameAsc(): void
    {
        $camperRepository = $this->getCamperRepository();
        $userRepository = $this->getUserRepository();
        $user = $userRepository->findOneByEmail('david@gmail.com');

        $data = new CamperSearchData();
        $data->setSortBy(CamperSortEnum::NAME_LAST_ASC);

        $paginator = $camperRepository->getUserPaginator($data, $user, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getCamperNames($paginator->getCurrentPageItems());
        $this->assertSame(['Camper 1', 'Camper 2'], $names);
    }

    private function getCamperNames(array $campers): array
    {
        $names = [];

        /** @var Camper $camper */
        foreach ($campers as $camper)
        {
            $names[] = $camper->getNameFirst() . ' ' . $camper->getNameLast();
        }

        return $names;
    }

    private function getUserRepository(): UserRepositoryInterface
    {
        $container = static::getContainer();

        /** @var UserRepositoryInterface $repository */
        $repository = $container->get(UserRepositoryInterface::class);

        return $repository;
    }

    private function getCamperRepository(): CamperRepository
    {
        $container = static::getContainer();

        /** @var CamperRepository $repository */
        $repository = $container->get(CamperRepository::class);

        return $repository;
    }
}