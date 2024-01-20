<?php

namespace App\Library\Constraint;

use App\Service\Validator\UniqueDiscountConfigValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that the entered name is not yet assigned to any discount config.
 */
#[Attribute]
class UniqueDiscountConfig extends Constraint
{
    public string $message = 'unique_discount_config';
    public string $nameProperty = 'name';
    public string $discountConfigProperty = 'discountConfig';

    public function __construct(string $message = null,
                                string $nameProperty = null,
                                string $discountConfigProperty = null,
                                array  $groups = null,
                                mixed  $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->nameProperty = $nameProperty ?? $this->nameProperty;
        $this->discountConfigProperty = $discountConfigProperty ?? $this->discountConfigProperty;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return UniqueDiscountConfigValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}