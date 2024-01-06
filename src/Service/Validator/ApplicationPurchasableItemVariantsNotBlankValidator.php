<?php

namespace App\Service\Validator;

use App\Library\Constraint\ApplicationPurchasableItemVariantsNotBlank;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

class ApplicationPurchasableItemVariantsNotBlankValidator extends ConstraintValidator
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
        if (!$constraint instanceof ApplicationPurchasableItemVariantsNotBlank)
        {
            throw new UnexpectedTypeException($constraint, ApplicationPurchasableItemVariantsNotBlank::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedTypeException($value, 'object');
        }

        $applicationPurchasableItemInstanceData = $value;
        $amount = $this->propertyAccessor->getValue($applicationPurchasableItemInstanceData, $constraint->amountProperty);

        if (!is_int($amount))
        {
            throw new UnexpectedTypeException($amount, 'int');
        }

        if ($amount <= 0)
        {
            return;
        }

        $applicationPurchasableItemVariantsData = $this->propertyAccessor->getValue($applicationPurchasableItemInstanceData, $constraint->applicationPurchasableItemVariantsDataProperty);

        foreach ($applicationPurchasableItemVariantsData as $index => $applicationPurchasableItemVariantData)
        {
            $value = $this->propertyAccessor->getValue($applicationPurchasableItemVariantData, $constraint->applicationPurchasableItemVariantDataValueProperty);

            if ($value === null || $value === '')
            {
                $message = $this->translator->trans($constraint->message, [], 'validators');
                $path = sprintf('%s[%s].%s', $constraint->applicationPurchasableItemVariantsDataProperty, $index, $constraint->applicationPurchasableItemVariantDataValueProperty);

                $this->context
                    ->buildViolation($message)
                    ->atPath($path)
                    ->addViolation()
                ;
            }
        }
    }
}