<?php

namespace App\Library\Constraint;

use App\Service\Validator\UniquePurchasableItemVariantValueValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that the entered name is not yet assigned to any value within its purchasable item variant.
 */
#[Attribute]
class UniquePurchasableItemVariantValue extends Constraint
{
    public string $message = 'unique_purchasable_item_variant_value';
    public string $purchasableItemVariantProperty = 'purchasableItemVariant';
    public string $purchasableItemVariantValueProperty = 'purchasableItemVariantValue';
    public string $nameProperty = 'name';

    public function __construct(string $message = null,
                                string $purchasableItemVariantProperty = null,
                                string $purchasableItemVariantValueProperty = null,
                                string $nameProperty = null,
                                array  $groups = null,
                                mixed  $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->purchasableItemVariantProperty = $purchasableItemVariantProperty ?? $this->purchasableItemVariantProperty;
        $this->purchasableItemVariantValueProperty = $purchasableItemVariantValueProperty ?? $this->purchasableItemVariantValueProperty;
        $this->nameProperty = $nameProperty ?? $this->nameProperty;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return UniquePurchasableItemVariantValueValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}