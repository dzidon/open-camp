<?php

namespace App\Model\Service\Permission;

use App\Model\Entity\Permission;
use App\Model\Entity\PermissionGroup;
use App\Model\Library\Permission\PermissionsAndGroupsCreationResult;
use App\Model\Repository\PermissionGroupRepositoryInterface;
use App\Model\Repository\PermissionRepositoryInterface;

/**
 * @inheritDoc
 */
class PermissionsAndGroupsFactory implements PermissionsAndGroupsFactoryInterface
{
    private PermissionRepositoryInterface $permissionRepository;
    private PermissionGroupRepositoryInterface $permissionGroupRepository;

    public function __construct(PermissionRepositoryInterface      $permissionRepository,
                                PermissionGroupRepositoryInterface $permissionGroupRepository)
    {
        $this->permissionRepository = $permissionRepository;
        $this->permissionGroupRepository = $permissionGroupRepository;
    }

    /**
     * @inheritDoc
     */
    public function createPermissionsAndGroups(): PermissionsAndGroupsCreationResult
    {
        // load existing
        $existingPermissionGroups = [];
        $existingPermissions = [];

        foreach ($this->permissionGroupRepository->findAll() as $existingPermissionGroup)
        {
            $existingPermissionGroups[$existingPermissionGroup->getName()] = $existingPermissionGroup;
        }

        foreach ($this->permissionRepository->findAll() as $existingPermission)
        {
            $existingPermissions[$existingPermission->getName()] = $existingPermission;
        }

        $createdPermissionGroups = [];
        $createdPermissions = [];

        // groups
        $groups = $this->createPermissionGroups();

        foreach ($groups as $name => $group)
        {
            if (array_key_exists($name, $existingPermissionGroups))
            {
                $groups[$name] = $existingPermissionGroups[$name];
            }
            else
            {
                $createdPermissionGroups[] = $group;
            }
        }

        // permissions
        $permissions = $this->createPermissions($groups);

        foreach ($permissions as $name => $permission)
        {
            if (!array_key_exists($name, $existingPermissions))
            {
                $createdPermissions[] = $permission;
            }
        }

        return new PermissionsAndGroupsCreationResult($createdPermissions, $createdPermissionGroups);
    }

    /**
     * Create permission groups.
     *
     * @return PermissionGroup[]
     */
    private function createPermissionGroups(): array
    {
        $groups['blog_post'] = new PermissionGroup('blog_post', 'permission_group.blog_post', 1200);
        $groups['application'] = new PermissionGroup('application', 'permission_group.application', 1100);
        $groups['application_payment'] = new PermissionGroup('application_payment', 'permission_group.application_payment', 1000);
        $groups['user'] = new PermissionGroup('user', 'permission_group.user', 900);
        $groups['role'] = new PermissionGroup('role', 'permission_group.role', 800);
        $groups['camp_category'] = new PermissionGroup('camp_category', 'permission_group.camp_category', 700);
        $groups['camp'] = new PermissionGroup('camp', 'permission_group.camp', 600);
        $groups['trip_location_path'] = new PermissionGroup('trip_location_path', 'permission_group.trip_location_path', 500);
        $groups['attachment_config'] = new PermissionGroup('attachment_config', 'permission_group.attachment_config', 400);
        $groups['purchasable_item'] = new PermissionGroup('purchasable_item', 'permission_group.purchasable_item', 300);
        $groups['form_field'] = new PermissionGroup('form_field', 'permission_group.form_field', 200);
        $groups['discount_config'] = new PermissionGroup('discount_config', 'permission_group.discount_config', 100);

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
        $permissions['blog_post_create'] = new Permission('blog_post_create', 'permission.blog_post_create', 400, $groups['blog_post']);
        $permissions['blog_post_read'] = new Permission('blog_post_read', 'permission.blog_post_read', 300, $groups['blog_post']);
        $permissions['blog_post_update'] = new Permission('blog_post_update', 'permission.blog_post_update', 200, $groups['blog_post']);
        $permissions['blog_post_delete'] = new Permission('blog_post_delete', 'permission.blog_post_delete', 100, $groups['blog_post']);

        $permissions['application_read'] = new Permission('application_read', 'permission.application_read', 400, $groups['application']);
        $permissions['application_state_update'] = new Permission('application_state_update', 'permission.application_state_update', 300, $groups['application']);
        $permissions['application_update'] = new Permission('application_update', 'permission.application_update', 200, $groups['application']);
        $permissions['application_delete'] = new Permission('application_delete', 'permission.application_delete', 100, $groups['application']);

        $permissions['application_payment_refund'] = new Permission('application_payment_refund', 'permission.application_payment_refund', 500, $groups['application_payment']);
        $permissions['application_payment_create'] = new Permission('application_payment_create', 'permission.application_payment_create', 400, $groups['application_payment']);
        $permissions['application_payment_read'] = new Permission('application_payment_read', 'permission.application_payment_read', 300, $groups['application_payment']);
        $permissions['application_payment_update'] = new Permission('application_payment_update', 'permission.application_payment_update', 200, $groups['application_payment']);
        $permissions['application_payment_delete'] = new Permission('application_payment_delete', 'permission.application_payment_delete', 100, $groups['application_payment']);
        
        $permissions['user_create'] = new Permission('user_create', 'permission.user_create', 500, $groups['user']);
        $permissions['user_read'] = new Permission('user_read', 'permission.user_read', 400, $groups['user']);
        $permissions['user_update'] = new Permission('user_update', 'permission.user_update', 300, $groups['user']);
        $permissions['user_role_update'] = new Permission('user_role_update', 'permission.user_role_update', 200, $groups['user']);
        $permissions['user_delete'] = new Permission('user_delete', 'permission.user_delete', 100, $groups['user']);

        $permissions['role_create'] = new Permission('role_create', 'permission.role_create', 400, $groups['role']);
        $permissions['role_read'] = new Permission('role_read', 'permission.role_read', 300, $groups['role']);
        $permissions['role_update'] = new Permission('role_update', 'permission.role_update', 200, $groups['role']);
        $permissions['role_delete'] = new Permission('role_delete', 'permission.role_delete', 100, $groups['role']);

        $permissions['camp_category_create'] = new Permission('camp_category_create', 'permission.camp_category_create', 400, $groups['camp_category']);
        $permissions['camp_category_read'] = new Permission('camp_category_read', 'permission.camp_category_read', 300, $groups['camp_category']);
        $permissions['camp_category_update'] = new Permission('camp_category_update', 'permission.camp_category_update', 200, $groups['camp_category']);
        $permissions['camp_category_delete'] = new Permission('camp_category_delete', 'permission.camp_category_delete', 100, $groups['camp_category']);

        $permissions['camp_create'] = new Permission('camp_create', 'permission.camp_create', 400, $groups['camp']);
        $permissions['camp_read'] = new Permission('camp_read', 'permission.camp_read', 300, $groups['camp']);
        $permissions['camp_update'] = new Permission('camp_update', 'permission.camp_update', 200, $groups['camp']);
        $permissions['camp_delete'] = new Permission('camp_delete', 'permission.camp_delete', 100, $groups['camp']);

        $permissions['trip_location_path_create'] = new Permission('trip_location_path_create', 'permission.trip_location_path_create', 400, $groups['trip_location_path']);
        $permissions['trip_location_path_read'] = new Permission('trip_location_path_read', 'permission.trip_location_path_read', 300, $groups['trip_location_path']);
        $permissions['trip_location_path_update'] = new Permission('trip_location_path_update', 'permission.trip_location_path_update', 200, $groups['trip_location_path']);
        $permissions['trip_location_path_delete'] = new Permission('trip_location_path_delete', 'permission.trip_location_path_delete', 100, $groups['trip_location_path']);

        $permissions['attachment_config_create'] = new Permission('attachment_config_create', 'permission.attachment_config_create', 400, $groups['attachment_config']);
        $permissions['attachment_config_read'] = new Permission('attachment_config_read', 'permission.attachment_config_read', 300, $groups['attachment_config']);
        $permissions['attachment_config_update'] = new Permission('attachment_config_update', 'permission.attachment_config_update', 200, $groups['attachment_config']);
        $permissions['attachment_config_delete'] = new Permission('attachment_config_delete', 'permission.attachment_config_delete', 100, $groups['attachment_config']);

        $permissions['purchasable_item_create'] = new Permission('purchasable_item_create', 'permission.purchasable_item_create', 400, $groups['purchasable_item']);
        $permissions['purchasable_item_read'] = new Permission('purchasable_item_read', 'permission.purchasable_item_read', 300, $groups['purchasable_item']);
        $permissions['purchasable_item_update'] = new Permission('purchasable_item_update', 'permission.purchasable_item_update', 200, $groups['purchasable_item']);
        $permissions['purchasable_item_delete'] = new Permission('purchasable_item_delete', 'permission.purchasable_item_delete', 100, $groups['purchasable_item']);

        $permissions['form_field_create'] = new Permission('form_field_create', 'permission.form_field_create', 400, $groups['form_field']);
        $permissions['form_field_read'] = new Permission('form_field_read', 'permission.form_field_read', 300, $groups['form_field']);
        $permissions['form_field_update'] = new Permission('form_field_update', 'permission.form_field_update', 200, $groups['form_field']);
        $permissions['form_field_delete'] = new Permission('form_field_delete', 'permission.form_field_delete', 100, $groups['form_field']);

        $permissions['discount_config_create'] = new Permission('discount_config_create', 'permission.discount_config_create', 400, $groups['discount_config']);
        $permissions['discount_config_read'] = new Permission('discount_config_read', 'permission.discount_config_read', 300, $groups['discount_config']);
        $permissions['discount_config_update'] = new Permission('discount_config_update', 'permission.discount_config_update', 200, $groups['discount_config']);
        $permissions['discount_config_delete'] = new Permission('discount_config_delete', 'permission.discount_config_delete', 100, $groups['discount_config']);

        return $permissions;
    }
}