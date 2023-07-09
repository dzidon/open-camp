<?php

namespace App\Security\Authorization;

use App\Model\Entity\User;
use App\Model\Repository\PermissionRepositoryInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Checks if user's role has a permission that corresponds to one of the given attributes.
 */
class PermissionVoter implements VoterInterface
{
    private ?array $permissionNames = null;

    private PermissionRepositoryInterface $permissionRepository;

    public function __construct(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * @inheritDoc
     */
    public function vote(TokenInterface $token, mixed $subject, array $attributes): int
    {
        $user = $token->getUser();
        $vote = self::ACCESS_ABSTAIN;

        if (!$user instanceof User)
        {
            return $vote;
        }

        $this->loadPermissionNamesIfNotYetLoaded();

        foreach ($attributes as $attribute)
        {
            if ($attribute === '_any_permission')
            {
                $vote = self::ACCESS_DENIED;

                if (!empty($user->getRole()?->getPermissions()))
                {
                    return self::ACCESS_GRANTED;
                }
            }
            else if (in_array($attribute, $this->permissionNames))
            {
                $vote = self::ACCESS_DENIED;

                if ($user->hasPermission($attribute))
                {
                    return self::ACCESS_GRANTED;
                }
            }
        }

        return $vote;
    }

    /**
     * Loads permission names only if they haven't been loaded yet.
     *
     * @return void
     */
    private function loadPermissionNamesIfNotYetLoaded(): void
    {
        if ($this->permissionNames !== null)
        {
            return;
        }

        $this->permissionNames = [];

        foreach ($this->permissionRepository->findAll() as $permission)
        {
            $this->permissionNames[] = $permission->getName();
        }
    }
}