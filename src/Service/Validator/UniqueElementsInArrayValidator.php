<?php

namespace App\Service\Validator;

use App\Library\Constraint\UniqueElementsInArray;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\ValidatorException;

/**
 * Validates that an array contains unique objects or sub-arrays.
 */
class UniqueElementsInArrayValidator extends ConstraintValidator
{
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueElementsInArray)
        {
            throw new UnexpectedTypeException($constraint, UniqueElementsInArray::class);
        }

        if (!is_array($value))
        {
            throw new UnexpectedTypeException($value, 'array');
        }

        $elements = $value;

        if (empty($constraint->fields))
        {
            throw new ValidatorException(
                sprintf('The "fields" option (array) must not be empty in "%s".', $constraint::class)
            );
        }

        $normalizedElements = [];
        $constraint->fields = array_unique($constraint->fields);

        foreach ($elements as $element)
        {
            $normalizedElement = [];

            foreach ($constraint->fields as $field)
            {
                $value = $this->propertyAccessor->getValue($element, $field);
                $normalizedElement[$field] = $value;
            }

            if (in_array($normalizedElement, $normalizedElements))
            {
                $this->context
                    ->buildViolation($constraint->message)
                    ->addViolation()
                ;

                return;
            }

            $normalizedElements[] = $normalizedElement;
        }
    }
}