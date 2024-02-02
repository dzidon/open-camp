<?php

namespace App\Service\Validator;

use App\Library\Constraint\CampDateInterval;
use App\Model\Entity\Camp;
use App\Model\Entity\CampDate;
use App\Model\Repository\CampDateRepositoryInterface;
use DateTimeInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates that the entered datetime interval is empty. Validation is performed against the database.
 */
class CampDateIntervalValidator extends ConstraintValidator
{
    private PropertyAccessorInterface $propertyAccessor;
    private CampDateRepositoryInterface $campDateRepository;

    public function __construct(PropertyAccessorInterface   $propertyAccessor,
                                CampDateRepositoryInterface $campDateRepository)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->campDateRepository = $campDateRepository;
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof CampDateInterval)
        {
            throw new UnexpectedTypeException($constraint, CampDateInterval::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedTypeException($value, 'object');
        }

        $campDateData = $value;
        $camp = $this->propertyAccessor->getValue($campDateData, $constraint->campProperty);

        if (!$camp instanceof Camp)
        {
            throw new UnexpectedTypeException($camp, Camp::class);
        }

        $campDate = $this->propertyAccessor->getValue($campDateData, $constraint->campDateProperty);

        if ($campDate !== null && !$campDate instanceof CampDate)
        {
            throw new UnexpectedTypeException($campDate, CampDate::class);
        }

        $startAt = $this->propertyAccessor->getValue($campDateData, $constraint->startAtProperty);

        if ($startAt !== null && !$startAt instanceof DateTimeInterface)
        {
            throw new UnexpectedTypeException($startAt, DateTimeInterface::class);
        }

        $endAt = $this->propertyAccessor->getValue($campDateData, $constraint->endAtProperty);

        if ($endAt !== null && !$endAt instanceof DateTimeInterface)
        {
            throw new UnexpectedTypeException($endAt, DateTimeInterface::class);
        }

        if ($startAt === null || $endAt === null)
        {
            return;
        }

        $id = $campDate?->getId();
        $collidingDates = $this->campDateRepository->findThoseThatCollideWithInterval($camp, $startAt, $endAt);

        foreach ($collidingDates as $collidingDate)
        {
            if ($id === null || $id->toRfc4122() !== $collidingDate->getId()->toRfc4122())
            {
                $this->context
                    ->buildViolation($constraint->message)
                    ->atPath($constraint->startAtProperty)
                    ->addViolation()
                ;

                $this->context
                    ->buildViolation($constraint->message)
                    ->atPath($constraint->endAtProperty)
                    ->addViolation()
                ;

                break;
            }
        }
    }
}