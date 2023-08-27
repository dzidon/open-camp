<?php

namespace App\Service\Validator;

use App\Library\Constraint\CampDateIntervals;
use App\Library\DateTime\IntervalsCollision;
use DateTimeInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * Validates that the entered collection of camp date intervals does not contain a collision. There is no validation
 * against the database.
 */
class CampDateIntervalsValidator extends ConstraintValidator
{
    private PropertyAccessorInterface $propertyAccessor;
    private TranslatorInterface $translator;

    public function __construct(PropertyAccessorInterface $propertyAccessor, TranslatorInterface $translator)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->translator = $translator;
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof CampDateIntervals)
        {
            throw new UnexpectedValueException($constraint, CampDateIntervals::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedValueException($value, 'object');
        }

        $campCreationData = $value;
        $campDatesData = $this->propertyAccessor->getValue($campCreationData, $constraint->campDatesDataProperty);

        if (!is_iterable($campDatesData))
        {
            throw new UnexpectedValueException($campDatesData, 'iterable');
        }

        foreach ($campDatesData as $key => $campDateData)
        {
            $this->assertCampDateData($campDateData, $constraint);

            $startAt = $this->propertyAccessor->getValue($campDateData, $constraint->startAtProperty);
            $endAt = $this->propertyAccessor->getValue($campDateData, $constraint->endAtProperty);

            if ($startAt === null || $endAt === null)
            {
                continue;
            }

            foreach ($campDatesData as $otherCampDateData)
            {
                if ($otherCampDateData === $campDateData)
                {
                    continue;
                }

                $this->assertCampDateData($otherCampDateData, $constraint);

                $otherStartAt = $this->propertyAccessor->getValue($otherCampDateData, $constraint->startAtProperty);
                $otherEndAt = $this->propertyAccessor->getValue($otherCampDateData, $constraint->endAtProperty);

                if ($otherStartAt === null || $otherEndAt === null)
                {
                    continue;
                }

                $collision = new IntervalsCollision($startAt, $endAt, $otherStartAt, $otherEndAt);

                if ($collision->isFound())
                {
                    $message = $this->translator->trans($constraint->message, [], 'validators');

                    $this->context
                        ->buildViolation($message)
                        ->atPath(sprintf('%s[%s].%s', $constraint->campDatesDataProperty, $key, $constraint->startAtProperty))
                        ->addViolation()
                    ;

                    $this->context
                        ->buildViolation($message)
                        ->atPath(sprintf('%s[%s].%s', $constraint->campDatesDataProperty, $key, $constraint->endAtProperty))
                        ->addViolation()
                    ;
                }
            }
        }
    }

    private function assertCampDateData(mixed $campDateData, CampDateIntervals $constraint): void
    {
        if (!is_object($campDateData))
        {
            throw new UnexpectedValueException($campDateData, 'object');
        }

        $startAt = $this->propertyAccessor->getValue($campDateData, $constraint->startAtProperty);

        if ($startAt !== null && !$startAt instanceof DateTimeInterface)
        {
            throw new UnexpectedValueException($startAt, DateTimeInterface::class);
        }

        $endAt = $this->propertyAccessor->getValue($campDateData, $constraint->endAtProperty);

        if ($endAt !== null && !$endAt instanceof DateTimeInterface)
        {
            throw new UnexpectedValueException($endAt, DateTimeInterface::class);
        }
    }
}