<?php

namespace App\Model\Repository;

use App\Model\Entity\CampDate;
use App\Model\Entity\CampDatePurchasableItem;

interface CampDatePurchasableItemRepositoryInterface
{
    /**
     * Saves a camp date purchasable item.
     *
     * @param CampDatePurchasableItem $campDatePurchasableItem
     * @param bool $flush
     * @return void
     */
    public function saveCampDatePurchasableItem(CampDatePurchasableItem $campDatePurchasableItem, bool $flush): void;

    /**
     * Removes a camp date purchasable item.
     *
     * @param CampDatePurchasableItem $campDatePurchasableItem
     * @param bool $flush
     * @return void
     */
    public function removeCampDatePurchasableItem(CampDatePurchasableItem $campDatePurchasableItem, bool $flush): void;

    /**
     * Finds all camp date purchasable items that have the given camp date.
     *
     * @param CampDate $campDate
     * @return CampDatePurchasableItem[]
     */
    public function findByCampDate(CampDate $campDate): array;
}