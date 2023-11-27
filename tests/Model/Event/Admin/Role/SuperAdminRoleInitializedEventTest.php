<?php

namespace App\Tests\Model\Event\Admin\Role;

use App\Model\Entity\Role;
use App\Model\Event\Admin\Role\SuperAdminRoleInitializedEvent;
use PHPUnit\Framework\TestCase;

class SuperAdminRoleInitializedEventTest extends TestCase
{
    private Role $role;

    private SuperAdminRoleInitializedEvent $event;

    public function testRole(): void
    {
        $this->assertSame($this->role, $this->event->getRole());

        $newEntity = new Role('Role new');
        $this->event->setRole($newEntity);
        $this->assertSame($newEntity, $this->event->getRole());
    }

    protected function setUp(): void
    {
        $this->role = new Role('Role');
        $this->event = new SuperAdminRoleInitializedEvent($this->role);
    }
}