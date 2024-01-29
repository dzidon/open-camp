<?php

namespace App\Service\Validator;

use App\Library\Constraint\UniqueUserUrlName;
use App\Model\Entity\User;
use App\Model\Repository\UserRepositoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Validates that the entered url name is not yet assigned to a user.
 */
class UniqueUserUrlNameValidator extends ConstraintValidator
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
        if (!$constraint instanceof UniqueUserUrlName)
        {
            throw new UnexpectedTypeException($constraint, UniqueUserUrlName::class);
        }

        if (!is_object($value))
        {
            throw new UnexpectedTypeException($value, 'object');
        }

        $userData = $value;
        $urlName = $this->propertyAccessor->getValue($userData, $constraint->urlNameProperty);

        if ($urlName !== null && !is_string($urlName))
        {
            throw new UnexpectedTypeException($urlName, 'string');
        }

        $user = $this->propertyAccessor->getValue($userData, $constraint->userProperty);

        if ($user !== null && !$user instanceof User)
        {
            throw new UnexpectedTypeException($user, User::class);
        }

        if ($urlName === null || $urlName === '')
        {
            return;
        }

        $existingUser = $this->userRepository->findOneByUrlName($urlName);

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
                ->atPath($constraint->urlNameProperty)
                ->addViolation()
            ;
        }
    }
}