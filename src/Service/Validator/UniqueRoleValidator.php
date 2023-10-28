<?php

namespace App\Service\Validator;

use App\Library\Constraint\UniqueRole;
use App\Model\Entity\Role;
use App\Model\Repository\RoleRepositoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
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
            throw new UnexpectedTypeException($constraint, UniqueRole::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedTypeException($value, 'object');
        }

        $roleData = $value;
        $label = $this->propertyAccessor->getValue($roleData, $constraint->labelProperty);

        if ($label !== null && !is_string($label))
        {
            throw new UnexpectedTypeException($label, 'string');
        }

        $role = $this->propertyAccessor->getValue($roleData, $constraint->roleProperty);

        if ($role !== null && !$role instanceof Role)
        {
            throw new UnexpectedTypeException($role, Role::class);
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

        $id = $role?->getId();
        $existingId = $existingRole->getId();

        if ($id === null || $id->toRfc4122() !== $existingId->toRfc4122())
        {
            $message = $this->translator->trans($constraint->message, [], 'validators');

            $this->context
                ->buildViolation($message)
                ->atPath($constraint->labelProperty)
                ->addViolation()
            ;
        }
    }
}