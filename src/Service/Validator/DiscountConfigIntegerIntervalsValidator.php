<?php

namespace App\Service\Validator;

use App\Library\Constraint\DiscountConfigIntegerIntervals;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates that the entered discount config integer intervals do not collide.
 */
class DiscountConfigIntegerIntervalsValidator extends ConstraintValidator
{
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof DiscountConfigIntegerIntervals)
        {
            throw new UnexpectedTypeException($constraint, DiscountConfigIntegerIntervals::class);
        }

        if (!is_iterable($value))
        {
            throw new UnexpectedTypeException($value, 'iterable');
        }

        $dataIterable = $value;

        foreach ($dataIterable as $data)
        {
            $from = $this->propertyAccessor->getValue($data, $constraint->fromProperty);
            $to = $this->propertyAccessor->getValue($data, $constraint->toProperty);

            if ($from === null && $to === null)
            {
                continue;
            }

            foreach ($dataIterable as $index => $otherData)
            {
                if ($data === $otherData)
                {
                    continue;
                }

                $otherFrom = $this->propertyAccessor->getValue($otherData, $constraint->fromProperty);
                $otherTo = $this->propertyAccessor->getValue($otherData, $constraint->toProperty);

                if ($otherFrom === null && $otherTo === null)
                {
                    continue;
                }

                if (($from === null || $otherTo   === null || $from <= $otherTo) &&
                    ($to   === null || $otherFrom === null || $to   >= $otherFrom))
                {
                    $this->context
                        ->buildViolation($constraint->message)
                        ->atPath("[$index].$constraint->fromProperty")
                        ->addViolation()
                    ;

                    $this->context
                        ->buildViolation($constraint->message)
                        ->atPath("[$index].$constraint->toProperty")
                        ->addViolation()
                    ;
                }
            }
        }
    }
}