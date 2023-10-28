<?php

namespace App\Service\Validator;

use App\Library\Constraint\UniqueTripLocationPath;
use App\Model\Entity\TripLocationPath;
use App\Model\Repository\TripLocationPathRepositoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Validates that the entered name is not yet assigned to any trip location path.
 */
class UniqueTripLocationPathValidator extends ConstraintValidator
{
    private PropertyAccessorInterface $propertyAccessor;
    private TripLocationPathRepositoryInterface $tripLocationPathRepository;
    private TranslatorInterface $translator;

    public function __construct(PropertyAccessorInterface           $propertyAccessor,
                                TripLocationPathRepositoryInterface $tripLocationPathRepository,
                                TranslatorInterface                 $translator)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->tripLocationPathRepository = $tripLocationPathRepository;
        $this->translator = $translator;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueTripLocationPath)
        {
            throw new UnexpectedTypeException($constraint, UniqueTripLocationPath::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedTypeException($value, 'object');
        }

        $tripLocationPathData = $value;
        $name = $this->propertyAccessor->getValue($tripLocationPathData, $constraint->nameProperty);

        if ($name !== null && !is_string($name))
        {
            throw new UnexpectedTypeException($name, 'string');
        }

        $tripLocationPath = $this->propertyAccessor->getValue($tripLocationPathData, $constraint->tripLocationPathProperty);

        if ($tripLocationPath !== null && !$tripLocationPath instanceof TripLocationPath)
        {
            throw new UnexpectedTypeException($tripLocationPath, TripLocationPath::class);
        }

        if ($name === null || $name === '')
        {
            return;
        }

        $existingTripLocationPath = $this->tripLocationPathRepository->findOneByName($name);

        if ($existingTripLocationPath === null)
        {
            return;
        }

        $id = $tripLocationPath?->getId();
        $existingId = $existingTripLocationPath->getId();

        if ($id === null || $id->toRfc4122() !== $existingId->toRfc4122())
        {
            $message = $this->translator->trans($constraint->message, [], 'validators');

            $this->context
                ->buildViolation($message)
                ->atPath($constraint->nameProperty)
                ->addViolation()
            ;
        }
    }
}