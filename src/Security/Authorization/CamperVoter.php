<?php

namespace App\Security\Authorization;

use App\Model\Entity\Camper;
use App\Model\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Votes on user camper access.
 */
class CamperVoter extends Voter
{
    const READ = 'camper_read';
    const UPDATE = 'camper_update';
    const DELETE = 'camper_delete';

    /**
     * {@inheritdoc}
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::READ, self::UPDATE, self::DELETE]))
        {
            return false;
        }

        if (!$subject instanceof Camper)
        {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User)
        {
            return false;
        }

        /** @var Camper $camper */
        $camper = $subject;

        return $camper->getUser() === $user;
    }
}