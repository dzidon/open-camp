<?php

namespace App\Validator\Validator;

use App\Model\Repository\UserRepositoryInterface;
use App\Validator\Constraint\UniqueUser;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Validates that the entered e-mail is not yet registered.
 */
class UniqueUserValidator extends ConstraintValidator
{
    private PropertyAccessorInterface $propertyAccessor;
    private UserRepositoryInterface $userRepository;
    private TranslatorInterface $translator;

    public function __construct(PropertyAccessorInterface $propertyAccessor,
                                UserRepositoryInterface   $userRepository,
                                TranslatorInterface       $translator)
    {
        $this->propertyAccessor = $propertyAccessor;
        $this->userRepository = $userRepository;
        $this->translator = $translator;
    }

    /**
     * @inheritDoc
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueUser)
        {
            throw new UnexpectedValueException($constraint, UniqueUser::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedValueException($value, 'object');
        }

        $userData = $value;
        $email = $this->propertyAccessor->getValue($userData, $constraint->emailProperty);

        if ($email !== null && !is_string($email))
        {
            throw new UnexpectedValueException($email, 'string');
        }

        $id = $this->propertyAccessor->getValue($userData, $constraint->idProperty);

        if ($id !== null && !is_int($id))
        {
            throw new UnexpectedValueException($id, 'int');
        }

        if ($email === null || $email === '')
        {
            return;
        }

        $existingUser = $this->userRepository->findOneByEmail($email);

        if ($existingUser === null)
        {
            return;
        }

        $existingId = $existingUser->getId();

        if ($id !== $existingId)
        {
            $message = $this->translator->trans($constraint->message);

            $this->context
                ->buildViolation($message)
                ->atPath($constraint->emailProperty)
                ->addViolation()
            ;
        }
    }
}