<?php

namespace App\Library\Constraint;

use App\Service\Validator\CampDateIntervalsValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that the entered collection of camp datetime intervals does not contain a collision. There is no validation
 * against the database.
 */
#[Attribute]
class CampDateIntervals extends Constraint
{
    public string $message = 'camp_date_interval';
    public string $campDatesDataProperty = 'campDatesData';
    public string $startAtProperty = 'startAt';
    public string $endAtProperty = 'endAt';

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return CampDateIntervalsValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}