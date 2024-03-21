<?php

namespace App\Service\Security\Authorization;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Grants administration access to admins or camp date guides.
 */
class AdminAccessVoter extends Voter
{
    const ADMIN_ACCESS = 'admin_access';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute === self::ADMIN_ACCESS && $subject === null)
        {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return $this->security->isGranted('any_admin_permission') || $this->security->isGranted('camp_date_guide');
    }
}