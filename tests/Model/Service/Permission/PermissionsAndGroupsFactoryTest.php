<?php

namespace App\Tests\Model\Service\Permission;

use App\Model\Repository\PermissionGroupRepositoryInterface;
use App\Model\Repository\PermissionRepositoryInterface;
use App\Model\Service\Permission\PermissionsAndGroupsFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PermissionsAndGroupsFactoryTest extends KernelTestCase
{
    private PermissionRepositoryInterface $permissionRepository;

    private PermissionGroupRepositoryInterface $permissionGroupRepository;

    private PermissionsAndGroupsFactory $factory;

    public function testCreatePermissionsAndGroups(): void
    {
        $result = $this->factory->createPermissionsAndGroups();
        $groups = $result->getCreatedPermissionGroups();
        $permissions = $result->getCreatedPermissions();

        $groupsSerialized = [];
        $permissionsSerialized = [];

        foreach ($groups as $group)
        {
            $groupsSerialized[] = [
                'name'     => $group->getName(),
                'priority' => $group->getPriority(),
                'label'    => $group->getLabel(),
            ];
        }

        foreach ($permissions as $permission)
        {
            $permissionsSerialized[] = [
                'name'             => $permission->getName(),
                'priority'         => $permission->getPriority(),
                'label'            => $permission->getLabel(),
                'permission_group' => $permission->getPermissionGroup()->getName(),
            ];
        }

        /*
         * Groups
         */
        $this->assertCount(11, $groupsSerialized);

        $this->assertContains([
            'name'     => 'application',
            'priority' => 1100,
            'label'    => 'permission_group.application',
        ], $groupsSerialized);

        $this->assertContains([
            'name'     => 'application_payment',
            'priority' => 1000,
            'label'    => 'permission_group.application_payment',
        ], $groupsSerialized);

        $this->assertContains([
            'name'     => 'user',
            'priority' => 900,
            'label'    => 'permission_group.user',
        ], $groupsSerialized);

        $this->assertContains([
            'name'     => 'role',
            'priority' => 800,
            'label'    => 'permission_group.role',
        ], $groupsSerialized);

        $this->assertContains([
            'name'     => 'camp_category',
            'priority' => 700,
            'label'    => 'permission_group.camp_category',
        ], $groupsSerialized);

        $this->assertContains([
            'name'     => 'camp',
            'priority' => 600,
            'label'    => 'permission_group.camp',
        ], $groupsSerialized);

        $this->assertContains([
            'name'     => 'trip_location_path',
            'priority' => 500,
            'label'    => 'permission_group.trip_location_path',
        ], $groupsSerialized);

        $this->assertContains([
            'name'     => 'attachment_config',
            'priority' => 400,
            'label'    => 'permission_group.attachment_config',
        ], $groupsSerialized);

        $this->assertContains([
            'name'     => 'purchasable_item',
            'priority' => 300,
            'label'    => 'permission_group.purchasable_item',
        ], $groupsSerialized);

        $this->assertContains([
            'name'     => 'form_field',
            'priority' => 200,
            'label'    => 'permission_group.form_field',
        ], $groupsSerialized);

        $this->assertContains([
            'name'     => 'discount_config',
            'priority' => 100,
            'label'    => 'permission_group.discount_config',
        ], $groupsSerialized);

        /*
         * Permissions
         */
        $this->assertCount(46, $permissionsSerialized);

        // application
        $this->assertContains([
            'name'             => 'application_summary_read',
            'priority'         => 500,
            'label'            => 'permission.application_summary_read',
            'permission_group' => 'application',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'application_read',
            'priority'         => 400,
            'label'            => 'permission.application_read',
            'permission_group' => 'application',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'application_state_update',
            'priority'         => 300,
            'label'            => 'permission.application_state_update',
            'permission_group' => 'application',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'application_update',
            'priority'         => 200,
            'label'            => 'permission.application_update',
            'permission_group' => 'application',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'application_delete',
            'priority'         => 100,
            'label'            => 'permission.application_delete',
            'permission_group' => 'application',
        ], $permissionsSerialized);

        // application payment
        $this->assertContains([
            'name'             => 'application_payment_create',
            'priority'         => 400,
            'label'            => 'permission.application_payment_create',
            'permission_group' => 'application_payment',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'application_payment_read',
            'priority'         => 300,
            'label'            => 'permission.application_payment_read',
            'permission_group' => 'application_payment',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'application_payment_update',
            'priority'         => 200,
            'label'            => 'permission.application_payment_update',
            'permission_group' => 'application_payment',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'application_payment_delete',
            'priority'         => 100,
            'label'            => 'permission.application_payment_delete',
            'permission_group' => 'application_payment',
        ], $permissionsSerialized);

        // user
        $this->assertContains([
            'name'             => 'user_create',
            'priority'         => 500,
            'label'            => 'permission.user_create',
            'permission_group' => 'user',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'user_read',
            'priority'         => 400,
            'label'            => 'permission.user_read',
            'permission_group' => 'user',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'user_update',
            'priority'         => 300,
            'label'            => 'permission.user_update',
            'permission_group' => 'user',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'user_role_update',
            'priority'         => 200,
            'label'            => 'permission.user_role_update',
            'permission_group' => 'user',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'user_delete',
            'priority'         => 100,
            'label'            => 'permission.user_delete',
            'permission_group' => 'user',
        ], $permissionsSerialized);
        
        // role
        $this->assertContains([
            'name'             => 'role_create',
            'priority'         => 400,
            'label'            => 'permission.role_create',
            'permission_group' => 'role',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'role_read',
            'priority'         => 300,
            'label'            => 'permission.role_read',
            'permission_group' => 'role',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'role_update',
            'priority'         => 200,
            'label'            => 'permission.role_update',
            'permission_group' => 'role',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'role_delete',
            'priority'         => 100,
            'label'            => 'permission.role_delete',
            'permission_group' => 'role',
        ], $permissionsSerialized);

        // camp category
        $this->assertContains([
            'name'             => 'camp_category_create',
            'priority'         => 400,
            'label'            => 'permission.camp_category_create',
            'permission_group' => 'camp_category',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'camp_category_read',
            'priority'         => 300,
            'label'            => 'permission.camp_category_read',
            'permission_group' => 'camp_category',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'camp_category_update',
            'priority'         => 200,
            'label'            => 'permission.camp_category_update',
            'permission_group' => 'camp_category',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'camp_category_delete',
            'priority'         => 100,
            'label'            => 'permission.camp_category_delete',
            'permission_group' => 'camp_category',
        ], $permissionsSerialized);

        // camp
        $this->assertContains([
            'name'             => 'camp_create',
            'priority'         => 400,
            'label'            => 'permission.camp_create',
            'permission_group' => 'camp',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'camp_read',
            'priority'         => 300,
            'label'            => 'permission.camp_read',
            'permission_group' => 'camp',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'camp_update',
            'priority'         => 200,
            'label'            => 'permission.camp_update',
            'permission_group' => 'camp',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'camp_delete',
            'priority'         => 100,
            'label'            => 'permission.camp_delete',
            'permission_group' => 'camp',
        ], $permissionsSerialized);

        // trip location path
        $this->assertContains([
            'name'             => 'trip_location_path_create',
            'priority'         => 400,
            'label'            => 'permission.trip_location_path_create',
            'permission_group' => 'trip_location_path',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'trip_location_path_read',
            'priority'         => 300,
            'label'            => 'permission.trip_location_path_read',
            'permission_group' => 'trip_location_path',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'trip_location_path_update',
            'priority'         => 200,
            'label'            => 'permission.trip_location_path_update',
            'permission_group' => 'trip_location_path',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'trip_location_path_delete',
            'priority'         => 100,
            'label'            => 'permission.trip_location_path_delete',
            'permission_group' => 'trip_location_path',
        ], $permissionsSerialized);

        // attachment config
        $this->assertContains([
            'name'             => 'attachment_config_create',
            'priority'         => 400,
            'label'            => 'permission.attachment_config_create',
            'permission_group' => 'attachment_config',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'attachment_config_read',
            'priority'         => 300,
            'label'            => 'permission.attachment_config_read',
            'permission_group' => 'attachment_config',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'attachment_config_update',
            'priority'         => 200,
            'label'            => 'permission.attachment_config_update',
            'permission_group' => 'attachment_config',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'attachment_config_delete',
            'priority'         => 100,
            'label'            => 'permission.attachment_config_delete',
            'permission_group' => 'attachment_config',
        ], $permissionsSerialized);

        // purchasable item
        $this->assertContains([
            'name'             => 'purchasable_item_create',
            'priority'         => 400,
            'label'            => 'permission.purchasable_item_create',
            'permission_group' => 'purchasable_item',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'purchasable_item_read',
            'priority'         => 300,
            'label'            => 'permission.purchasable_item_read',
            'permission_group' => 'purchasable_item',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'purchasable_item_update',
            'priority'         => 200,
            'label'            => 'permission.purchasable_item_update',
            'permission_group' => 'purchasable_item',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'purchasable_item_delete',
            'priority'         => 100,
            'label'            => 'permission.purchasable_item_delete',
            'permission_group' => 'purchasable_item',
        ], $permissionsSerialized);

        // form field
        $this->assertContains([
            'name'             => 'form_field_create',
            'priority'         => 400,
            'label'            => 'permission.form_field_create',
            'permission_group' => 'form_field',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'form_field_read',
            'priority'         => 300,
            'label'            => 'permission.form_field_read',
            'permission_group' => 'form_field',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'form_field_update',
            'priority'         => 200,
            'label'            => 'permission.form_field_update',
            'permission_group' => 'form_field',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'form_field_delete',
            'priority'         => 100,
            'label'            => 'permission.form_field_delete',
            'permission_group' => 'form_field',
        ], $permissionsSerialized);

        // discount config
        $this->assertContains([
            'name'             => 'discount_config_create',
            'priority'         => 400,
            'label'            => 'permission.discount_config_create',
            'permission_group' => 'discount_config',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'discount_config_read',
            'priority'         => 300,
            'label'            => 'permission.discount_config_read',
            'permission_group' => 'discount_config',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'discount_config_update',
            'priority'         => 200,
            'label'            => 'permission.discount_config_update',
            'permission_group' => 'discount_config',
        ], $permissionsSerialized);

        $this->assertContains([
            'name'             => 'discount_config_delete',
            'priority'         => 100,
            'label'            => 'permission.discount_config_delete',
            'permission_group' => 'discount_config',
        ], $permissionsSerialized);

        /*
         * Save and call the method again
         */
        foreach ($groups as $group)
        {
            $this->permissionGroupRepository->savePermissionGroup($group, false);
        }

        foreach ($permissions as $key => $permission)
        {
            $this->permissionRepository->savePermission($permission, $key === array_key_last($permissions));
        }

        $result = $this->factory->createPermissionsAndGroups();
        $groups = $result->getCreatedPermissionGroups();
        $permissions = $result->getCreatedPermissions();

        $this->assertEmpty($groups);
        $this->assertEmpty($permissions);
    }

    protected function setUp(): void
    {
        $container = static::getContainer();

        /** @var PermissionsAndGroupsFactory $factory */
        $factory = $container->get(PermissionsAndGroupsFactory::class);
        $this->factory = $factory;

        /** @var PermissionRepositoryInterface $permissionRepository */
        $permissionRepository = $container->get(PermissionRepositoryInterface::class);
        $this->permissionRepository = $permissionRepository;

        /** @var PermissionGroupRepositoryInterface $permissionGroupRepository */
        $permissionGroupRepository = $container->get(PermissionGroupRepositoryInterface::class);
        $this->permissionGroupRepository = $permissionGroupRepository;
    }
}