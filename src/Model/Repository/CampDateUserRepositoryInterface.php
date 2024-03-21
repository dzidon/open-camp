<?php

namespace App\Model\Repository;

use App\Model\Entity\CampDate;
use App\Model\Entity\CampDateUser;
use App\Model\Entity\User;

interface CampDateUserRepositoryInterface
{
    /**
     * Saves a camp date user.
     *
     * @param CampDateUser $campDateUser
     * @param bool $flush
     * @return void
     */
    public function saveCampDateUser(CampDateUser $campDateUser, bool $flush): void;

    /**
     * Removes a camp date user.
     *
     * @param CampDateUser $campDateUser
     * @param bool $flush
     * @return void
     */
    public function removeCampDateUser(CampDateUser $campDateUser, bool $flush): void;

    /**
     * Finds all camp date users by camp date.
     *
     * @param CampDate $campDate
     * @return CampDateUser[]
     */
    public function findByCampDate(CampDate $campDate): array;

    /**
     * Finds all camp date users by user.
     *
     * @param User $user
     * @return CampDateUser[]
     */
    public function findByUser(User $user): array;

    /**
     * Return a camp date user.
     *
     * @param CampDate $campDate
     * @param User $user
     * @return CampDateUser|null
     */
    public function findOneForCampDateAndUser(CampDate $campDate, User $user): ?CampDateUser;
}