<?php

namespace App\Service\Validator;

use App\Library\Constraint\UniquePurchasableItemVariant;
use App\Model\Entity\PurchasableItem;
use App\Model\Entity\PurchasableItemVariant;
use App\Model\Repository\PurchasableItemVariantRepositoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Validates that the entered name is not yet assigned to any variant within its purchasable item.
 */
class UniquePurchasableItemVariantValidator extends ConstraintValidator
{
    private PropertyAccessorInterface $propertyAccessor;
    private PurchasableItemVariantRepositoryInterface $purchasableItemVariantRepository;
    private TranslatorInterface $translator;

    public function __construct(PropertyAccessorInterface       $propertyAccessor,
                                PurchasableItemVariantRepositoryInterface $purchasableItemVariantRepository,
                                TranslatorInterface             $translator)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->purchasableItemVariantRepository = $purchasableItemVariantRepository;
        $this->translator = $translator;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniquePurchasableItemVariant)
        {
            throw new UnexpectedTypeException($constraint, UniquePurchasableItemVariant::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedTypeException($value, 'object');
        }

        $purchasableItemVariantData = $value;
        $purchasableItem = $this->propertyAccessor->getValue($purchasableItemVariantData, $constraint->purchasableItemProperty);

        if (!$purchasableItem instanceof PurchasableItem)
        {
            throw new UnexpectedTypeException($purchasableItem, PurchasableItem::class);
        }

        $name = $this->propertyAccessor->getValue($purchasableItemVariantData, $constraint->nameProperty);

        if ($name !== null && !is_string($name))
        {
            throw new UnexpectedTypeException($name, 'string');
        }

        $purchasableItemVariant = $this->propertyAccessor->getValue($purchasableItemVariantData, $constraint->purchasableItemVariantProperty);

        if ($purchasableItemVariant !== null && !$purchasableItemVariant instanceof PurchasableItemVariant)
        {
            throw new UnexpectedTypeException($purchasableItemVariant, PurchasableItemVariant::class);
        }

        if ($name === null || $name === '')
        {
            return;
        }

        $id = $purchasableItemVariant?->getId();
        $existingPurchasableItemVariants = $this->purchasableItemVariantRepository->findByPurchasableItem($purchasableItem);

        foreach ($existingPurchasableItemVariants as $existingPurchasableItemVariant)
        {
            $existingId = $existingPurchasableItemVariant->getId();

            if ($id !== null && $existingId->toRfc4122() === $id->toRfc4122())
            {
                continue;
            }

            $existingName = $existingPurchasableItemVariant->getName();

            if ($existingName === $name)
            {
                $message = $this->translator->trans($constraint->message, [], 'validators');

                $this->context
                    ->buildViolation($message)
                    ->atPath($constraint->nameProperty)
                    ->addViolation()
                ;

                return;
            }
        }
    }
}