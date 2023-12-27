<?php

namespace App\Model\Repository;

use App\Model\Entity\ApplicationFormFieldValue;

interface ApplicationFormFieldValueRepositoryInterface
{
    /**
     * Saves an application form field value.
     *
     * @param ApplicationFormFieldValue $applicationFormFieldValue
     * @param bool $flush
     * @return void
     */
    public function saveApplicationFormFieldValue(ApplicationFormFieldValue $applicationFormFieldValue, bool $flush): void;

    /**
     * Removes an application form field value.
     *
     * @param ApplicationFormFieldValue $applicationFormFieldValue
     * @param bool $flush
     * @return void
     */
    public function removeApplicationFormFieldValue(ApplicationFormFieldValue $applicationFormFieldValue, bool $flush): void;
}