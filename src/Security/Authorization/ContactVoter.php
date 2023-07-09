<?php

namespace App\Security\Authorization;

use App\Model\Entity\Contact;
use App\Model\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Votes on user contact access.
 */
class ContactVoter extends Voter
{
    const READ = 'contact_read';
    const UPDATE = 'contact_update';
    const DELETE = 'contact_delete';

    /**
     * {@inheritdoc}
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::READ, self::UPDATE, self::DELETE]))
        {
            return false;
        }

        if (!$subject instanceof Contact)
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

        /** @var Contact $contact */
        $contact = $subject;

        return $contact->getUser() === $user;
    }
}