<?php

namespace App\Model\EventSubscriber\Admin\Role;

use App\Model\Event\Admin\Role\SuperAdminRoleAssignEvent;
use App\Model\Repository\UserRepositoryInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class SuperAdminRoleAssignSubscriber
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[AsEventListener(event: SuperAdminRoleAssignEvent::NAME, priority: 200)]
    public function onAssignUpdateUser(SuperAdminRoleAssignEvent $event): void
    {
        $user = $event->getUser();
        $role = $event->getRole();

        $user->setRole($role);
    }

    #[AsEventListener(event: SuperAdminRoleAssignEvent::NAME, priority: 100)]
    public function onAssignSaveUser(SuperAdminRoleAssignEvent $event): void
    {
        $user = $event->getUser();

        $this->userRepository->saveUser($user, true);
    }
}