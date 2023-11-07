<?php

namespace App\Tests\Model\Repository;

use App\Library\Data\Admin\PurchasableItemVariantValueSearchData;
use App\Library\Enum\Search\Data\Admin\PurchasableItemVariantValueSortEnum;
use App\Model\Entity\PurchasableItem;
use App\Model\Entity\PurchasableItemVariant;
use App\Model\Entity\PurchasableItemVariantValue;
use App\Model\Repository\PurchasableItemRepositoryInterface;
use App\Model\Repository\PurchasableItemVariantRepositoryInterface;
use App\Model\Repository\PurchasableItemVariantValueRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\UuidV4;

class PurchasableItemVariantValueRepositoryTest extends KernelTestCase
{
    public function testSaveAndRemove(): void
    {
        $purchasableItemVariantValueRepository = $this->getPurchasableItemVariantValueRepository();
        $purchasableItemVariantRepository = $this->getPurchasableItemVariantRepository();
        $purchasableItemRepository = $this->getPurchasableItemRepository();

        $purchasableItem = new PurchasableItem('Item', 'Label', 1000.0, 10);
        $purchasableItemVariant = new PurchasableItemVariant('Variant', 100, $purchasableItem);
        $purchasableItemVariantValue = new PurchasableItemVariantValue('Value', 100, $purchasableItemVariant);
        $purchasableItemRepository->savePurchasableItem($purchasableItem, false);
        $purchasableItemVariantRepository->savePurchasableItemVariant($purchasableItemVariant, false);
        $purchasableItemVariantValueRepository->savePurchasableItemVariantValue($purchasableItemVariantValue, true);
        $id = $purchasableItemVariantValue->getId();

        $loadedPurchasableItemVariantValue = $purchasableItemVariantValueRepository->findOneById($id);
        $this->assertNotNull($loadedPurchasableItemVariantValue);
        $this->assertSame($id, $loadedPurchasableItemVariantValue->getId());

        $purchasableItemVariantValueRepository->removePurchasableItemVariantValue($purchasableItemVariantValue, true);
        $loadedPurchasableItemVariantValue = $purchasableItemVariantValueRepository->findOneById($id);
        $this->assertNull($loadedPurchasableItemVariantValue);
    }

    public function testFindOneById(): void
    {
        $repository = $this->getPurchasableItemVariantValueRepository();

        $uid = new UuidV4('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b');
        $purchasableItemVariantValue = $repository->findOneById($uid);

        $this->assertSame($uid->toRfc4122(), $purchasableItemVariantValue->getId()->toRfc4122());
    }

    public function testFindByName(): void
    {
        $repository = $this->getPurchasableItemVariantValueRepository();

        $purchasableItemVariantValues = $repository->findByName('Value 1');
        $names = $this->getPurchasableItemVariantNames($purchasableItemVariantValues);
        $this->assertSame(['Value 1'], $names);
    }

    public function testFindByPurchasableItemVariant(): void
    {
        $purchasableItemVariantValueRepository = $this->getPurchasableItemVariantValueRepository();
        $purchasableItemVariantRepository = $this->getPurchasableItemVariantRepository();
        $purchasableItemVariant = $purchasableItemVariantRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $purchasableItemVariantValues = $purchasableItemVariantValueRepository->findByPurchasableItemVariant($purchasableItemVariant);
        $names = $this->getPurchasableItemVariantNames($purchasableItemVariantValues);
        $this->assertSame(['Value 1', 'Value 2'], $names);
    }

    public function testCanRemoveTripLocation(): void
    {
        $repository = $this->getPurchasableItemVariantValueRepository();

        $purchasableItemVariantValue = $repository->findOneById(new UuidV4('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));
        $this->assertTrue($repository->canRemovePurchasableItemVariantValue($purchasableItemVariantValue));

        $purchasableItemVariantValue = $repository->findOneById(new UuidV4('550e8400-e29b-41d4-a716-446655440000'));
        $this->assertFalse($repository->canRemovePurchasableItemVariantValue($purchasableItemVariantValue));
    }

    public function testGetAdminPaginator(): void
    {
        $purchasableItemVariantValueRepository = $this->getPurchasableItemVariantValueRepository();
        $purchasableItemVariantRepository = $this->getPurchasableItemVariantRepository();
        $purchasableItemVariant = $purchasableItemVariantRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new PurchasableItemVariantValueSearchData();
        $paginator = $purchasableItemVariantValueRepository->getAdminPaginator($data, $purchasableItemVariant, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getPurchasableItemVariantNames($paginator->getCurrentPageItems());
        $this->assertSame(['Value 2', 'Value 1'], $names);
    }

    public function testGetAdminPaginatorWithName(): void
    {
        $purchasableItemVariantValueRepository = $this->getPurchasableItemVariantValueRepository();
        $purchasableItemVariantRepository = $this->getPurchasableItemVariantRepository();
        $purchasableItemVariant = $purchasableItemVariantRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new PurchasableItemVariantValueSearchData();
        $data->setPhrase('lue 1');
        $paginator = $purchasableItemVariantValueRepository->getAdminPaginator($data, $purchasableItemVariant, 1, 2);
        $this->assertSame(1, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getPurchasableItemVariantNames($paginator->getCurrentPageItems());
        $this->assertSame(['Value 1'], $names);
    }

    public function testGetAdminPaginatorSortByCreatedAtDesc(): void
    {
        $purchasableItemVariantValueRepository = $this->getPurchasableItemVariantValueRepository();
        $purchasableItemVariantRepository = $this->getPurchasableItemVariantRepository();
        $purchasableItemVariant = $purchasableItemVariantRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new PurchasableItemVariantValueSearchData();
        $data->setSortBy(PurchasableItemVariantValueSortEnum::CREATED_AT_DESC);
        $paginator = $purchasableItemVariantValueRepository->getAdminPaginator($data, $purchasableItemVariant, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getPurchasableItemVariantNames($paginator->getCurrentPageItems());
        $this->assertSame(['Value 2', 'Value 1'], $names);
    }

    public function testGetAdminPaginatorSortByCreatedAtAsc(): void
    {
        $purchasableItemVariantValueRepository = $this->getPurchasableItemVariantValueRepository();
        $purchasableItemVariantRepository = $this->getPurchasableItemVariantRepository();
        $purchasableItemVariant = $purchasableItemVariantRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new PurchasableItemVariantValueSearchData();
        $data->setSortBy(PurchasableItemVariantValueSortEnum::CREATED_AT_ASC);
        $paginator = $purchasableItemVariantValueRepository->getAdminPaginator($data, $purchasableItemVariant, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getPurchasableItemVariantNames($paginator->getCurrentPageItems());
        $this->assertSame(['Value 1', 'Value 2'], $names);
    }

    public function testGetAdminPaginatorSortByNameDesc(): void
    {
        $purchasableItemVariantValueRepository = $this->getPurchasableItemVariantValueRepository();
        $purchasableItemVariantRepository = $this->getPurchasableItemVariantRepository();
        $purchasableItemVariant = $purchasableItemVariantRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new PurchasableItemVariantValueSearchData();
        $data->setSortBy(PurchasableItemVariantValueSortEnum::NAME_DESC);
        $paginator = $purchasableItemVariantValueRepository->getAdminPaginator($data, $purchasableItemVariant, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getPurchasableItemVariantNames($paginator->getCurrentPageItems());
        $this->assertSame(['Value 2', 'Value 1'], $names);
    }

    public function testGetAdminPaginatorSortByNameAsc(): void
    {
        $purchasableItemVariantValueRepository = $this->getPurchasableItemVariantValueRepository();
        $purchasableItemVariantRepository = $this->getPurchasableItemVariantRepository();
        $purchasableItemVariant = $purchasableItemVariantRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new PurchasableItemVariantValueSearchData();
        $data->setSortBy(PurchasableItemVariantValueSortEnum::NAME_ASC);
        $paginator = $purchasableItemVariantValueRepository->getAdminPaginator($data, $purchasableItemVariant, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getPurchasableItemVariantNames($paginator->getCurrentPageItems());
        $this->assertSame(['Value 1', 'Value 2'], $names);
    }

    public function testGetAdminPaginatorSortByPriorityDesc(): void
    {
        $purchasableItemVariantValueRepository = $this->getPurchasableItemVariantValueRepository();
        $purchasableItemVariantRepository = $this->getPurchasableItemVariantRepository();
        $purchasableItemVariant = $purchasableItemVariantRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new PurchasableItemVariantValueSearchData();
        $data->setSortBy(PurchasableItemVariantValueSortEnum::PRIORITY_DESC);
        $paginator = $purchasableItemVariantValueRepository->getAdminPaginator($data, $purchasableItemVariant, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getPurchasableItemVariantNames($paginator->getCurrentPageItems());
        $this->assertSame(['Value 1', 'Value 2'], $names);
    }

    public function testGetAdminPaginatorSortByPriorityAsc(): void
    {
        $purchasableItemVariantValueRepository = $this->getPurchasableItemVariantValueRepository();
        $purchasableItemVariantRepository = $this->getPurchasableItemVariantRepository();
        $purchasableItemVariant = $purchasableItemVariantRepository->findOneById(UuidV4::fromString('e37a04ae-2d35-4a1f-adc5-a6ab7b8e428b'));

        $data = new PurchasableItemVariantValueSearchData();
        $data->setSortBy(PurchasableItemVariantValueSortEnum::PRIORITY_ASC);
        $paginator = $purchasableItemVariantValueRepository->getAdminPaginator($data, $purchasableItemVariant, 1, 2);
        $this->assertSame(2, $paginator->getTotalItems());
        $this->assertSame(1, $paginator->getPagesCount());
        $this->assertSame(1, $paginator->getCurrentPage());
        $this->assertSame(2, $paginator->getPageSize());

        $names = $this->getPurchasableItemVariantNames($paginator->getCurrentPageItems());
        $this->assertSame(['Value 2', 'Value 1'], $names);
    }

    private function getPurchasableItemVariantNames(array $purchasableItemVariantValues): array
    {
        $names = [];

        /** @var PurchasableItemVariantValue $purchasableItemVariantValue */
        foreach ($purchasableItemVariantValues as $purchasableItemVariantValue)
        {
            $names[] = $purchasableItemVariantValue->getName();
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

    private function getPurchasableItemVariantRepository(): PurchasableItemVariantRepositoryInterface
    {
        $container = static::getContainer();

        /** @var PurchasableItemVariantRepositoryInterface $repository */
        $repository = $container->get(PurchasableItemVariantRepositoryInterface::class);

        return $repository;
    }

    private function getPurchasableItemVariantValueRepository(): PurchasableItemVariantValueRepository
    {
        $container = static::getContainer();

        /** @var PurchasableItemVariantValueRepository $repository */
        $repository = $container->get(PurchasableItemVariantValueRepository::class);

        return $repository;
    }
}