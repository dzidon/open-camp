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
        if (!is_object($value))
        {
            throw new UnexpectedValueException($value, 'object');
        }

        $roleData = $value;

        if (!$constraint instanceof UniqueRole)
        {
            throw new UnexpectedValueException($constraint, UniqueRole::class);
        }

        $label = $this->propertyAccessor->getValue($roleData, $constraint->labelProperty);

        if ($label === null || $label === '')
        {
            return;
        }

        $id = $this->propertyAccessor->getValue($roleData, $constraint->idProperty);
        $existingRole = $this->roleRepository->findOneByLabel($label);

        if ($existingRole === null)
        {
            return;
        }

        $existingLabel = $existingRole->getLabel();
        $existingId = $existingRole->getId();

        if ($label === $existingLabel && $id !== $existingId)
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