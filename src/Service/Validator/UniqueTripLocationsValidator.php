<?php

namespace App\Service\Validator;

use App\Library\Constraint\UniqueTripLocations;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * Validates that the entered name is not yet assigned to any trip location within its path. There is no validation
 * against the database.
 */
class UniqueTripLocationsValidator extends ConstraintValidator
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
        if (!$constraint instanceof UniqueTripLocations)
        {
            throw new UnexpectedValueException($constraint, UniqueTripLocations::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedValueException($value, 'object');
        }

        $tripLocationPathCreationData = $value;
        $tripLocationsData = $this->propertyAccessor->getValue($tripLocationPathCreationData, $constraint->tripLocationsDataProperty);

        if (!is_iterable($tripLocationsData))
        {
            throw new UnexpectedValueException($tripLocationsData, 'iterable');
        }

        $usedNames = [];

        foreach ($tripLocationsData as $key => $tripLocationData)
        {
            if (!is_object($tripLocationData))
            {
                throw new UnexpectedValueException($tripLocationData, 'object');
            }

            $name = $this->propertyAccessor->getValue($tripLocationData, $constraint->nameProperty);

            if ($name !== null && !is_string($name))
            {
                throw new UnexpectedValueException($name, 'string');
            }

            if ($name === null)
            {
                continue;
            }

            if (array_key_exists($name, $usedNames))
            {
                $message = $this->translator->trans($constraint->message, [], 'validators');

                $this->context
                    ->buildViolation($message)
                    ->atPath(sprintf('%s[%s].%s', $constraint->tripLocationsDataProperty, $key, $constraint->nameProperty))
                    ->addViolation()
                ;
            }
            else
            {
                $usedNames[$name] = true;
            }
        }
    }
}