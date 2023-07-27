<?php

namespace App\Validator\Validator;

use App\Model\Repository\RoleRepositoryInterface;
use App\Validator\Constraint\UniqueRole;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Validates that the entered label is not yet assigned to any role.
 */
class UniqueRoleValidator extends ConstraintValidator
{
    private PropertyAccessorInterface $propertyAccessor;
    private RoleRepositoryInterface $roleRepository;
    private TranslatorInterface $translator;

    public function __construct(PropertyAccessorInterface $propertyAccessor,
                                RoleRepositoryInterface   $roleRepository,
                                TranslatorInterface       $translator)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->roleRepository = $roleRepository;
        $this->translator = $translator;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueRole)
        {
            throw new UnexpectedValueException($constraint, UniqueRole::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedValueException($value, 'object');
        }

        $roleData = $value;
        $label = $this->propertyAccessor->getValue($roleData, $constraint->labelProperty);

        if ($label !== null && !is_string($label))
        {
            throw new UnexpectedValueException($label, 'string');
        }

        $id = $this->propertyAccessor->getValue($roleData, $constraint->idProperty);

        if ($id !== null && !is_int($id))
        {
            throw new UnexpectedValueException($id, 'int');
        }

        if ($label === null || $label === '')
        {
            return;
        }

        $existingRole = $this->roleRepository->findOneByLabel($label);

        if ($existingRole === null)
        {
            return;
        }

        $existingId = $existingRole->getId();

        if ($id !== $existingId)
        {
            $message = $this->translator->trans($constraint->message);

            $this->context
                ->buildViolation($message)
                ->atPath($constraint->labelProperty)
                ->addViolation()
            ;
        }
    }
}