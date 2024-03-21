<?php

namespace App\Service\Security\Authorization;

use App\Model\Entity\Application;
use App\Model\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Votes on user profile application access.
 */
class ProfileApplicationVoter extends Voter
{
    const READ = 'application_read';

    /**
     * {@inheritdoc}
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute !== self::READ)
        {
            return false;
        }

        if (!$subject instanceof Application)
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

        /** @var Application $application */
        $application = $subject;

        return $application->getUser() === $user;
    }
}