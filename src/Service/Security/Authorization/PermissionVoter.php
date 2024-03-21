<?php

namespace App\Service\Security\Authorization;

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

    private ?array $permissionGroupedNames = null;

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

        $this->loadPermissionAndGroupNamesIfNotYetLoaded();

        foreach ($attributes as $permissionName)
        {
            if (!is_string($permissionName))
            {
                continue;
            }

            if ($permissionName === 'any_admin_permission' && $subject === null)
            {
                $vote = self::ACCESS_DENIED;

                if (!empty($user->getRole()?->getPermissions()))
                {
                    return self::ACCESS_GRANTED;
                }
            }
            else if (array_key_exists($permissionName, $this->permissionNames) && $subject === null)
            {
                $vote = self::ACCESS_DENIED;

                if ($user->hasPermission($permissionName))
                {
                    return self::ACCESS_GRANTED;
                }
            }
            else if (array_key_exists($permissionName, $this->permissionGroupedNames) && ($subject === 'any_admin_permission' || $subject === 'all_admin_permissions'))
            {
                $permissionGroupName = $permissionName;
                $permissionNamesFromGroup = $this->permissionGroupedNames[$permissionGroupName];
                $vote = self::ACCESS_DENIED;

                if ($subject === 'any_admin_permission')
                {
                    foreach ($permissionNamesFromGroup as $permissionNameFromGroup)
                    {
                        if ($user->hasPermission($permissionNameFromGroup))
                        {
                            return self::ACCESS_GRANTED;
                        }
                    }
                }

                if ($subject === 'all_admin_permissions')
                {
                    $hasAllPermissions = true;

                    foreach ($permissionNamesFromGroup as $permissionNameFromGroup)
                    {
                        if (!$user->hasPermission($permissionNameFromGroup))
                        {
                            $hasAllPermissions = false;

                            break;
                        }
                    }

                    if ($hasAllPermissions)
                    {
                        return self::ACCESS_GRANTED;
                    }
                }
            }
        }

        return $vote;
    }

    /**
     * Loads permission names if they haven't been loaded yet.
     *
     * @return void
     */
    private function loadPermissionAndGroupNamesIfNotYetLoaded(): void
    {
        if ($this->permissionNames === null || $this->permissionGroupedNames === null)
        {
            $this->permissionNames = [];
            $this->permissionGroupedNames = [];

            foreach ($this->permissionRepository->findAll() as $permission)
            {
                $permissionName = $permission->getName();
                $this->permissionNames[$permissionName] = $permissionName;

                $permissionGroup = $permission->getPermissionGroup();
                $permissionGroupName = $permissionGroup->getName();
                $this->permissionGroupedNames[$permissionGroupName][] = $permissionName;
            }
        }
    }
}