<?php

namespace App\Library\Constraint;

use App\Service\Validator\UniquePurchasableItemVariantValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that the entered name is not yet assigned to any variant within its purchasable item.
 */
#[Attribute]
class UniquePurchasableItemVariant extends Constraint
{
    public string $message = 'unique_purchasable_item_variant';
    public string $purchasableItemProperty = 'purchasableItem';
    public string $purchasableItemVariantProperty = 'purchasableItemVariant';
    public string $nameProperty = 'name';

    public function __construct(string $message = null,
                                string $purchasableItemProperty = null,
                                string $purchasableItemVariantProperty = null,
                                string $nameProperty = null,
                                array  $groups = null,
                                mixed  $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->purchasableItemProperty = $purchasableItemProperty ?? $this->purchasableItemProperty;
        $this->purchasableItemVariantProperty = $purchasableItemVariantProperty ?? $this->purchasableItemVariantProperty;
        $this->nameProperty = $nameProperty ?? $this->nameProperty;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return UniquePurchasableItemVariantValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}