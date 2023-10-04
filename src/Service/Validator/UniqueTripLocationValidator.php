<?php

namespace App\Service\Validator;

use App\Library\Constraint\UniqueTripLocation;
use App\Model\Entity\TripLocationPath;
use App\Model\Repository\TripLocationRepositoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Validates that the entered name is not yet assigned to any trip location within its path.
 */
class UniqueTripLocationValidator extends ConstraintValidator
{
    private PropertyAccessorInterface $propertyAccessor;
    private TripLocationRepositoryInterface $tripLocationRepository;
    private TranslatorInterface $translator;

    public function __construct(PropertyAccessorInterface       $propertyAccessor,
                                TripLocationRepositoryInterface $tripLocationRepository,
                                TranslatorInterface             $translator)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->tripLocationRepository = $tripLocationRepository;
        $this->translator = $translator;
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

        $id = $this->propertyAccessor->getValue($tripLocationData, $constraint->idProperty);

        if ($id !== null && !$id instanceof UuidV4)
        {
            throw new UnexpectedTypeException($id, UuidV4::class);
        }

        if ($tripLocationPath === null || $name === null || $name === '')
        {
            return;
        }

        $existingTripLocations = $this->tripLocationRepository->findByTripLocationPath($tripLocationPath);

        foreach ($existingTripLocations as $existingTripLocation)
        {
            $existingId = $existingTripLocation->getId();

            if ($id !== null && $existingId->toRfc4122() === $id->toRfc4122())
            {
                continue;
            }

            $existingName = $existingTripLocation->getName();

            if ($existingName === $name)
            {
                $message = $this->translator->trans($constraint->message, [], 'validators');

                $this->context
                    ->buildViolation($message)
                    ->atPath($constraint->nameProperty)
                    ->addViolation()
                ;

                return;
            }
        }
    }
}