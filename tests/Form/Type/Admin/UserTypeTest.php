<?php

namespace App\Tests\Form\Type\Admin;

use App\Entity\Role;
use App\Form\DataTransfer\Data\Admin\UserData;
use App\Form\Type\Admin\UserType;
use App\Repository\RoleRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\ChoiceList\Factory\Cache\ChoiceLoader;
use Symfony\Component\Form\FormFactoryInterface;

class UserTypeTest extends KernelTestCase
{
    public function testIsGrantedUserUpdate(): void
    {
        $this->setSecurityMock(['user_update']);
        $factory = $this->getFormFactory();

        $data = new UserData();
        $form = $factory->create(UserType::class, $data);

        $this->assertTrue($form->has('email'));
        $this->assertFalse($form->has('role'));
    }

    public function testIsGrantedUserUpdateRole(): void
    {
        $this->setSecurityMock(['user_update_role']);
        $factory = $this->getFormFactory();

        $data = new UserData();
        $form = $factory->create(UserType::class, $data);

        $this->assertFalse($form->has('email'));
        $this->assertTrue($form->has('role'));
    }

    public function testIsGrantedBoth(): void
    {
        $this->setSecurityMock(['user_update', 'user_update_role']);
        $factory = $this->getFormFactory();

        $data = new UserData();
        $form = $factory->create(UserType::class, $data);

        $this->assertTrue($form->has('email'));
        $this->assertTrue($form->has('role'));
    }

    public function testChoicesRolesDefault(): void
    {
        $this->setSecurityMock(['user_update_role']);
        $factory = $this->getFormFactory();

        $data = new UserData();
        $form = $factory->create(UserType::class, $data);

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
        $this->setSecurityMock(['user_update_role']);
        $factory = $this->getFormFactory();

        $data = new UserData();
        $form = $factory->create(UserType::class, $data, [
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
        $this->setSecurityMock(['user_update_role']);
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

        $data = new UserData();
        $form = $factory->create(UserType::class, $data, [
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

    private function setSecurityMock(array $userRoles): void
    {
        $container = static::getContainer();

        /** @var Security|MockObject $securityMock */
        $securityMock = $this->getMockBuilder(Security::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $securityMock
            ->expects($this->any())
            ->method('isGranted')
            ->willReturnCallback(function (string $role) use ($userRoles)
            {
                return in_array($role, $userRoles);
            })
        ;

        $container->set(Security::class, $securityMock);
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