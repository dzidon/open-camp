<?php

namespace App\Service\Validator;

use App\Library\Constraint\UniqueTripLocation;
use App\Model\Entity\TripLocation;
use App\Model\Entity\TripLocationPath;
use App\Model\Repository\TripLocationRepositoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates that the entered name is not yet assigned to any trip location within its path.
 */
class UniqueTripLocationValidator extends ConstraintValidator
{
    private PropertyAccessorInterface $propertyAccessor;
    private TripLocationRepositoryInterface $tripLocationRepository;

    public function __construct(PropertyAccessorInterface       $propertyAccessor,
                                TripLocationRepositoryInterface $tripLocationRepository)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->tripLocationRepository = $tripLocationRepository;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueTripLocation)
        {
            throw new UnexpectedTypeException($constraint, UniqueTripLocation::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedTypeException($value, 'object');
        }

        $tripLocationData = $value;
        $tripLocationPath = $this->propertyAccessor->getValue($tripLocationData, $constraint->tripLocationPathProperty);

        if ($tripLocationPath !== null && !$tripLocationPath instanceof TripLocationPath)
        {
            throw new UnexpectedTypeException($tripLocationPath, TripLocationPath::class);
        }

        $name = $this->propertyAccessor->getValue($tripLocationData, $constraint->nameProperty);

        if ($name !== null && !is_string($name))
        {
            throw new UnexpectedTypeException($name, 'string');
        }

        $tripLocation = $this->propertyAccessor->getValue($tripLocationData, $constraint->tripLocationProperty);

        if ($tripLocation !== null && !$tripLocation instanceof TripLocation)
        {
            throw new UnexpectedTypeException($tripLocation, TripLocation::class);
        }

        if ($tripLocationPath === null || $name === null || $name === '')
        {
            return;
        }

        $id = $tripLocation?->getId();
        $existingTripLocations = $this->tripLocationRepository->findByTripLocationPath($tripLocationPath);

        foreach ($existingTripLocations as $existingTripLocation)
        {
            $existingId = $existingTripLocation->getId();
            $existingName = $existingTripLocation->getName();

            if (($id === null || $existingId->toRfc4122() !== $id->toRfc4122()) && $existingName === $name)
            {
                $this->context
                    ->buildViolation($constraint->message)
                    ->atPath($constraint->nameProperty)
                    ->addViolation()
                ;

                return;
            }
        }
    }
}