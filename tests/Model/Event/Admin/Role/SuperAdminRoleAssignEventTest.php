<?php

namespace App\Tests\Model\Event\Admin\Role;

use App\Model\Entity\Role;
use App\Model\Entity\User;
use App\Model\Event\Admin\Role\SuperAdminRoleAssignEvent;
use PHPUnit\Framework\TestCase;

class SuperAdminRoleAssignEventTest extends TestCase
{
    private Role $role;

    private User $user;

    private SuperAdminRoleAssignEvent $event;

    public function testRole(): void
    {
        $this->assertSame($this->role, $this->event->getRole());

        $newEntity = new Role('Role new');
        $this->event->setRole($newEntity);
        $this->assertSame($newEntity, $this->event->getRole());
    }

    public function testUser(): void
    {
        $this->assertSame($this->user, $this->event->getUser());

        $newUser = new User('bob.new@gmail.com');
        $this->event->setUser($newUser);
        $this->assertSame($newUser, $this->event->getUser());
    }

    public function testIsFlush(): void
    {
        $this->assertTrue($this->event->isFlush());

        $this->event->setIsFlush(false);
        $this->assertFalse($this->event->isFlush());

        $this->event->setIsFlush(true);
        $this->assertTrue($this->event->isFlush());
    }

    protected function setUp(): void
    {
        $this->role = new Role('Role');
        $this->user = new User('bob@gmail.com');
        $this->event = new SuperAdminRoleAssignEvent($this->user, $this->role);
    }
}