<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\PurchasableItemVariantSearchData;
use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\PurchasableItem;
use App\Model\Entity\PurchasableItemVariant;
use Symfony\Component\Uid\UuidV4;

interface PurchasableItemVariantRepositoryInterface
{
    /**
     * Saves a purchasable item variant.
     *
     * @param PurchasableItemVariant $purchasableItemVariant
     * @param bool $flush
     * @return void
     */
    public function savePurchasableItemVariant(PurchasableItemVariant $purchasableItemVariant, bool $flush): void;

    /**
     * Removes a purchasable item variant.
     *
     * @param PurchasableItemVariant $purchasableItemVariant
     * @param bool $flush
     * @return void
     */
    public function removePurchasableItemVariant(PurchasableItemVariant $purchasableItemVariant, bool $flush): void;

    /**
     * Finds one purchasable item variant by id.
     *
     * @param UuidV4 $id
     * @return PurchasableItemVariant|null
     */
    public function findOneById(UuidV4 $id): ?PurchasableItemVariant;

    /**
     * Finds purchasable item variants by name.
     *
     * @param string $name
     * @return PurchasableItemVariant[]
     */
    public function findByName(string $name): array;

    /**
     * Finds purchasable item variants by purchasable item.
     *
     * @param PurchasableItem $purchasableItem
     * @return PurchasableItemVariant[]
     */
    public function findByPurchasableItem(PurchasableItem $purchasableItem): array;

    /**
     * Returns admin purchasable item variant search paginator.
     *
     * @param PurchasableItemVariantSearchData $data
     * @param PurchasableItem $purchasableItem
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getAdminPaginator(PurchasableItemVariantSearchData $data, PurchasableItem $purchasableItem, int $currentPage, int $pageSize): PaginatorInterface;
}