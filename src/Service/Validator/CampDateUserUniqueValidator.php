<?php

namespace App\Service\Validator;

use App\Library\Constraint\CampDateUserUnique;
use App\Model\Entity\User;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Validates that the entered user is assigned to the given camp date only once.
 */
class CampDateUserUniqueValidator extends ConstraintValidator
{
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    public function validate(mixed $value, Constraint $constraint)
    {
        if (!$constraint instanceof CampDateUserUnique)
        {
            throw new UnexpectedTypeException($constraint, CampDateUserUnique::class);
        }

        $campDateUsersData = $value;

        if (!is_array($campDateUsersData))
        {
            throw new UnexpectedTypeException($campDateUsersData, 'array');
        }

        $usedIds = [];

        foreach ($campDateUsersData as $campDateUserData)
        {
            $user = $this->propertyAccessor->getValue($campDateUserData, $constraint->userProperty);

            if ($user === null)
            {
                continue;
            }

            if (!$user instanceof User)
            {
                throw new UnexpectedTypeException($user, User::class);
            }

            $userId = $user
                ->getId()
                ->toRfc4122()
            ;

            if (array_key_exists($userId, $usedIds))
            {
                $userLabel = $user->getEmail();
                $nameFull = $user->getNameFull();

                if ($nameFull !== null)
                {
                    $userLabel .= sprintf(' (%s)', $nameFull);
                }

                $this->context
                    ->buildViolation($constraint->message, [
                        'user' => $userLabel,
                    ])
                    ->addViolation()
                ;

                return;
            }

            $usedIds[$userId] = $userId;
        }
    }
}