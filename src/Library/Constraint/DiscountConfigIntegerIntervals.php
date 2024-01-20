<?php

namespace App\Library\Constraint;

use App\Service\Validator\DiscountConfigIntegerIntervalsValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that the entered discount config integer intervals do not collide.
 */
#[Attribute]
class DiscountConfigIntegerIntervals extends Constraint
{
    public string $message = 'discount_config_integer_intervals';
    public string $fromProperty = 'from';
    public string $toProperty = 'to';

    public function __construct(string $message = null,
                                string $fromProperty = null,
                                string $toProperty = null,
                                array  $groups = null,
                                mixed  $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->fromProperty = $fromProperty ?? $this->fromProperty;
        $this->toProperty = $toProperty ?? $this->toProperty;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return DiscountConfigIntegerIntervalsValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}