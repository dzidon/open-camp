<?php

namespace App\Model\Repository;

use App\Model\Entity\CampDate;
use App\Model\Entity\CampDateFormField;

interface CampDateFormFieldRepositoryInterface
{
    /**
     * Saves a camp date form field.
     *
     * @param CampDateFormField $campDateFormField
     * @param bool $flush
     * @return void
     */
    public function saveCampDateFormField(CampDateFormField $campDateFormField, bool $flush): void;

    /**
     * Removes a camp date form field.
     *
     * @param CampDateFormField $campDateFormField
     * @param bool $flush
     * @return void
     */
    public function removeCampDateFormField(CampDateFormField $campDateFormField, bool $flush): void;

    /**
     * Finds all camp date form fields that have the given camp date.
     *
     * @param CampDate $campDate
     * @return CampDateFormField[]
     */
    public function findByCampDate(CampDate $campDate): array;
}