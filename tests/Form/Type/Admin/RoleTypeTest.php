<?php

namespace App\Tests\Form\Type\Admin;

use App\Entity\Permission;
use App\Form\DataTransfer\Data\Admin\RoleData;
use App\Form\Type\Admin\RoleType;
use App\Repository\PermissionRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\ChoiceList\Factory\Cache\ChoiceLoader;
use Symfony\Component\Form\FormFactoryInterface;

class RoleTypeTest extends KernelTestCase
{
    public function testChoicesPermissionsDefault(): void
    {
        $factory = $this->getFormFactory();

        $data = new RoleData();
        $form = $factory->create(RoleType::class, $data);

        /** @var ChoiceLoader $choiceLoader */
        $choiceLoader = $form
            ->get('permissions')
            ->getConfig()
            ->getOption('choice_loader')
        ;

        /** @var Permission[] $choices */
        $choices = $choiceLoader
            ->loadChoiceList()
            ->getChoices()
        ;

        $permissionNames = $this->getPermissionNames($choices);

        $this->assertSame(['permission1', 'permission2', 'permission3', 'permission4'], $permissionNames);
    }

    public function testChoicesPermissionsNull(): void
    {
        $factory = $this->getFormFactory();

        $data = new RoleData();
        $form = $factory->create(RoleType::class, $data, [
            'choices_permissions' => null,
        ]);

        /** @var ChoiceLoader $choiceLoader */
        $choiceLoader = $form
            ->get('permissions')
            ->getConfig()
            ->getOption('choice_loader')
        ;

        /** @var Permission[] $choices */
        $choices = $choiceLoader
            ->loadChoiceList()
            ->getChoices()
        ;

        $permissionNames = $this->getPermissionNames($choices);

        $this->assertSame(['permission1', 'permission2', 'permission3', 'permission4'], $permissionNames);
    }

    public function testChoicesPermissionsPassed(): void
    {
        $factory = $this->getFormFactory();
        $permissionRepository = $this->getPermissionRepository();

        $passedPermissions = [];
        $permissions = $permissionRepository->findAll();
        foreach ($permissions as $permission)
        {
            if ($permission->getName() === 'permission1' || $permission->getName() === 'permission4')
            {
                $passedPermissions[] = $permission;
            }
        }

        $data = new RoleData();
        $form = $factory->create(RoleType::class, $data, [
            'choices_permissions' => $passedPermissions,
        ]);

        /** @var Permission[] $choices */
        $choices = $form
            ->get('permissions')
            ->getConfig()
            ->getOption('choices')
        ;

        $permissionNames = $this->getPermissionNames($choices);

        $this->assertSame(['permission1', 'permission4'], $permissionNames);
    }

    private function getPermissionNames(array $permissions): array
    {
        $permissionNames = [];

        /** @var Permission $permission */
        foreach ($permissions as $permission)
        {
            $permissionNames[] = $permission->getName();
        }

        return $permissionNames;
    }

    private function getPermissionRepository(): PermissionRepository
    {
        $container = static::getContainer();

        /** @var PermissionRepository $repository */
        $repository = $container->get(PermissionRepository::class);

        return $repository;
    }

    private function getFormFactory(): FormFactoryInterface
    {
        $container = static::getContainer();

        /** @var FormFactoryInterface $factory */
        $factory = $container->get(FormFactoryInterface::class);

        return $factory;
    }
}