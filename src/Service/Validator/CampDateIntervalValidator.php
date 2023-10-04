<?php

namespace App\Service\Validator;

use App\Library\Constraint\CampDateInterval;
use App\Model\Entity\Camp;
use App\Model\Repository\CampDateRepositoryInterface;
use DateTimeInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Validates that the entered datetime interval is empty. Validation is performed against the database.
 */
class CampDateIntervalValidator extends ConstraintValidator
{
    private PropertyAccessorInterface $propertyAccessor;
    private CampDateRepositoryInterface $campDateRepository;
    private TranslatorInterface $translator;

    public function __construct(PropertyAccessorInterface   $propertyAccessor,
                                CampDateRepositoryInterface $campDateRepository,
                                TranslatorInterface         $translator)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->campDateRepository = $campDateRepository;
        $this->translator = $translator;
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

        $id = $this->propertyAccessor->getValue($campDateData, $constraint->idProperty);

        if ($id !== null && !$id instanceof UuidV4)
        {
            throw new UnexpectedTypeException($id, UuidV4::class);
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

        $collidingDates = $this->campDateRepository->findThoseThatCollideWithInterval($camp, $startAt, $endAt);

        foreach ($collidingDates as $collidingDate)
        {
            if ($id === null || $id->toRfc4122() !== $collidingDate->getId()->toRfc4122())
            {
                $message = $this->translator->trans($constraint->message, [], 'validators');

                $this->context
                    ->buildViolation($message)
                    ->atPath($constraint->startAtProperty)
                    ->addViolation()
                ;

                $this->context
                    ->buildViolation($message)
                    ->atPath($constraint->endAtProperty)
                    ->addViolation()
                ;

                break;
            }
        }
    }
}