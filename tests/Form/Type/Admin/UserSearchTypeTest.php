<?php

namespace App\Tests\Form\Type\Admin;

use App\Entity\Role;
use App\Form\DataTransfer\Data\Admin\UserSearchData;
use App\Form\Type\Admin\UserSearchType;
use App\Repository\RoleRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\ChoiceList\Factory\Cache\ChoiceLoader;
use Symfony\Component\Form\FormFactoryInterface;

class UserSearchTypeTest extends KernelTestCase
{
    public function testChoicesRolesDefault(): void
    {
        $factory = $this->getFormFactory();

        $data = new UserSearchData();
        $form = $factory->create(UserSearchType::class, $data);

        /** @var ChoiceLoader $choiceLoader */
        $choiceLoader = $form
            ->get('role')
            ->getConfig()
            ->getOption('choice_loader')
        ;

        /** @var Role[] $choices */
        $choices = $choiceLoader
            ->loadChoiceList()
            ->getChoices()
        ;

        $roleLabels = $this->getRoleNames($choices);

        $this->assertSame(['Super admin', 'Admin'], $roleLabels);
    }

    public function testChoicesRolesNull(): void
    {
        $factory = $this->getFormFactory();

        $data = new UserSearchData();
        $form = $factory->create(UserSearchType::class, $data, [
            'choices_roles' => null,
        ]);

        /** @var ChoiceLoader $choiceLoader */
        $choiceLoader = $form
            ->get('role')
            ->getConfig()
            ->getOption('choice_loader')
        ;

        /** @var Role[] $choices */
        $choices = $choiceLoader
            ->loadChoiceList()
            ->getChoices()
        ;

        $roleLabels = $this->getRoleNames($choices);

        $this->assertSame(['Super admin', 'Admin'], $roleLabels);
    }

    public function testChoicesRolesPassed(): void
    {
        $factory = $this->getFormFactory();
        $roleRepository = $this->getRoleRepository();

        $passedRoles = [];
        $roles = $roleRepository->findAll();
        foreach ($roles as $role)
        {
            if ($role->getLabel() === 'Admin')
            {
                $passedRoles[] = $role;
            }
        }

        $data = new UserSearchData();
        $form = $factory->create(UserSearchType::class, $data, [
            'choices_roles' => $passedRoles,
        ]);

        /** @var Role[] $choices */
        $choices = $form
            ->get('role')
            ->getConfig()
            ->getOption('choices')
        ;

        $roleLabels = $this->getRoleNames($choices);

        $this->assertSame(['Admin'], $roleLabels);
    }

    private function getRoleNames(array $roles): array
    {
        $roleNames = [];

        /** @var Role $role */
        foreach ($roles as $role)
        {
            $roleNames[] = $role->getLabel();
        }

        return $roleNames;
    }

    private function getRoleRepository(): RoleRepository
    {
        $container = static::getContainer();

        /** @var RoleRepository $repository */
        $repository = $container->get(RoleRepository::class);

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