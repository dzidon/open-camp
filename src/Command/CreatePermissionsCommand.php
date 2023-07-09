<?php

namespace App\Command;

use App\Model\Entity\Permission;
use App\Model\Entity\PermissionGroup;
use App\Model\Repository\PermissionGroupRepositoryInterface;
use App\Model\Repository\PermissionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Creates permissions and permission groups.
 */
#[AsCommand(name: 'app:create-permissions')]
class CreatePermissionsCommand extends Command
{
    private PermissionRepositoryInterface $permissionRepository;
    private PermissionGroupRepositoryInterface $permissionGroupRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(PermissionRepositoryInterface      $permissionRepository,
                                PermissionGroupRepositoryInterface $permissionGroupRepository,
                                EntityManagerInterface             $entityManager)
    {
        $this->permissionRepository = $permissionRepository;
        $this->permissionGroupRepository = $permissionGroupRepository;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // load existing
        $existingPermissionGroups = [];
        foreach ($this->permissionGroupRepository->findAll() as $existingPermissionGroup)
        {
            $existingPermissionGroups[$existingPermissionGroup->getName()] = $existingPermissionGroup;
        }

        $existingPermissions = [];
        foreach ($this->permissionRepository->findAll() as $existingPermission)
        {
            $existingPermissions[$existingPermission->getName()] = $existingPermission;
        }

        // groups
        $createdGroups = [];
        $groups = $this->createPermissionGroups();

        foreach ($groups as $name => $group)
        {
            if (array_key_exists($name, $existingPermissionGroups))
            {
                $groups[$name] = $existingPermissionGroups[$name];
            }
            else
            {
                $createdGroups[] = $name;
                $this->permissionGroupRepository->savePermissionGroup($group, false);
            }
        }

        // permissions
        $createdPermissions = [];
        $permissions = $this->createPermissions($groups);

        foreach ($permissions as $name => $permission)
        {
            if (!array_key_exists($name, $existingPermissions))
            {
                $createdPermissions[] = $name;
                $this->permissionRepository->savePermission($permission, false);
            }
        }

        $this->entityManager->flush();

        // output message - groups
        if (empty($createdGroups))
        {
            $output->writeln('No new groups created.');
        }
        else
        {
            $output->writeln(sprintf('Groups created: %s', implode(', ', $createdGroups)));
        }

        // output message - permissions
        if (empty($createdPermissions))
        {
            $output->writeln('No new permissions created.');
        }
        else
        {
            $output->writeln(sprintf('Permissions created: %s', implode(', ', $createdPermissions)));
        }

        return Command::SUCCESS;
    }

    /**
     * Create permission groups.
     *
     * @return PermissionGroup[]
     */
    private function createPermissionGroups(): array
    {
        $groups['user'] = $this->permissionGroupRepository->createPermissionGroup('user', 'permission_group.user', 100);
        $groups['role'] = $this->permissionGroupRepository->createPermissionGroup('role', 'permission_group.role', 200);

        return $groups;
    }

    /**
     * Creates permissions.
     *
     * @param PermissionGroup[] $groups
     * @return Permission[]
     */
    private function createPermissions(array $groups): array
    {
        $permissions['user_create'] = $this->permissionRepository->createPermission('user_create', 'permission.user_create', 100, $groups['user']);
        $permissions['user_read'] = $this->permissionRepository->createPermission('user_read', 'permission.user_read', 200, $groups['user']);
        $permissions['user_update'] = $this->permissionRepository->createPermission('user_update', 'permission.user_update', 300, $groups['user']);
        $permissions['user_update_role'] = $this->permissionRepository->createPermission('user_update_role', 'permission.user_update_role', 400, $groups['user']);
        $permissions['user_delete'] = $this->permissionRepository->createPermission('user_delete', 'permission.user_delete', 500, $groups['user']);

        $permissions['role_create'] = $this->permissionRepository->createPermission('role_create', 'permission.role_create', 100, $groups['role']);
        $permissions['role_read'] = $this->permissionRepository->createPermission('role_read', 'permission.role_read', 200, $groups['role']);
        $permissions['role_update'] = $this->permissionRepository->createPermission('role_update', 'permission.role_update', 300, $groups['role']);
        $permissions['role_delete'] = $this->permissionRepository->createPermission('role_delete', 'permission.role_delete', 400, $groups['role']);

        return $permissions;
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this
            ->setHelp('Creates permissions and permission groups used in the application.')
        ;
    }
}