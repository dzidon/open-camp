<?php

namespace App\Tests\Model\Repository;

use App\Library\Data\Admin\PurchasableItemSearchData;
use App\Library\Enum\Search\Data\Admin\PurchasableItemSortEnum;
use App\Model\Entity\PurchasableItem;
use App\Model\Repository\PurchasableItemRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class PurchasableItemRepositoryTest extends KernelTestCase
{
    public function testSaveAndRemove(): void
    {
        $purchasableItemRepository = $this->getPurchasableItemRepository();

        $purchasableItem = new PurchasableItem('Item', 1000.0, 10);
        $purchasableItemRepository->savePurchasableItem($purchasableItem, true);
        $id = $purchasableItem->getId();

        $loadedPurchasableItem = $purchasableItemRepository->findOneById($id);
        $this->assertNotNull($loadedPurchasableItem);
        $this->assertSame($id, $loadedPurchasableItem->getId());

        $purchasableItemRepository->removePurchasableItem($purchasableItem, true);
        $loadedPurchasableItem = $purchasableItemRepository->findOneById($id);
        $this->assertNull($loadedPurchasableItem);
    }

    public function testFindOneById(): void
    {
        $purchasableItemRepository = $this->getPurchasableItemRepository();

        $uid = new UuidV4('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $purchasableItem = $purchasableItemRepository->findOneById($uid);

        $this->assertSame($uid->toRfc4122(), $purchasableItem->getId()->toRfc4122());
    }

    public function testFindOneByName(): void
    {
        $purchasableItemRepository = $this->getPurchasableItemRepository();

        $purchasableItem = $purchasableItemRepository->findOneByName('Item 1');
        $this->assertSame('Item 1', $purchasableItem->getName());
    }

    public function testGetAdminPaginator(): void
    {
        $purchasableItemRepository = $this->getPurchasableItemRepository();

        $data = new PurchasableItemSearchData();
        $paginator = $purchasableItemRepository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getPurchasableItemNames($paginator->getCurrentPageItems());
        $this->assertSame(['Item 2', 'Item 1'], $names);
    }

    public function testGetAdminPaginatorWithName(): void
    {
        $purchasableItemRepository = $this->getPurchasableItemRepository();

        $data = new PurchasableItemSearchData();
        $data->setPhrase('em 1');
        $paginator = $purchasableItemRepository->getAdminPaginator($data, 1, 2);
        $this->assertSame(1, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getPurchasableItemNames($paginator->getCurrentPageItems());
        $this->assertSame(['Item 1'], $names);
    }

    public function testGetAdminPaginatorSortByCreatedAtDesc(): void
    {
        $purchasableItemRepository = $this->getPurchasableItemRepository();

        $data = new PurchasableItemSearchData();
        $data->setSortBy(PurchasableItemSortEnum::CREATED_AT_DESC);

        $paginator = $purchasableItemRepository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getPurchasableItemNames($paginator->getCurrentPageItems());
        $this->assertSame(['Item 2', 'Item 1'], $names);
    }

    public function testGetAdminPaginatorSortByCreatedAtAsc(): void
    {
        $purchasableItemRepository = $this->getPurchasableItemRepository();

        $data = new PurchasableItemSearchData();
        $data->setSortBy(PurchasableItemSortEnum::CREATED_AT_ASC);

        $paginator = $purchasableItemRepository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getPurchasableItemNames($paginator->getCurrentPageItems());
        $this->assertSame(['Item 1', 'Item 2'], $names);
    }

    public function testGetAdminPaginatorSortByNameDesc(): void
    {
        $purchasableItemRepository = $this->getPurchasableItemRepository();

        $data = new PurchasableItemSearchData();
        $data->setSortBy(PurchasableItemSortEnum::NAME_DESC);

        $paginator = $purchasableItemRepository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getPurchasableItemNames($paginator->getCurrentPageItems());
        $this->assertSame(['Item 2', 'Item 1'], $names);
    }

    public function testGetAdminPaginatorSortByNameAsc(): void
    {
        $purchasableItemRepository = $this->getPurchasableItemRepository();

        $data = new PurchasableItemSearchData();
        $data->setSortBy(PurchasableItemSortEnum::NAME_ASC);

        $paginator = $purchasableItemRepository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getPurchasableItemNames($paginator->getCurrentPageItems());
        $this->assertSame(['Item 1', 'Item 2'], $names);
    }

    public function testGetAdminPaginatorSortByPriceDesc(): void
    {
        $purchasableItemRepository = $this->getPurchasableItemRepository();

        $data = new PurchasableItemSearchData();
        $data->setSortBy(PurchasableItemSortEnum::PRICE_DESC);

        $paginator = $purchasableItemRepository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getPurchasableItemNames($paginator->getCurrentPageItems());
        $this->assertSame(['Item 2', 'Item 1'], $names);
    }

    public function testGetAdminPaginatorSortByPriceAsc(): void
    {
        $purchasableItemRepository = $this->getPurchasableItemRepository();

        $data = new PurchasableItemSearchData();
        $data->setSortBy(PurchasableItemSortEnum::PRICE_ASC);

        $paginator = $purchasableItemRepository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getPurchasableItemNames($paginator->getCurrentPageItems());
        $this->assertSame(['Item 1', 'Item 2'], $names);
    }

    public function testGetAdminPaginatorSortByMaxAmountPerCamperDesc(): void
    {
        $purchasableItemRepository = $this->getPurchasableItemRepository();

        $data = new PurchasableItemSearchData();
        $data->setSortBy(PurchasableItemSortEnum::MAX_AMOUNT_PER_CAMPER_DESC);

        $paginator = $purchasableItemRepository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getPurchasableItemNames($paginator->getCurrentPageItems());
        $this->assertSame(['Item 2', 'Item 1'], $names);
    }

    public function testGetAdminPaginatorSortByMaxAmountPerCamperAsc(): void
    {
        $purchasableItemRepository = $this->getPurchasableItemRepository();

        $data = new PurchasableItemSearchData();
        $data->setSortBy(PurchasableItemSortEnum::MAX_AMOUNT_PER_CAMPER_ASC);

        $paginator = $purchasableItemRepository->getAdminPaginator($data, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getPurchasableItemNames($paginator->getCurrentPageItems());
        $this->assertSame(['Item 1', 'Item 2'], $names);
    }

    private function getPurchasableItemNames(array $purchasableItems): array
    {
        $names = [];

        /** @var PurchasableItem $purchasableItem */
        foreach ($purchasableItems as $purchasableItem)
        {
            $names[] = $purchasableItem->getName();
        }

        return $names;
    }

    private function getPurchasableItemRepository(): PurchasableItemRepository
    {
        $container = static::getContainer();

        /** @var PurchasableItemRepository $repository */
        $repository = $container->get(PurchasableItemRepository::class);

        return $repository;
    }
}