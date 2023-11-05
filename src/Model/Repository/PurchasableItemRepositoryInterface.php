<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\PurchasableItemSearchData;
use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\PurchasableItem;
use Symfony\Component\Uid\UuidV4;

interface PurchasableItemRepositoryInterface
{
    /**
     * Saves a purchasable item.
     *
     * @param PurchasableItem $purchasableItem
     * @param bool $flush
     * @return void
     */
    public function savePurchasableItem(PurchasableItem $purchasableItem, bool $flush): void;

    /**
     * Removes a purchasable item.
     *
     * @param PurchasableItem $purchasableItem
     * @param bool $flush
     * @return void
     */
    public function removePurchasableItem(PurchasableItem $purchasableItem, bool $flush): void;

    /**
     * Finds all available purchasable items.
     *
     * @return PurchasableItem[]
     */
    public function findAll(): array;

    /**
     * Finds one purchasable item by id.
     *
     * @param UuidV4 $id
     * @return PurchasableItem|null
     */
    public function findOneById(UuidV4 $id): ?PurchasableItem;

    /**
     * Finds one purchasable item by name.
     *
     * @param string $name
     * @return PurchasableItem|null
     */
    public function findOneByName(string $name): ?PurchasableItem;

    /**
     * Returns admin purchasable item search paginator.
     *
     * @param PurchasableItemSearchData $data
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getAdminPaginator(PurchasableItemSearchData $data, int $currentPage, int $pageSize): PaginatorInterface;
}