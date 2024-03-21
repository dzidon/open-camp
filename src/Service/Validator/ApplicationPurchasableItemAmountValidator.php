<?php

namespace App\Service\Validator;

use App\Library\Constraint\ApplicationPurchasableItemAmount;
use App\Library\Data\Common\ApplicationPurchasableItemInstanceData;
use App\Model\Entity\ApplicationPurchasableItem;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ApplicationPurchasableItemAmountValidator extends ConstraintValidator
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
        if (!$constraint instanceof ApplicationPurchasableItemAmount)
        {
            throw new UnexpectedTypeException($constraint, ApplicationPurchasableItemAmount::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedTypeException($value, 'object');
        }

        $applicationPurchasableItemData = $value;
        $applicationPurchasableItem = $this->propertyAccessor->getValue(
            $applicationPurchasableItemData,
            $constraint->applicationPurchasableItemProperty
        );

        if (!$applicationPurchasableItem instanceof ApplicationPurchasableItem)
        {
            throw new UnexpectedTypeException($applicationPurchasableItem, ApplicationPurchasableItem::class);
        }

        /** @var ApplicationPurchasableItemInstanceData[] $applicationPurchasableItemInstancesData */
        $applicationPurchasableItemInstancesData = $this->propertyAccessor->getValue(
            $applicationPurchasableItemData,
            $constraint->applicationPurchasableItemInstancesDataProperty
        );

        if (!is_iterable($applicationPurchasableItemInstancesData))
        {
            throw new UnexpectedTypeException($applicationPurchasableItemInstancesData, 'iterable');
        }

        $totalAmount = 0;
        $application = $applicationPurchasableItem->getApplication();
        $maxAmount = $applicationPurchasableItem->getMaxAmount();
        $isIndividualMode = $application->isPurchasableItemsIndividualMode();

        if (!$isIndividualMode)
        {
            $maxAmount = $applicationPurchasableItem->getCalculatedMaxAmount();
        }

        foreach ($applicationPurchasableItemInstancesData as $applicationPurchasableItemInstanceData)
        {
            $amount = $this->propertyAccessor->getValue(
                $applicationPurchasableItemInstanceData,
                $constraint->applicationPurchasableItemInstanceAmountProperty
            );

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

        if ($totalAmount > $maxAmount)
        {
            $path = sprintf('%s[%s].%s',
                $constraint->applicationPurchasableItemInstancesDataProperty,
                array_key_last($applicationPurchasableItemInstancesData),
                $constraint->applicationPurchasableItemInstanceAmountProperty
            );

            $this->context
                ->buildViolation($constraint->message, [
                    'current_amount' => $totalAmount,
                    'max_amount'     => $maxAmount,
                ])
                ->atPath($path)
                ->addViolation()
            ;
        }
    }
}