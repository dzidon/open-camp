<?php

namespace App\Library\Constraint;

use App\Service\Validator\DiscountSiblingIntervalInConfigValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that the entered name is not yet assigned to any discount config.
 */
#[Attribute]
class DiscountSiblingIntervalInConfig extends Constraint
{
    public string $message = 'discount_sibling_interval_not_in_config';
    public string $discountSiblingsConfigProperty = 'discountSiblingsConfig';
    public string $discountSiblingsIntervalProperty = 'discountSiblingsInterval';

    public function __construct(string $message = null,
                                string $discountSiblingsConfigProperty = null,
                                string $discountSiblingsIntervalProperty = null,
                                array  $groups = null,
                                mixed  $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->discountSiblingsConfigProperty = $discountSiblingsConfigProperty ?? $this->discountSiblingsConfigProperty;
        $this->discountSiblingsIntervalProperty = $discountSiblingsIntervalProperty ?? $this->discountSiblingsIntervalProperty;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return DiscountSiblingIntervalInConfigValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}