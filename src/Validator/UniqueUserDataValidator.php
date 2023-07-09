<?php

namespace App\Validator;

use App\Repository\UserRepositoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Validates that the entered e-mail is not yet registered.
 */
class UniqueUserDataValidator extends ConstraintValidator
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
        if (!is_object($value))
        {
            throw new UnexpectedValueException($value, 'object');
        }

        $userData = $value;

        if (!$constraint instanceof UniqueUserData)
        {
            throw new UnexpectedValueException($constraint, UniqueUserData::class);
        }

        $email = $this->propertyAccessor->getValue($userData, $constraint->emailProperty);
        $id = $this->propertyAccessor->getValue($userData, $constraint->idProperty);
        $existingUser = $this->userRepository->findOneByEmail($email);

        if ($existingUser === null)
        {
            return;
        }

        $existingEmail = $existingUser->getEmail();
        $existingId = $existingUser->getId();

        if ($email === $existingEmail && $id !== $existingId)
        {
            $message = $this->translator->trans($constraint->message);

            $this->context
                ->buildViolation($message)
                ->atPath('email')
                ->addViolation()
            ;
        }
    }
}