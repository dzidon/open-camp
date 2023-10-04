<?php

namespace App\Library\Constraint;

use App\Service\Validator\UniquePurchasableItemValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that the entered name is not yet assigned to any purchasable item.
 */
#[Attribute]
class UniquePurchasableItem extends Constraint
{
    public string $message = 'unique_purchasable_item';
    public string $nameProperty = 'name';
    public string $purchasableItemProperty = 'purchasableItem';

    public function __construct(string $message = null,
                                string $nameProperty = null,
                                string $purchasableItemProperty = null,
                                array  $groups = null,
                                mixed  $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->nameProperty = $nameProperty ?? $this->nameProperty;
        $this->purchasableItemProperty = $purchasableItemProperty ?? $this->purchasableItemProperty;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return UniquePurchasableItemValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}