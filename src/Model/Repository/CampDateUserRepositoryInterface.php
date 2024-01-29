<?php

namespace App\Model\Repository;

use App\Model\Entity\CampDate;
use App\Model\Entity\CampDateUser;

interface CampDateUserRepositoryInterface
{
    /**
     * Saves a camp date purchasable item.
     *
     * @param CampDateUser $campDateUser
     * @param bool $flush
     * @return void
     */
    public function saveCampDateUser(CampDateUser $campDateUser, bool $flush): void;

    /**
     * Removes a camp date purchasable item.
     *
     * @param CampDateUser $campDateUser
     * @param bool $flush
     * @return void
     */
    public function removeCampDateUser(CampDateUser $campDateUser, bool $flush): void;

    /**
     * Finds all camp date purchasable items that have the given camp date.
     *
     * @param CampDate $campDate
     * @return CampDateUser[]
     */
    public function findByCampDate(CampDate $campDate): array;
}