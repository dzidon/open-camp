<?php

namespace App\Library\Constraint;

use App\Service\Validator\CampDateIntervalValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that the entered datetime interval is empty. Validation is performed against the database.
 */
#[Attribute]
class CampDateInterval extends Constraint
{
    public string $message = 'camp_date_interval';
    public string $idProperty = 'id';
    public string $campProperty = 'camp';
    public string $startAtProperty = 'startAt';
    public string $endAtProperty = 'endAt';

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return CampDateIntervalValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}