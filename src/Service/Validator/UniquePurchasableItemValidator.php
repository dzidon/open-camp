<?php

namespace App\Service\Validator;

use App\Library\Constraint\UniquePurchasableItem;
use App\Model\Entity\PurchasableItem;
use App\Model\Repository\PurchasableItemRepositoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates that the entered name is not yet assigned to any purchasable item.
 */
class UniquePurchasableItemValidator extends ConstraintValidator
{
    private PropertyAccessorInterface $propertyAccessor;
    private PurchasableItemRepositoryInterface $purchasableItemRepository;

    public function __construct(PropertyAccessorInterface          $propertyAccessor,
                                PurchasableItemRepositoryInterface $purchasableItemRepository)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->purchasableItemRepository = $purchasableItemRepository;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniquePurchasableItem)
        {
            throw new UnexpectedTypeException($constraint, UniquePurchasableItem::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedTypeException($value, 'object');
        }

        $itemData = $value;
        $name = $this->propertyAccessor->getValue($itemData, $constraint->nameProperty);

        if ($name !== null && !is_string($name))
        {
            throw new UnexpectedTypeException($name, 'string');
        }

        $purchasableItem = $this->propertyAccessor->getValue($itemData, $constraint->purchasableItemProperty);

        if ($purchasableItem !== null && !$purchasableItem instanceof PurchasableItem)
        {
            throw new UnexpectedTypeException($purchasableItem, PurchasableItem::class);
        }

        if ($name === null || $name === '')
        {
            return;
        }

        $existingPurchasableItem = $this->purchasableItemRepository->findOneByName($name);

        if ($existingPurchasableItem === null)
        {
            return;
        }

        $id = $purchasableItem?->getId();
        $existingId = $existingPurchasableItem->getId();

        if ($id === null || $id->toRfc4122() !== $existingId->toRfc4122())
        {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath($constraint->nameProperty)
                ->addViolation()
            ;
        }
    }
}