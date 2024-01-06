<?php

namespace App\Library\Constraint;

use App\Service\Validator\ApplicationPurchasableItemVariantsNotBlankValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates that the entered application purchasable item variant values are not blank.
 */
#[Attribute]
class ApplicationPurchasableItemVariantsNotBlank extends Constraint
{
    public string $message = 'application_purchasable_item_variant_mandatory';
    public string $amountProperty = 'amount';
    public string $applicationPurchasableItemVariantsDataProperty = 'applicationPurchasableItemVariantsData';
    public string $applicationPurchasableItemVariantDataValueProperty = 'value';

    public function __construct(string $message = null,
                                string $amountProperty = null,
                                string $applicationPurchasableItemVariantsDataProperty = null,
                                string $applicationPurchasableItemVariantDataValueProperty = null,
                                array  $groups = null,
                                mixed  $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->amountProperty = $amountProperty ?? $this->amountProperty;
        $this->applicationPurchasableItemVariantsDataProperty = $applicationPurchasableItemVariantsDataProperty ?? $this->applicationPurchasableItemVariantsDataProperty;
        $this->applicationPurchasableItemVariantDataValueProperty = $applicationPurchasableItemVariantDataValueProperty ?? $this->applicationPurchasableItemVariantDataValueProperty;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return ApplicationPurchasableItemVariantsNotBlankValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}