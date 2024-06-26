<?php

namespace App\Service\Validator;

use App\Library\Constraint\UniqueFormField;
use App\Model\Entity\FormField;
use App\Model\Repository\FormFieldRepositoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates that the entered name is not yet assigned to form field.
 */
class UniqueFormFieldValidator extends ConstraintValidator
{
    private PropertyAccessorInterface $propertyAccessor;
    private FormFieldRepositoryInterface $formFieldRepository;

    public function __construct(PropertyAccessorInterface    $propertyAccessor,
                                FormFieldRepositoryInterface $formFieldRepository)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->formFieldRepository = $formFieldRepository;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueFormField)
        {
            throw new UnexpectedTypeException($constraint, UniqueFormField::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedTypeException($value, 'object');
        }

        $formFieldData = $value;
        $name = $this->propertyAccessor->getValue($formFieldData, $constraint->nameProperty);

        if ($name !== null && !is_string($name))
        {
            throw new UnexpectedTypeException($name, 'string');
        }

        $formField = $this->propertyAccessor->getValue($formFieldData, $constraint->formFieldProperty);

        if ($formField !== null && !$formField instanceof FormField)
        {
            throw new UnexpectedTypeException($formField, FormField::class);
        }

        if ($name === null || $name === '')
        {
            return;
        }

        $existingFormField = $this->formFieldRepository->findOneByName($name);

        if ($existingFormField === null)
        {
            return;
        }

        $id = $formField?->getId();
        $existingId = $existingFormField->getId();

        if ($id === null || $id->toRfc4122() !== $existingId->toRfc4122())
        {
            $this->context
                ->buildViolation($constraint->message)
                ->atPath($constraint->nameProperty)
                ->addViolation()
            ;
        }
    }
}