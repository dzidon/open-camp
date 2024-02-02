<?php

namespace App\Service\Validator;

use App\Library\Constraint\ApplicationCampersCount;
use App\Model\Entity\CampDate;
use App\Model\Repository\ApplicationCamperRepositoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates that the given number of campers can apply to a camp date.
 */
class ApplicationCampersCountValidator extends ConstraintValidator
{
    private ApplicationCamperRepositoryInterface $applicationCamperRepository;

    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(ApplicationCamperRepositoryInterface $applicationCamperRepository,
                                PropertyAccessorInterface            $propertyAccessor)
    {
        $this->applicationCamperRepository = $applicationCamperRepository;
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

        if ($campDate === null)
        {
            return;
        }

        if (!$campDate instanceof CampDate)
        {
            throw new UnexpectedTypeException($campDate, CampDate::class);
        }

        if ($campDate->isOpenAboveCapacity())
        {
            return;
        }

        $campDateCapacity = $campDate->getCapacity();
        $applicationCampersData = $this->propertyAccessor->getValue($applicationData, $constraint->applicationCampersDataProperty);

        if (!is_iterable($applicationCampersData))
        {
            throw new UnexpectedTypeException($applicationCampersData, 'iterable');
        }

        $applicationCampers = $this->applicationCamperRepository->findThoseThatOccupySlotsInCampDate($campDate);
        $numberOfApplicationCampers = count($applicationCampers);
        $applicationCamperIds = [];

        foreach ($applicationCampers as $applicationCamper)
        {
            $idString = $applicationCamper
                ->getId()
                ->toRfc4122()
            ;

            $applicationCamperIds[$idString] = $idString;
        }

        foreach ($applicationCampersData as $applicationCamperData)
        {
            if (!is_object($applicationCamperData))
            {
                throw new UnexpectedTypeException($applicationCamperData, 'object');
            }

            $applicationCamperId = $this->propertyAccessor->getValue($applicationCamperData, $constraint->applicationCamperIdProperty);

            if ($applicationCamperId !== null && !$applicationCamperId instanceof UuidV4)
            {
                throw new UnexpectedTypeException($applicationCamperId, UuidV4::class);
            }

            $applicationCamperIdString = $applicationCamperId?->toRfc4122();

            if ($applicationCamperIdString === null || !array_key_exists($applicationCamperIdString, $applicationCamperIds))
            {
                $numberOfApplicationCampers++;
            }
        }

        if ($numberOfApplicationCampers > $campDateCapacity)
        {
            $exceededOver = $numberOfApplicationCampers - $campDateCapacity;

            $this->context
                ->buildViolation($constraint->message, ['capacity_exceeded_over' => $exceededOver])
                ->addViolation()
            ;
        }
    }
}