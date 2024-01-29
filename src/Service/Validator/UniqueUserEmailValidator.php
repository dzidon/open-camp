<?php

namespace App\Service\Validator;

use App\Library\Constraint\UniqueUserEmail;
use App\Model\Entity\User;
use App\Model\Repository\UserRepositoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Validates that the entered e-mail is not yet registered.
 */
class UniqueUserEmailValidator extends ConstraintValidator
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
        if (!$constraint instanceof UniqueUserEmail)
        {
            throw new UnexpectedTypeException($constraint, UniqueUserEmail::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedTypeException($value, 'object');
        }

        $userData = $value;
        $email = $this->propertyAccessor->getValue($userData, $constraint->emailProperty);

        if ($email !== null && !is_string($email))
        {
            throw new UnexpectedTypeException($email, 'string');
        }

        $user = $this->propertyAccessor->getValue($userData, $constraint->userProperty);

        if ($user !== null && !$user instanceof User)
        {
            throw new UnexpectedTypeException($user, User::class);
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

        $id = $user?->getId();
        $existingId = $existingUser->getId();

        if ($id === null || $id->toRfc4122() !== $existingId->toRfc4122())
        {
            $message = $this->translator->trans($constraint->message, [], 'validators');

            $this->context
                ->buildViolation($message)
                ->atPath($constraint->emailProperty)
                ->addViolation()
            ;
        }
    }
}