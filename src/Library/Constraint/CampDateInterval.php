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

    public function __construct(string $message = null,
                                string $idProperty = null,
                                string $campProperty = null,
                                string $startAtProperty = null,
                                string $endAtProperty = null,
                                array  $groups = null,
                                mixed  $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->idProperty = $idProperty ?? $this->idProperty;
        $this->campProperty = $campProperty ?? $this->campProperty;
        $this->startAtProperty = $startAtProperty ?? $this->startAtProperty;
        $this->endAtProperty = $endAtProperty ?? $this->endAtProperty;
    }

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