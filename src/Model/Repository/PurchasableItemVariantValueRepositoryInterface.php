<?php

namespace App\Model\Repository;

use App\Library\Data\Admin\PurchasableItemVariantValueSearchData;
use App\Library\Search\Paginator\PaginatorInterface;
use App\Model\Entity\PurchasableItemVariant;
use App\Model\Entity\PurchasableItemVariantValue;
use Symfony\Component\Uid\UuidV4;

interface PurchasableItemVariantValueRepositoryInterface
{
    /**
     * Saves a purchasable item variant value.
     *
     * @param PurchasableItemVariantValue $purchasableItemVariantValue
     * @param bool $flush
     * @return void
     */
    public function savePurchasableItemVariantValue(PurchasableItemVariantValue $purchasableItemVariantValue, bool $flush): void;

    /**
     * Removes a purchasable item variant value.
     *
     * @param PurchasableItemVariantValue $purchasableItemVariantValue
     * @param bool $flush
     * @return void
     */
    public function removePurchasableItemVariantValue(PurchasableItemVariantValue $purchasableItemVariantValue, bool $flush): void;

    /**
     * Finds one purchasable item variant value by id.
     *
     * @param UuidV4 $id
     * @return PurchasableItemVariantValue|null
     */
    public function findOneById(UuidV4 $id): ?PurchasableItemVariantValue;

    /**
     * Finds purchasable item variant values by name.
     *
     * @param string $name
     * @return PurchasableItemVariantValue[]
     */
    public function findByName(string $name): array;

    /**
     * Returns true if the given purchasable item variant value can be removed.
     *
     * @param PurchasableItemVariantValue $purchasableItemVariantValue
     * @return bool
     */
    public function canRemovePurchasableItemVariantValue(PurchasableItemVariantValue $purchasableItemVariantValue): bool;

    /**
     * Returns admin purchasable item variant value search paginator.
     *
     * @param PurchasableItemVariantValueSearchData $data
     * @param PurchasableItemVariant $purchasableItemVariant
     * @param int $currentPage
     * @param int $pageSize
     * @return PaginatorInterface
     */
    public function getAdminPaginator(PurchasableItemVariantValueSearchData $data,
                                      PurchasableItemVariant                $purchasableItemVariant,
                                      int                                   $currentPage,
                                      int                                   $pageSize): PaginatorInterface;
}