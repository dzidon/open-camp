<?php

namespace App\Service\Command;

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
        $groups['user'] = new PermissionGroup('user', 'permission_group.user', 100);
        $groups['role'] = new PermissionGroup('role', 'permission_group.role', 200);
        $groups['camp_category'] = new PermissionGroup('camp_category', 'permission_group.camp_category', 300);
        $groups['camp'] = new PermissionGroup('camp', 'permission_group.camp', 400);
        $groups['trip_location_path'] = new PermissionGroup('trip_location_path', 'permission_group.trip_location_path', 500);
        $groups['attachment_config'] = new PermissionGroup('attachment_config', 'permission_group.attachment_config', 600);
        $groups['purchasable_item'] = new PermissionGroup('purchasable_item', 'permission_group.purchasable_item', 700);

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
        $permissions['user_create'] = new Permission('user_create', 'permission.user_create', 100, $groups['user']);
        $permissions['user_read'] = new Permission('user_read', 'permission.user_read', 200, $groups['user']);
        $permissions['user_update'] = new Permission('user_update', 'permission.user_update', 300, $groups['user']);
        $permissions['user_update_role'] = new Permission('user_update_role', 'permission.user_update_role', 400, $groups['user']);
        $permissions['user_delete'] = new Permission('user_delete', 'permission.user_delete', 500, $groups['user']);

        $permissions['role_create'] = new Permission('role_create', 'permission.role_create', 100, $groups['role']);
        $permissions['role_read'] = new Permission('role_read', 'permission.role_read', 200, $groups['role']);
        $permissions['role_update'] = new Permission('role_update', 'permission.role_update', 300, $groups['role']);
        $permissions['role_delete'] = new Permission('role_delete', 'permission.role_delete', 400, $groups['role']);

        $permissions['camp_category_create'] = new Permission('camp_category_create', 'permission.camp_category_create', 100, $groups['camp_category']);
        $permissions['camp_category_read'] = new Permission('camp_category_read', 'permission.camp_category_read', 200, $groups['camp_category']);
        $permissions['camp_category_update'] = new Permission('camp_category_update', 'permission.camp_category_update', 300, $groups['camp_category']);
        $permissions['camp_category_delete'] = new Permission('camp_category_delete', 'permission.camp_category_delete', 400, $groups['camp_category']);

        $permissions['camp_create'] = new Permission('camp_create', 'permission.camp_create', 100, $groups['camp']);
        $permissions['camp_read'] = new Permission('camp_read', 'permission.camp_read', 200, $groups['camp']);
        $permissions['camp_update'] = new Permission('camp_update', 'permission.camp_update', 300, $groups['camp']);
        $permissions['camp_delete'] = new Permission('camp_delete', 'permission.camp_delete', 400, $groups['camp']);

        $permissions['trip_location_path_create'] = new Permission('trip_location_path_create', 'permission.trip_location_path_create', 100, $groups['trip_location_path']);
        $permissions['trip_location_path_read'] = new Permission('trip_location_path_read', 'permission.trip_location_path_read', 200, $groups['trip_location_path']);
        $permissions['trip_location_path_update'] = new Permission('trip_location_path_update', 'permission.trip_location_path_update', 300, $groups['trip_location_path']);
        $permissions['trip_location_path_delete'] = new Permission('trip_location_path_delete', 'permission.trip_location_path_delete', 400, $groups['trip_location_path']);

        $permissions['attachment_config_create'] = new Permission('attachment_config_create', 'permission.attachment_config_create', 100, $groups['attachment_config']);
        $permissions['attachment_config_read'] = new Permission('attachment_config_read', 'permission.attachment_config_read', 200, $groups['attachment_config']);
        $permissions['attachment_config_update'] = new Permission('attachment_config_update', 'permission.attachment_config_update', 300, $groups['attachment_config']);
        $permissions['attachment_config_delete'] = new Permission('attachment_config_delete', 'permission.attachment_config_delete', 400, $groups['attachment_config']);

        $permissions['purchasable_item_create'] = new Permission('purchasable_item_create', 'permission.purchasable_item_create', 100, $groups['purchasable_item']);
        $permissions['purchasable_item_read'] = new Permission('purchasable_item_read', 'permission.purchasable_item_read', 200, $groups['purchasable_item']);
        $permissions['purchasable_item_update'] = new Permission('purchasable_item_update', 'permission.purchasable_item_update', 300, $groups['purchasable_item']);
        $permissions['purchasable_item_delete'] = new Permission('purchasable_item_delete', 'permission.purchasable_item_delete', 400, $groups['purchasable_item']);

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