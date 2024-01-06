<?php

namespace App\Service\Validator;

use App\Library\Constraint\ApplicationPurchasableItemAmount;
use App\Library\Data\User\ApplicationPurchasableItemInstanceData;
use App\Model\Entity\ApplicationPurchasableItem;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ApplicationPurchasableItemAmountValidator extends ConstraintValidator
{
    private TranslatorInterface $translator;

    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(TranslatorInterface $translator, PropertyAccessorInterface $propertyAccessor)
    {
        $this->translator = $translator;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ApplicationPurchasableItemAmount)
        {
            throw new UnexpectedTypeException($constraint, ApplicationPurchasableItemAmount::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedTypeException($value, 'object');
        }

        $applicationPurchasableItemData = $value;
        $applicationPurchasableItem = $this->propertyAccessor->getValue($applicationPurchasableItemData, $constraint->applicationPurchasableItemProperty);

        if (!$applicationPurchasableItem instanceof ApplicationPurchasableItem)
        {
            throw new UnexpectedTypeException($applicationPurchasableItem, ApplicationPurchasableItem::class);
        }

        /** @var ApplicationPurchasableItemInstanceData[] $applicationPurchasableItemInstancesData */
        $applicationPurchasableItemInstancesData = $this->propertyAccessor->getValue($applicationPurchasableItemData, $constraint->applicationPurchasableItemInstancesDataProperty);

        if (!is_iterable($applicationPurchasableItemInstancesData))
        {
            throw new UnexpectedTypeException($applicationPurchasableItemInstancesData, 'iterable');
        }

        foreach ($applicationPurchasableItemInstancesData as $applicationPurchasableItemInstanceData)
        {
            if (!$applicationPurchasableItemInstanceData instanceof ApplicationPurchasableItemInstanceData)
            {
                throw new UnexpectedTypeException($applicationPurchasableItemInstanceData, ApplicationPurchasableItemInstanceData::class);
            }
        }

        $calculatedMaxAmount = $applicationPurchasableItem->getCalculatedMaxAmount();
        $totalAmount = 0;

        foreach ($applicationPurchasableItemInstancesData as $applicationPurchasableItemInstanceData)
        {
            $amount = $this->propertyAccessor->getValue($applicationPurchasableItemInstanceData, $constraint->applicationPurchasableItemInstanceAmountProperty);

            if (!is_int($amount))
            {
                throw new UnexpectedTypeException($amount, 'int');
            }

            if ($amount <= 0)
            {
                continue;
            }

            $totalAmount += $amount;
        }

        if ($totalAmount > $calculatedMaxAmount)
        {
            $message = $this->translator->trans($constraint->message, [
                'item_name'      => $applicationPurchasableItem->getLabel(),
                'current_amount' => $totalAmount,
                'max_amount'     => $calculatedMaxAmount,
            ], 'validators');

            $this->context
                ->buildViolation($message)
                ->atPath($constraint->applicationPurchasableItemInstancesDataProperty)
                ->addViolation()
            ;
        }
    }
}