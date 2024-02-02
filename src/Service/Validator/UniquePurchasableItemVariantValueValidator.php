<?php

namespace App\Service\Validator;

use App\Library\Constraint\UniquePurchasableItemVariantValue;
use App\Model\Entity\PurchasableItemVariant;
use App\Model\Entity\PurchasableItemVariantValue;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates that the entered name is not yet assigned to any value within its purchasable item variant.
 */
class UniquePurchasableItemVariantValueValidator extends ConstraintValidator
{
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniquePurchasableItemVariantValue)
        {
            throw new UnexpectedTypeException($constraint, UniquePurchasableItemVariantValue::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedTypeException($value, 'object');
        }

        $purchasableItemVariantValueData = $value;
        $purchasableItemVariant = $this->propertyAccessor->getValue($purchasableItemVariantValueData, $constraint->purchasableItemVariantProperty);

        if ($purchasableItemVariant !== null && !$purchasableItemVariant instanceof PurchasableItemVariant)
        {
            throw new UnexpectedTypeException($purchasableItemVariant, PurchasableItemVariant::class);
        }

        $name = $this->propertyAccessor->getValue($purchasableItemVariantValueData, $constraint->nameProperty);

        if ($name !== null && !is_string($name))
        {
            throw new UnexpectedTypeException($name, 'string');
        }

        $purchasableItemVariantValue = $this->propertyAccessor->getValue($purchasableItemVariantValueData, $constraint->purchasableItemVariantValueProperty);

        if ($purchasableItemVariantValue !== null && !$purchasableItemVariantValue instanceof PurchasableItemVariantValue)
        {
            throw new UnexpectedTypeException($purchasableItemVariantValue, PurchasableItemVariantValue::class);
        }

        if ($purchasableItemVariant === null || $name === null || $name === '')
        {
            return;
        }

        $id = $purchasableItemVariantValue?->getId();
        $existingPurchasableItemVariantValues = $purchasableItemVariant->getPurchasableItemVariantValues();

        foreach ($existingPurchasableItemVariantValues as $existingPurchasableItemVariantValue)
        {
            $existingId = $existingPurchasableItemVariantValue->getId();

            if ($id !== null && $existingId->toRfc4122() === $id->toRfc4122())
            {
                continue;
            }

            $existingName = $existingPurchasableItemVariantValue->getName();

            if ($existingName === $name)
            {
                $this->context
                    ->buildViolation($constraint->message)
                    ->atPath($constraint->nameProperty)
                    ->addViolation()
                ;

                return;
            }
        }
    }
}