<?php

namespace App\Tests\Model\Repository;

use App\Library\Data\Admin\PurchasableItemVariantSearchData;
use App\Library\Enum\Search\Data\Admin\PurchasableItemVariantSortEnum;
use App\Model\Entity\PurchasableItem;
use App\Model\Entity\PurchasableItemVariant;
use App\Model\Repository\PurchasableItemRepositoryInterface;
use App\Model\Repository\PurchasableItemVariantRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class PurchasableItemVariantRepositoryTest extends KernelTestCase
{
    public function testSaveAndRemove(): void
    {
        $purchasableItemVariantRepository = $this->getPurchasableItemVariantRepository();
        $purchasableItemRepository = $this->getPurchasableItemRepository();

        $purchasableItem = new PurchasableItem('Item', 'Label', 1000.0, 10);
        $purchasableItemVariant = new PurchasableItemVariant('Variant', 100, $purchasableItem);
        $purchasableItemRepository->savePurchasableItem($purchasableItem, false);
        $purchasableItemVariantRepository->savePurchasableItemVariant($purchasableItemVariant, true);
        $id = $purchasableItemVariant->getId();

        $loadedPurchasableItemVariant = $purchasableItemVariantRepository->findOneById($id);
        $this->assertNotNull($loadedPurchasableItemVariant);
        $this->assertSame($id, $loadedPurchasableItemVariant->getId());

        $purchasableItemVariantRepository->removePurchasableItemVariant($purchasableItemVariant, true);
        $loadedPurchasableItemVariant = $purchasableItemVariantRepository->findOneById($id);
        $this->assertNull($loadedPurchasableItemVariant);
    }

    public function testFindOneById(): void
    {
        $repository = $this->getPurchasableItemVariantRepository();

        $uid = new UuidV4('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $tripLocationPath = $repository->findOneById($uid);

        $this->assertSame($uid->toRfc4122(), $tripLocationPath->getId()->toRfc4122());
    }

    public function testFindByName(): void
    {
        $repository = $this->getPurchasableItemVariantRepository();

        $purchasableItemVariants = $repository->findByName('Variant 1');
        $names = $this->getPurchasableItemVariantNames($purchasableItemVariants);
        $this->assertSame(['Variant 1'], $names);
    }

    public function testGetAdminPaginator(): void
    {
        $purchasableItemVariantRepository = $this->getPurchasableItemVariantRepository();
        $purchasableItemRepository = $this->getPurchasableItemRepository();
        $purchasableItem = $purchasableItemRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new PurchasableItemVariantSearchData();
        $paginator = $purchasableItemVariantRepository->getAdminPaginator($data, $purchasableItem, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getPurchasableItemVariantNames($paginator->getCurrentPageItems());
        $this->assertSame(['Variant 2', 'Variant 1'], $names);
    }

    public function testGetAdminPaginatorWithName(): void
    {
        $purchasableItemVariantRepository = $this->getPurchasableItemVariantRepository();
        $purchasableItemRepository = $this->getPurchasableItemRepository();
        $purchasableItem = $purchasableItemRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new PurchasableItemVariantSearchData();
        $data->setPhrase('ant 1');
        $paginator = $purchasableItemVariantRepository->getAdminPaginator($data, $purchasableItem, 1, 2);
        $this->assertSame(1, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getPurchasableItemVariantNames($paginator->getCurrentPageItems());
        $this->assertSame(['Variant 1'], $names);
    }

    public function testGetAdminPaginatorSortByCreatedAtDesc(): void
    {
        $purchasableItemVariantRepository = $this->getPurchasableItemVariantRepository();
        $purchasableItemRepository = $this->getPurchasableItemRepository();
        $purchasableItem = $purchasableItemRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new PurchasableItemVariantSearchData();
        $data->setSortBy(PurchasableItemVariantSortEnum::CREATED_AT_DESC);
        $paginator = $purchasableItemVariantRepository->getAdminPaginator($data, $purchasableItem, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getPurchasableItemVariantNames($paginator->getCurrentPageItems());
        $this->assertSame(['Variant 2', 'Variant 1'], $names);
    }

    public function testGetAdminPaginatorSortByCreatedAtAsc(): void
    {
        $purchasableItemVariantRepository = $this->getPurchasableItemVariantRepository();
        $purchasableItemRepository = $this->getPurchasableItemRepository();
        $purchasableItem = $purchasableItemRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new PurchasableItemVariantSearchData();
        $data->setSortBy(PurchasableItemVariantSortEnum::CREATED_AT_ASC);
        $paginator = $purchasableItemVariantRepository->getAdminPaginator($data, $purchasableItem, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getPurchasableItemVariantNames($paginator->getCurrentPageItems());
        $this->assertSame(['Variant 1', 'Variant 2'], $names);
    }

    public function testGetAdminPaginatorSortByNameDesc(): void
    {
        $purchasableItemVariantRepository = $this->getPurchasableItemVariantRepository();
        $purchasableItemRepository = $this->getPurchasableItemRepository();
        $purchasableItem = $purchasableItemRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new PurchasableItemVariantSearchData();
        $data->setSortBy(PurchasableItemVariantSortEnum::NAME_DESC);
        $paginator = $purchasableItemVariantRepository->getAdminPaginator($data, $purchasableItem, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getPurchasableItemVariantNames($paginator->getCurrentPageItems());
        $this->assertSame(['Variant 2', 'Variant 1'], $names);
    }

    public function testGetAdminPaginatorSortByNameAsc(): void
    {
        $purchasableItemVariantRepository = $this->getPurchasableItemVariantRepository();
        $purchasableItemRepository = $this->getPurchasableItemRepository();
        $purchasableItem = $purchasableItemRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new PurchasableItemVariantSearchData();
        $data->setSortBy(PurchasableItemVariantSortEnum::NAME_ASC);
        $paginator = $purchasableItemVariantRepository->getAdminPaginator($data, $purchasableItem, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getPurchasableItemVariantNames($paginator->getCurrentPageItems());
        $this->assertSame(['Variant 1', 'Variant 2'], $names);
    }

    public function testGetAdminPaginatorSortByPriorityDesc(): void
    {
        $purchasableItemVariantRepository = $this->getPurchasableItemVariantRepository();
        $purchasableItemRepository = $this->getPurchasableItemRepository();
        $purchasableItem = $purchasableItemRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new PurchasableItemVariantSearchData();
        $data->setSortBy(PurchasableItemVariantSortEnum::PRIORITY_DESC);
        $paginator = $purchasableItemVariantRepository->getAdminPaginator($data, $purchasableItem, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getPurchasableItemVariantNames($paginator->getCurrentPageItems());
        $this->assertSame(['Variant 1', 'Variant 2'], $names);
    }

    public function testGetAdminPaginatorSortByPriorityAsc(): void
    {
        $purchasableItemVariantRepository = $this->getPurchasableItemVariantRepository();
        $purchasableItemRepository = $this->getPurchasableItemRepository();
        $purchasableItem = $purchasableItemRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new PurchasableItemVariantSearchData();
        $data->setSortBy(PurchasableItemVariantSortEnum::PRIORITY_ASC);
        $paginator = $purchasableItemVariantRepository->getAdminPaginator($data, $purchasableItem, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getPurchasableItemVariantNames($paginator->getCurrentPageItems());
        $this->assertSame(['Variant 2', 'Variant 1'], $names);
    }

    private function getPurchasableItemVariantNames(array $purchasableItemVariants): array
    {
        $names = [];

        /** @var PurchasableItemVariant $purchasableItemVariant */
        foreach ($purchasableItemVariants as $purchasableItemVariant)
        {
            $names[] = $purchasableItemVariant->getName();
        }

        return $names;
    }

    private function getPurchasableItemRepository(): PurchasableItemRepositoryInterface
    {
        $container = static::getContainer();

        /** @var PurchasableItemRepositoryInterface $repository */
        $repository = $container->get(PurchasableItemRepositoryInterface::class);

        return $repository;
    }

    private function getPurchasableItemVariantRepository(): PurchasableItemVariantRepository
    {
        $container = static::getContainer();

        /** @var PurchasableItemVariantRepository $repository */
        $repository = $container->get(PurchasableItemVariantRepository::class);

        return $repository;
    }
}