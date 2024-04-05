<?php

namespace App\Service\Validator;

use App\Library\Constraint\ApplicationCampersCount;
use App\Model\Entity\CampDate;
use App\Model\Repository\CampDateRepositoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates that the given number of campers can apply to a camp date.
 */
class ApplicationCampersCountValidator extends ConstraintValidator
{
    private CampDateRepositoryInterface $campDateRepository;

    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(CampDateRepositoryInterface $campDateRepository, PropertyAccessorInterface $propertyAccessor)
    {
        $this->campDateRepository = $campDateRepository;
        $this->propertyAccessor = $propertyAccessor;
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ApplicationCampersCount)
        {
            throw new UnexpectedTypeException($constraint, ApplicationCampersCount::class);
        }

        $applicationData = $value;

        if (!is_object($applicationData))
        {
            throw new UnexpectedTypeException($applicationData, 'object');
        }

        $campDate = $this->propertyAccessor->getValue($applicationData, $constraint->campDateProperty);

        if (!$campDate instanceof CampDate)
        {
            throw new UnexpectedTypeException($campDate, CampDate::class);
        }

        $applicationCampersData = $this->propertyAccessor->getValue($applicationData, $constraint->applicationCampersDataProperty);
        $numberOfNewApplicationCampers = count($applicationCampersData);
        $capacityExceededBy = $this->campDateRepository->willNumberOfNewCampersExceedCampDateCapacity($campDate, $numberOfNewApplicationCampers);

        if ($capacityExceededBy > 0)
        {
            $this->context
                ->buildViolation($constraint->message, ['capacity_exceeded_by' => $capacityExceededBy])
                ->addViolation()
            ;
        }
    }
}