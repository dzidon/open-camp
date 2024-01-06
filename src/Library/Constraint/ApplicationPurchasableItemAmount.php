<?php

namespace App\Library\Constraint;

use App\Service\Validator\ApplicationPurchasableItemAmountValidator;
use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * Validates entered application purchasable item instance amounts.
 */
#[Attribute]
class ApplicationPurchasableItemAmount extends Constraint
{
    public string $message = 'application_purchasable_item_amount';
    public string $applicationPurchasableItemProperty = 'applicationPurchasableItem';
    public string $applicationPurchasableItemInstancesDataProperty = 'applicationPurchasableItemInstancesData';
    public string $applicationPurchasableItemInstanceAmountProperty = 'amount';

    public function __construct(string $message = null,
                                string $applicationPurchasableItemProperty = null,
                                string $applicationPurchasableItemInstancesDataProperty = null,
                                string $applicationPurchasableItemInstanceAmountProperty = null,
                                array  $groups = null,
                                mixed  $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->message = $message ?? $this->message;
        $this->applicationPurchasableItemProperty = $applicationPurchasableItemProperty ?? $this->applicationPurchasableItemProperty;
        $this->applicationPurchasableItemInstancesDataProperty = $applicationPurchasableItemInstancesDataProperty ?? $this->applicationPurchasableItemInstancesDataProperty;
        $this->applicationPurchasableItemInstanceAmountProperty = $applicationPurchasableItemInstanceAmountProperty ?? $this->applicationPurchasableItemInstanceAmountProperty;
    }

    /**
     * @inheritDoc
     */
    public function validatedBy(): string
    {
        return ApplicationPurchasableItemAmountValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}